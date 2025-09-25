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
    SUMAR EL TOTAL DE VENTAS
    =============================================*/
    static public function ctrSumaTotalVentas(){
        $tabla = "ventas";
        $respuesta = ModeloVentas::mdlSumaTotalVentas($tabla);
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
				Swal.fire({
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
				Swal.fire({ type:"error", title:"Lista de productos inválida", showConfirmButton:true, confirmButtonText:"Cerrar" })
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
		$idCliente = $_POST["seleccionarCliente"] ?? $_POST["idClienteVenta"] ?? null;
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
		    // Calcular cantidad de productos
		    $listaProductos = json_decode($_POST["listaProductos"], true);
		    $totalProductos = 0;
		    foreach ($listaProductos as $producto) {
		        $totalProductos += $producto['cantidad'];
		    }
		    
		    // Determinar el tipo de comprobante para la impresión
		    $tipoComprobante = isset($_POST["nuevoTipoComprobante"]) ? $_POST["nuevoTipoComprobante"] : "boleta";
		    $archivoImpresion = ($tipoComprobante == "factura") ? "factura.php" : "boleta.php";
		    
		    echo '<script>
		        localStorage.removeItem("rango");
		        
		        // Abrir impresión inmediatamente (antes del alerta)
		        var ventanaImpresion = window.open("extensiones/tcpdf/pdf/'.$archivoImpresion.'?codigo=' . $_POST["nuevaVenta"] . '", "_blank");
		        
		        // Si el navegador bloquea la ventana, mostrar instrucciones
		        if (!ventanaImpresion || ventanaImpresion.closed || typeof ventanaImpresion.closed == "undefined") {
		            var mensajeImpresion = "<p style=\"color: #dc3545;\"><i class=\"fa fa-warning\"></i> La impresión fue bloqueada. <a href=\"extensiones/tcpdf/pdf/'.$archivoImpresion.'?codigo=' . $_POST["nuevaVenta"] . '\" target=\"_blank\">Haz clic aquí para abrir el comprobante</a></p>";
		        } else {
		            var mensajeImpresion = "<p style=\"color: #28a745;\"><i class=\"fa fa-check\"></i> El comprobante se abrió correctamente</p>";
		        }
		        
		        Swal.fire({
		            title: "🎊 Venta Exitosa",
		            html: `
		                <div style="text-align: center; padding: 15px;">
		                    <div style="font-size: 48px; margin-bottom: 15px;">✅</div>
		                    <h3 style="color: #28a745; margin-bottom: 20px;">¡Venta Registrada!</h3>
		                    
		                    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin: 15px 0;">
		                        <div style="display: flex; justify-content: space-around; text-align: center;">
		                            <div>
		                                <div style="font-size: 24px; color: #007bff;">' . count($listaProductos) . '</div>
		                                <div style="font-size: 12px; color: #6c757d;">Productos</div>
		                            </div>
		                            <div>
		                                <div style="font-size: 24px; color: #28a745;">' . $totalProductos . '</div>
		                                <div style="font-size: 12px; color: #6c757d;">Unidades</div>
		                            </div>
		                            <div>
		                                <div style="font-size: 24px; color: #fd7e14;">$ ' . number_format($_POST["totalVenta"], 2) . '</div>
		                                <div style="font-size: 12px; color: #6c757d;">Total</div>
		                            </div>
		                        </div>
		                    </div>
		                    
		                    ` + mensajeImpresion + `
		                    
		                    <div class="timer-progress">
		                        <div class="timer-progress-bar"></div>
		                    </div>
		                </div>
		            `,
		            icon: "success",
		            showConfirmButton: false,
		            timer: 5000,
		            timerProgressBar: true
		        }).then(() => {
		            window.location = "crear-venta";
		        });
		    </script>';
		} else {
		    echo '<script>
		        Swal.fire({
		            icon: "error",
		            title: "Error",
		            text: "No se pudo registrar la venta",
		            confirmButtonText: "Cerrar"
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
				Swal.fire({ type:"error", title:"Venta no encontrada", showConfirmButton:true, confirmButtonText:"Cerrar" })
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
				if($nuevoStock < 0) $nuevoStock = 0;
				ModeloProductos::mdlActualizarProducto($tablaProductos, "stock", $nuevoStock, "id", $idProducto);
			}

			/* Restar compras al cliente por la venta anterior */
			$tablaClientes = "clientes";
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
				$cant = (int)$value["cantidad"];

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

			/* Actualizar compras al cliente por la nueva venta */
			$nuevaSuma = array_sum($totalDespues) + $comprasNueva;
			ModeloClientes::mdlActualizarCliente($tablaClientes, "compras", $nuevaSuma, $idClienteActual);
		}

		/*=============================================
		GUARDAR LA VENTA
		=============================================*/
		$datos = array(
			"id"               => $_POST["idVenta"],
			"id_vendedor"      => $_POST["idVendedor"],
			"id_cliente"       => $_POST["seleccionarCliente"],
			"codigo"           => $_POST["editarVenta"],
			"productos"        => $listaProductos,
			"impuesto"         => $_POST["nuevoPrecioImpuesto"],
			"neto"             => $_POST["nuevoPrecioNeto"],
			"total"            => $_POST["totalVenta"],
			"metodo_pago"      => $_POST["listaMetodoPago"],
			"tipo_comprobante" => isset($_POST["editarTipoComprobante"]) ? $_POST["editarTipoComprobante"] : "Boleta"
		);

		$respuesta = ModeloVentas::mdlEditarVenta($tabla, $datos);

		if($respuesta == "ok"){
			echo'<script>
				localStorage.removeItem("rango");
				Swal.fire({
					type: "success",
					title: "La venta ha sido editada correctamente",
					showConfirmButton: true,
					confirmButtonText: "Cerrar"
				}).then(function(result){
					if (result.value) { window.location = "ventas"; }
				});
			</script>';
		}
	}

	/*=============================================
	ELIMINAR VENTA
	=============================================*/
	static public function ctrEliminarVenta(){

		if(isset($_GET["idVenta"])){

			$tabla = "ventas";
			$item  = "id";
			$valor = $_GET["idVenta"];

			$traerVenta = ModeloVentas::mdlMostrarVentas($tabla, $item, $valor);

			/*=============================================
			ACTUALIZAR FECHA ÚLTIMA COMPRA CLIENTE
			=============================================*/
			$tablaClientes = "clientes";
			$itemVentas = null;
			$valorVentas = null;

			$traerVentas = ModeloVentas::mdlMostrarVentas($tabla, $itemVentas, $valorVentas);

			$sumarTotalCompras = array();

			foreach ($traerVentas as $key => $value) {
				if($value["id_cliente"] == $traerVenta["id_cliente"]){
					array_push($sumarTotalCompras, $value["productos"]);
				}
			}

			$sumarTotalCompras2 = array();

			foreach ($sumarTotalCompras as $key => $value) {
				$productos = json_decode($value, true);
				foreach ($productos as $key2 => $value2) {
					array_push($sumarTotalCompras2, $value2["cantidad"]);
				}
			}

			$sumaTotal = array_sum($sumarTotalCompras2);
			$modeloCompras = ModeloClientes::mdlActualizarCliente($tablaClientes, "compras", $sumaTotal, $traerVenta["id_cliente"]);
			if($modeloCompras == "ok"){
				$traerVentas = ModeloVentas::mdlMostrarVentas($tabla, $itemVentas, $valorVentas);
				$fechaCliente = array();

				foreach ($traerVentas as $key => $value) {
					if($value["id_cliente"] == $traerVenta["id_cliente"]){
						array_push($fechaCliente, $value["fecha"]);
					}
				}

				if(count($fechaCliente) > 0){
					$cliente = ModeloClientes::mdlActualizarCliente($tablaClientes, "ultima_compra", end($fechaCliente), $traerVenta["id_cliente"]);
				}
			}

			/*=============================================
			ACTUALIZAR STOCK Y VENTAS DE PRODUCTOS
			=============================================*/
			$productos = json_decode($traerVenta["productos"], true);

			foreach ($productos as $key => $value) {
				$tablaProductos = "productos";

				$item = "id";
				$valor = $value["id"];
				$orden = "id";

				$traerProducto = ModeloProductos::mdlMostrarProductos($tablaProductos, $item, $valor, $orden);

				$item1a = "ventas";
				$valor1a = (int)$traerProducto["ventas"] - $value["cantidad"];
				$nuevoProductoVentas = ModeloProductos::mdlActualizarProducto($tablaProductos, $item1a, $valor1a, $item, $valor);

				$item2a = "stock";
				$valor2a = $value["cantidad"] + (int)$traerProducto["stock"];
				$nuevoProductoStock = ModeloProductos::mdlActualizarProducto($tablaProductos, $item2a, $valor2a, $item, $valor);
			}

			$respuesta = ModeloVentas::mdlBorrarVenta($tabla, $_GET["idVenta"]);

			if($respuesta == "ok"){
				echo'<script>
					Swal.fire({
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
	DESCARGAR REPORTE EXCEL
	=============================================*/
	public function ctrDescargarReporte(){
		if(isset($_GET["reporte"])){
			date_default_timezone_set('America/Lima');
			$fecha = date('Y-m-d');
			$hora = date('H:i:s');
			$nombreArchivo = 'Reporte-Ventas-'.$fecha.'-'.$hora.'.xls';
			header('Content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$nombreArchivo);
			header('Pragma: no-cache');
			header('Expires: 0');
			
			$tabla = "ventas";
			if(isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])){
				$ventas = ModeloVentas::mdlRangoFechasVentas($tabla, $_GET["fechaInicial"], $_GET["fechaFinal"]);
			}else{
				$item = null;
				$valor = null;
				$ventas = ModeloVentas::mdlMostrarVentas($tabla, $item, $valor);
			}
			
			echo utf8_decode("<table border='1'>
				<tr>
				<td style='font-weight:bold; border:1px solid #eee;'>CÓDIGO</td>
				<td style='font-weight:bold; border:1px solid #eee;'>CLIENTE</td>
				<td style='font-weight:bold; border:1px solid #eee;'>VENDEDOR</td>
				<td style='font-weight:bold; border:1px solid #eee;'>CANTIDAD</td>
				<td style='font-weight:bold; border:1px solid #eee;'>PRODUCTOS</td>
				<td style='font-weight:bold; border:1px solid #eee;'>IMPUESTO</td>
				<td style='font-weight:bold; border:1px solid #eee;'>NETO</td>
				<td style='font-weight:bold; border:1px solid #eee;'>TOTAL</td>
				<td style='font-weight:bold; border:1px solid #eee;'>MÉTODO DE PAGO</td>
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
	}
}