<?php

require_once "conexion.php";

class ModeloProductos
{

	/*=============================================
	MOSTRAR PRODUCTOS
	=============================================*/

	static public function mdlMostrarProductos($tabla, $item, $valor, $orden = "id"){
		$link = Conexion::conectar();

		if($item != null){
			$stmt = $link->prepare("SELECT * FROM $tabla WHERE $item = :$item LIMIT 1");
			$stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}else{
			$stmt = $link->prepare("SELECT * FROM $tabla ORDER BY $orden DESC");
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	/*=============================================
	REGISTRO DE PRODUCTO
	=============================================*/
	static public function mdlIngresarProducto($tabla, $datos)
	{

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_categoria, codigo, descripcion, imagen, stock, precioMayor, precio_compra, precio_venta) VALUES (:id_categoria, :codigo, :descripcion, :imagen, :stock, :precioMayor, :precio_compra, :precio_venta)");

		$stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
		$stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
		$stmt->bindParam(":stock", $datos["stock"], PDO::PARAM_STR);
		$stmt->bindParam(":precioMayor", $datos["precioMayor"], PDO::PARAM_STR);
		$stmt->bindParam(":precio_compra", $datos["precio_compra"], PDO::PARAM_STR);
		$stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_STR);

		if ($stmt->execute()) {

			return "ok";
		} else {

			return "error";
		}

		$stmt->close();
		$stmt = null;
	}

	/*=============================================
	EDITAR PRODUCTO
	=============================================*/
	static public function mdlEditarProducto($tabla, $datos)
	{

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET id_categoria = :id_categoria, descripcion = :descripcion, imagen = :imagen, stock = :stock, precioMayor = :precioMayor, precio_compra = :precio_compra, precio_venta = :precio_venta WHERE codigo = :codigo");

		$stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
		$stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
		$stmt->bindParam(":stock", $datos["stock"], PDO::PARAM_STR);
		$stmt->bindParam(":precioMayor", $datos["precioMayor"], PDO::PARAM_STR);
		$stmt->bindParam(":precio_compra", $datos["precio_compra"], PDO::PARAM_STR);
		$stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_STR);

		if ($stmt->execute()) {

			return "ok";
		} else {

			return "error";
		}

		$stmt->close();
		$stmt = null;
	}

	/*=============================================
	BORRAR PRODUCTO
	=============================================*/

	static public function mdlEliminarProducto($tabla, $datos)
	{

		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");

		$stmt->bindParam(":id", $datos, PDO::PARAM_INT);

		if ($stmt->execute()) {

			return "ok";
		} else {

			return "error";
		}

		$stmt->close();

		$stmt = null;
	}

	/*=============================================
	ACTUALIZAR PRODUCTO
	=============================================*/

	static public function mdlActualizarProducto($tabla, $itemSet, $valorSet, $itemWhere, $valorWhere){
		$link = Conexion::conectar();

		$sql = "UPDATE $tabla SET $itemSet = :valorSet WHERE $itemWhere = :valorWhere";
		$stmt = $link->prepare($sql);

		$paramTipoSet   = is_numeric($valorSet)   ? PDO::PARAM_INT : PDO::PARAM_STR;
		$paramTipoWhere = is_numeric($valorWhere) ? PDO::PARAM_INT : PDO::PARAM_STR;

		$stmt->bindParam(":valorSet",   $valorSet,   $paramTipoSet);
		$stmt->bindParam(":valorWhere", $valorWhere, $paramTipoWhere);

		if($stmt->execute()){
			return "ok";
		}
		return "error";
	}

	/*=============================================
	MOSTRAR SUMA VENTAS
	=============================================*/

	static public function mdlMostrarSumaVentas($tabla)
	{

		$stmt = Conexion::conectar()->prepare("SELECT SUM(ventas) as total FROM $tabla");

		$stmt->execute();

		return $stmt->fetch();

		$stmt->close();

		$stmt = null;
	}

/*=============================================
MOSTRAR PRODUCTOS ACTIVOS Y CON STOCK > 0
=============================================*/
static public function mdlMostrarProductosActivos($tabla, $orden){
    $link = Conexion::conectar();
    $stmt = $link->prepare("SELECT * FROM $tabla WHERE estado = 0 AND stock > 0 ORDER BY $orden DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*=============================================
MOSTRAR PRODUCTO POR DESCRIPCIÓN (solo activos y con stock > 0)
=============================================*/
static public function mdlMostrarProductoDescripcionActiva($tabla, $descripcion){
    $link = Conexion::conectar();
    $stmt = $link->prepare("SELECT * FROM $tabla WHERE descripcion LIKE :descripcion AND estado = 0 AND stock > 0");
    $stmt->bindValue(':descripcion', '%'.$descripcion.'%', PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
