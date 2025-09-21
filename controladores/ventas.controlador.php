<?php

class ControladorVentas{

	/*=============================================
	MOSTRAR VENTAS
	=============================================*/
	static public function ctrMostrarVentas($item, $valor){

		$tabla = "ventas";
		$respuesta = ModeloVentas::mdlMostrarVentas($tabla, $item, $valor);
		return $respuesta;
	}

	/*=============================================
	CREAR VENTA
	=============================================*/
	static public function ctrCrearVenta(){

		if(!isset($_POST["nuevaVenta"])) return;

		/* Validación: debe haber productos */
		if(empty($_POST["listaProductos"])){
			echo'<script>
				swal({
					type: "error",
					title: "La venta no se puede ejecutar sin productos",
					showConfirmButton: true,
					confirmButtonText: "Cerrar"
				}).then(function(result){
					if (result.value) { window.location = "ventas"; }
				});
			</script>';
			return;
		}

		$listaProductos = json_decode($_POST["listaProductos"], true);
		if(!is_array($listaProductos) || !count($listaProductos)){
			echo'<script>
				swal({ type:"error", title:"Lista de productos inválida", showConfirmButton:true, confirmButtonText:"Cerrar" })
				.then(function(){ window.location = "ventas"; });
			</script>';
			return;
		}

		/*=============================================
		ACTUALIZAR STOCK y VENTAS de cada producto
		=============================================*/
		$totalProductosComprados = array();

		foreach ($listaProductos as $value) {

			$idProducto  = (int)$value["id"];
			$cantidad    = (int)$value["cantidad"];

			array_push($totalProductosComprados, $cantidad);

			$tablaProductos = "productos";

			/* Traer producto actual para calcular en servidor */
			$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, "id", $idProducto, "id");
			if(!$traerProducto) continue;

			/* Ventas = ventas actuales + cantidad */
			$ventasActual = (int)$traerProducto["ventas"];
			$nuevasVentas = $ventasActual + $cantidad;
			ModeloProductos::mdlActualizarProducto($tablaProductos, "ventas", $nuevasVentas, "id", $idProducto);

			/* Stock = stock actual - cantidad */
			$stockActual  = (int)$traerProducto["stock"];
			$nuevoStock   = $stockActual - $cantidad;
			if($nuevoStock < 0) $nuevoStock = 0; // seguridad
			ModeloProductos::mdlActualizarProducto($tablaProductos, "stock", $nuevoStock, "id", $idProducto);
		}

		/*=============================================
		ACTUALIZAR CLIENTE (compras y última compra)
		=============================================*/
		$tablaClientes = "clientes";
		$idCliente     = $_POST["seleccionarCliente"];

		$traerCliente  = ModeloClientes::mdlMostrarClientes($tablaClientes, "id", $idCliente);

		$comprasPrevias = $traerCliente ? (int)$traerCliente["compras"] : 0;
		$nuevaSuma = array_sum($totalProductosComprados) + $comprasPrevias;
		ModeloClientes::mdlActualizarCliente($tablaClientes, "compras", $nuevaSuma, $idCliente);

		date_default_timezone_set('America/Lima');
		$valorUltima = date('Y-m-d').' '.date('H:i:s');
		ModeloClientes::mdlActualizarCliente($tablaClientes, "ultima_compra", $valorUltima, $idCliente);

		/*=============================================
		GUARDAR LA VENTA
		=============================================*/
		$tabla = "ventas";
        // Preparar los datos de la venta incluyendo el tipo de comprobante.
        // "tipo_comprobante" proviene del select en el formulario (Boleta, Factura o Ticket).
        $datos = array(
            "id_vendedor"      => $_POST["idVendedor"],
            "id_cliente"       => $_POST["seleccionarCliente"],
            "codigo"           => $_POST["nuevaVenta"],
            "productos"        => $_POST["listaProductos"],
            "impuesto"         => $_POST["nuevoPrecioImpuesto"],
            "neto"             => $_POST["nuevoPrecioNeto"],
            "total"            => $_POST["totalVenta"],
            "metodo_pago"      => $_POST["listaMetodoPago"],
            "tipo_comprobante" => isset($_POST["nuevoTipoComprobante"]) ? $_POST["nuevoTipoComprobante"] : "Boleta"
        );

		$respuesta = ModeloVentas::mdlIngresarVenta($tabla, $datos);

