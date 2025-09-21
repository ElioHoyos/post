<?php

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

require_once "../controladores/categorias.controlador.php";
require_once "../modelos/categorias.modelo.php";

class AjaxProductos{

  /*=============================================
  GENERAR CÓDIGO A PARTIR DE ID CATEGORIA
  =============================================*/
  public $idCategoria;

  public function ajaxCrearCodigoProducto(){

  	$item = "id_categoria";
  	$valor = $this->idCategoria;
    $orden = "id";

  	$respuesta = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

  	echo json_encode($respuesta);

  }

public $activarProducto;
public $estadoProducto;

public function ajaxActivarProducto() {

  $tabla = "productos";
  $item1 = "estado";                      // columna a actualizar
  $valor1 = intval($this->estadoProducto);
  $item2 = "id";                          // clave primaria
  $valor2 = intval($this->activarProducto);

  $respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
  echo $respuesta; // "ok" | "error"
}


  /*=============================================
  EDITAR PRODUCTO
  =============================================*/ 

  public $idProducto;
  public $traerProductos;
  public $nombreProducto;

  public function ajaxEditarProducto(){

    if($this->traerProductos == "ok"){
      // Solo productos activos para el flujo de ventas
      $orden = "id";
      $respuesta = ControladorProductos::ctrMostrarProductosActivos($orden);

      echo json_encode($respuesta);


    }else if($this->nombreProducto != ""){
      // Buscar por descripción pero solo si está activo
      $respuesta = ControladorProductos::ctrMostrarProductoDescripcionActiva($this->nombreProducto);

      echo json_encode($respuesta);

    }else{

      $item = "id";
      $valor = $this->idProducto;
      $orden = "id";

      $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor,
        $orden);

      echo json_encode($respuesta);

    }

  }

}


/*=============================================
GENERAR CÓDIGO A PARTIR DE ID CATEGORIA
=============================================*/	

if(isset($_POST["idCategoria"])){

	$codigoProducto = new AjaxProductos();
	$codigoProducto -> idCategoria = $_POST["idCategoria"];
	$codigoProducto -> ajaxCrearCodigoProducto();

}
/*=============================================
EDITAR PRODUCTO
=============================================*/ 

if(isset($_POST["idProducto"])){

  $editarProducto = new AjaxProductos();
  $editarProducto -> idProducto = $_POST["idProducto"];
  $editarProducto -> ajaxEditarProducto();

}

/*=============================================
TRAER PRODUCTO
=============================================*/ 

if(isset($_POST["traerProductos"])){

  $traerProductos = new AjaxProductos();
  $traerProductos -> traerProductos = $_POST["traerProductos"];
  $traerProductos -> ajaxEditarProducto();

}

/*=============================================
TRAER PRODUCTO
=============================================*/ 

if(isset($_POST["nombreProducto"])){

  $traerProductos = new AjaxProductos();
  $traerProductos -> nombreProducto = $_POST["nombreProducto"];
  $traerProductos -> ajaxEditarProducto();

}

/*=============================================
ACTIVAR / DESACTIVAR PRODUCTO
=============================================*/
if (isset($_POST["activarProducto"]) && isset($_POST["estadoProducto"])) {

  $activar = new AjaxProductos();
  $activar->activarProducto = $_POST["activarProducto"];   // id del producto
  $activar->estadoProducto  = $_POST["estadoProducto"];     // 0 ó 1 (NUEVO estado)
  $activar->ajaxActivarProducto();
  exit();
}