		if($respuesta == "ok"){
			echo'<script>
				localStorage.removeItem("rango");
				swal({
					type: "success",
					title: "La venta ha sido guardada correctamente",
					showConfirmButton: true,
					confirmButtonText: "Imprimir"
				}).then(function(result){
					if (result.value) {
						window.open("extensiones/tcpdf/pdf/boleta.php?codigo='.$_POST["nuevaVenta"].'", "_blank");
						window.location = "crear-venta";
					}
				});
			</script>';
		}
	}

	/*=============================================
	EDITAR VENTA
	=============================================*/
	static public function ctrEditarVenta(){

		if(!isset($_POST["editarVenta"])) return;

		$tabla = "ventas";
		$ventaCodigo = $_POST["editarVenta"];
		$traerVenta = ModeloVentas::mdlMostrarVentas($tabla, "codigo", $ventaCodigo);

		if(!$traerVenta){
			echo'<script>
				swal({ type:"error", title:"Venta no encontrada", showConfirmButton:true, confirmButtonText:"Cerrar" })
				.then(function(){ window.location = "ventas"; });
			</script>';
			return;
		}

		/* ¿Vienen productos editados? */
		if(empty($_POST["listaProductos"])){
			$listaProductos = $traerVenta["productos"];
			$cambioProducto = false;
		}else{
			$listaProductos = $_POST["listaProductos"];
			$cambioProducto = true;
		}

		if($cambioProducto){

			/*=============================================
			REVERTIR impacto de la venta anterior
			=============================================*/
			$productosAntes = json_decode($traerVenta["productos"], true);
			$totalAntes = array();

			foreach ($productosAntes as $value) {

				$idProducto = (int)$value["id"];
				$cant       = (int)$value["cantidad"];

				array_push($totalAntes, $cant);

				$tablaProductos = "productos";
				$prod = ModeloProductos::mdlMostrarProductos($tablaProductos, "id", $idProducto, "id");
				if(!$prod) continue;

				/* Ventas = ventas actuales - cantidad */
				$nuevasVentas = max(0, (int)$prod["ventas"] - $cant);
				ModeloProductos::mdlActualizarProducto($tablaProductos, "ventas", $nuevasVentas, "id", $idProducto);

				/* Stock = stock actual + cantidad */
				$nuevoStock = (int)$prod["stock"] + $cant;
				ModeloProductos::mdlActualizarProducto($tablaProductos, "stock", $nuevoStock, "id", $idProducto);
			}

			/* Restar compras al cliente por la venta anterior */
			$tablaClientes   = "clientes";
			$idClienteActual = $_POST["seleccionarCliente"];
			$cliente = ModeloClientes::mdlMostrarClientes($tablaClientes, "id", $idClienteActual);
			$comprasPrev = $cliente ? (int)$cliente["compras"] : 0;
			$comprasNueva = $comprasPrev - array_sum($totalAntes);
			if($comprasNueva < 0) $comprasNueva = 0;
			ModeloClientes::mdlActualizarCliente($tablaClientes, "compras", $comprasNueva, $idClienteActual);

			/*=============================================
			APLICAR impacto de la nueva lista
			=============================================*/
			$productosDespues = json_decode($listaProductos, true);
			$totalDespues = array();

			foreach ($productosDespues as $value) {

				$idProducto = (int)$value["id"];
				$cant       = (int)$value["cantidad"];

				array_push($totalDespues, $cant);

				$tablaProductos = "productos";
				$prod2 = ModeloProductos::mdlMostrarProductos($tablaProductos, "id", $idProducto, "id");
				if(!$prod2) continue;

				/* Ventas = ventas actuales + cantidad */
				$nuevasVentas = (int)$prod2["ventas"] + $cant;
				ModeloProductos::mdlActualizarProducto($tablaProductos, "ventas", $nuevasVentas, "id", $idProducto);

				/* Stock = stock actual - cantidad */
				$nuevoStock = (int)$prod2["stock"] - $cant;
				if($nuevoStock < 0) $nuevoStock = 0;
				ModeloProductos::mdlActualizarProducto($tablaProductos, "stock", $nuevoStock, "id", $idProducto);
			}

			/* Sumar compras al cliente por la nueva lista */
			$cliente2 = ModeloClientes::mdlMostrarClientes($tablaClientes, "id", $idClienteActual);
			$comprasBase = $cliente2 ? (int)$cliente2["compras"] : 0;
			$comprasFinal = $comprasBase + array_sum($totalDespues);
			ModeloClientes::mdlActualizarCliente($tablaClientes, "compras", $comprasFinal, $idClienteActual);

			/* Ultima compra (Lima) */
			date_default_timezone_set('America/Lima');
			$valorUltima = date('Y-m-d').' '.date('H:i:s');
			ModeloClientes::mdlActualizarCliente($tablaClientes, "ultima_compra", $valorUltima, $idClienteActual);
		}

		/*=============================================
		GUARDAR CAMBIOS DE LA VENTA
		=============================================*/
        // Incluir tipo_comprobante al editar venta. Si no viene en el POST, conservar el existente.
        $datos = array(
            "id_vendedor"      => $_POST["idVendedor"],
            "id_cliente"       => $_POST["seleccionarCliente"],
            "codigo"           => $_POST["editarVenta"],
            "productos"        => $listaProductos,
            "impuesto"         => $_POST["nuevoPrecioImpuesto"],
            "neto"             => $_POST["nuevoPrecioNeto"],
            "total"            => $_POST["totalVenta"],
            "metodo_pago"      => $_POST["listaMetodoPago"],
            "tipo_comprobante" => isset($_POST["nuevoTipoComprobante"]) ? $_POST["nuevoTipoComprobante"] : (isset($traerVenta["tipo_comprobante"]) ? $traerVenta["tipo_comprobante"] : "Boleta")
        );

		$respuesta = ModeloVentas::mdlEditarVenta($tabla, $datos);

		if($respuesta == "ok"){
			echo'<script>
				localStorage.removeItem("rango");
				swal({
					type: "success",
					title: "La venta ha sido editada correctamente",
					showConfirmButton: true,
					confirmButtonText: "Cerrar"
				}).then((result) => {
					if (result.value) { window.location = "ventas"; }
				});
			</script>';
		}
	}

	/*=============================================
	ELIMINAR VENTA
	=============================================*/
	static public function ctrEliminarVenta(){

		if(!isset($_GET["idVenta"])) return;

		$tabla = "ventas";
		$idVenta = $_GET["idVenta"];

		$traerVenta = ModeloVentas::mdlMostrarVentas($tabla, "id", $idVenta);
		if(!$traerVenta) return;

		/*=============================================
		ACTUALIZAR FECHA ÚLTIMA COMPRA DEL CLIENTE
		=============================================*/
		$tablaClientes = "clientes";

		$traerVentasCliente = ModeloVentas::mdlMostrarVentas($tabla, null, null);
		$guardarFechas = array();

		foreach ($traerVentasCliente as $value) {
			if($value["id_cliente"] == $traerVenta["id_cliente"]){
				$guardarFechas[] = $value["fecha"];
			}
		}

		if(count($guardarFechas) > 1){
			$fechaSet = ($traerVenta["fecha"] > $guardarFechas[count($guardarFechas)-2])
				? $guardarFechas[count($guardarFechas)-2]
				: $guardarFechas[count($guardarFechas)-1];
			ModeloClientes::mdlActualizarCliente($tablaClientes, "ultima_compra", $fechaSet, $traerVenta["id_cliente"]);
		}else{
			ModeloClientes::mdlActualizarCliente($tablaClientes, "ultima_compra", "0000-00-00 00:00:00", $traerVenta["id_cliente"]);
		}

		/*=============================================
		REVERTIR PRODUCTOS (ventas y stock)
		=============================================*/
		$productos = json_decode($traerVenta["productos"], true);
		$totalProductosComprados = array();

		foreach ($productos as $value) {

			$idProducto = (int)$value["id"];
			$cant       = (int)$value["cantidad"];
			$totalProductosComprados[] = $cant;

			$tablaProductos = "productos";
			$prod = ModeloProductos::mdlMostrarProductos($tablaProductos, "id", $idProducto, "id");
			if(!$prod) continue;

			/* Ventas = ventas actuales - cantidad */
			$nuevasVentas = max(0, (int)$prod["ventas"] - $cant);
			ModeloProductos::mdlActualizarProducto($tablaProductos, "ventas", $nuevasVentas, "id", $idProducto);

			/* Stock = stock actual + cantidad */
			$nuevoStock = (int)$prod["stock"] + $cant;
			ModeloProductos::mdlActualizarProducto($tablaProductos, "stock", $nuevoStock, "id", $idProducto);
		}

		/*=============================================
		RESTAR COMPRAS AL CLIENTE
		=============================================*/
		$cliente = ModeloClientes::mdlMostrarClientes($tablaClientes, "id", $traerVenta["id_cliente"]);
		$comprasPrev = $cliente ? (int)$cliente["compras"] : 0;
		$comprasNueva = $comprasPrev - array_sum($totalProductosComprados);
		if($comprasNueva < 0) $comprasNueva = 0;
		ModeloClientes::mdlActualizarCliente($tablaClientes, "compras", $comprasNueva, $traerVenta["id_cliente"]);

		/*=============================================
		ELIMINAR VENTA
		=============================================*/
		$respuesta = ModeloVentas::mdlEliminarVenta($tabla, $idVenta);

		if($respuesta == "ok"){
			echo'<script>
				swal({
					type: "success",
					title: "La venta ha sido borrada correctamente",
					showConfirmButton: true,
					confirmButtonText: "Cerrar"
				}).then(function(result){
					if (result.value) { window.location = "ventas"; }
				});
			</script>';
		}
	}

	/*=============================================
	RANGO FECHAS
	=============================================*/
	static public function ctrRangoFechasVentas($fechaInicial, $fechaFinal){
		$tabla = "ventas";
		$respuesta = ModeloVentas::mdlRangoFechasVentas($tabla, $fechaInicial, $fechaFinal);
		return $respuesta;
	}

	/*=============================================
	DESCARGAR EXCEL
	=============================================*/
	public function ctrDescargarReporte(){

		if(!isset($_GET["reporte"])) return;

		$tabla = "ventas";

		if(isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])){
			$ventas = ModeloVentas::mdlRangoFechasVentas($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"]);
		}else{
			$ventas = ModeloVentas::mdlMostrarVentas($tabla, null, null);
		}

		/* Archivo Excel */
		$Name = $_GET["reporte"].'.xls';

		header('Expires: 0');
		header('Cache-control: private');
		header("Content-type: application/vnd.ms-excel");
		header("Cache-Control: cache, must-revalidate");
		header('Content-Description: File Transfer');
		header('Last-Modified: '.date('D, d M Y H:i:s'));
		header("Pragma: public");
		header('Content-Disposition:; filename="'.$Name.'"');
		header("Content-Transfer-Encoding: binary");

		echo utf8_decode("<table border='0'>
			<tr>
				<td style='font-weight:bold; border:1px solid #eee;'>CÓDIGO</td>
				<td style='font-weight:bold; border:1px solid #eee;'>CLIENTE</td>
				<td style='font-weight:bold; border:1px solid #eee;'>VENDEDOR</td>
				<td style='font-weight:bold; border:1px solid #eee;'>CANTIDAD</td>
				<td style='font-weight:bold; border:1px solid #eee;'>PRODUCTOS</td>
				<td style='font-weight:bold; border:1px solid #eee;'>IMPUESTO</td>
				<td style='font-weight:bold; border:1px solid #eee;'>NETO</td>
				<td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td>
				<td style='font-weight:bold; border:1px solid #eee;'>METODO DE PAGO</td>
				<td style='font-weight:bold; border:1px solid #eee;'>FECHA</td>
			</tr>");

		foreach ($ventas as $item){

			$cliente  = ControladorClientes::ctrMostrarClientes("id", $item["id_cliente"]);
			$vendedor = ControladorUsuarios::ctrMostrarUsuarios("id", $item["id_vendedor"]);

			echo utf8_decode("<tr>
				<td style='border:1px solid #eee;'>".$item["codigo"]."</td>
				<td style='border:1px solid #eee;'>".$cliente["nombre"]."</td>
				<td style='border:1px solid #eee;'>".$vendedor["nombre"]."</td>
				<td style='border:1px solid #eee;'>");

			$productos = json_decode($item["productos"], true);
			foreach ($productos as $p) { echo utf8_decode($p["cantidad"]."<br>"); }

			echo utf8_decode("</td><td style='border:1px solid #eee;'>");
			foreach ($productos as $p) { echo utf8_decode($p["descripcion"]."<br>"); }

			echo utf8_decode("</td>
				<td style='border:1px solid #eee;'>$ ".number_format($item["impuesto"],2)."</td>
				<td style='border:1px solid #eee;'>$ ".number_format($item["neto"],2)."</td>
				<td style='border:1px solid #eee;'>$ ".number_format($item["total"],2)."</td>
				<td style='border:1px solid #eee;'>".$item["metodo_pago"]."</td>
				<td style='border:1px solid #eee;'>".substr($item["fecha"],0,10)."</td>
			</tr>");
		}

		echo "</table>";
	}

	/*=============================================
	SUMA TOTAL VENTAS
	=============================================*/
	static public function ctrSumaTotalVentas(){
		$tabla = "ventas";
		$respuesta = ModeloVentas::mdlSumaTotalVentas($tabla);
		return $respuesta;
	}

}
