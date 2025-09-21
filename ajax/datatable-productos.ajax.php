<?php
// datatable-productos.ajax.php

// ¡Muy importante!: No dejar que nada se imprima antes del JSON.
ob_start();
header('Content-Type: application/json; charset=utf-8');

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";
require_once "../controladores/categorias.controlador.php";
require_once "../modelos/categorias.modelo.php";

try {
    $item  = null;
    $valor = null;
    $orden = "id";

    $productos = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

    if (!$productos || !is_array($productos) || count($productos) === 0) {
        echo json_encode(["data" => []], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $rows = [];

    for ($i = 0; $i < count($productos); $i++) {
        // Sanitizar por si vienen caracteres raros
        $id          = (int)($productos[$i]["id"] ?? 0);
        $codigo      = (string)($productos[$i]["codigo"] ?? "");
        $descripcion = (string)($productos[$i]["descripcion"] ?? "");
        $idCategoria = (int)($productos[$i]["id_categoria"] ?? 0);
        $precioCompra= (string)($productos[$i]["precio_compra"] ?? "0");
        $precioVenta = (string)($productos[$i]["precio_venta"] ?? "0");
        $stockVal    = (int)($productos[$i]["stock"] ?? 0);
        $fecha       = (string)($productos[$i]["fecha"] ?? "");
        $estadoBD    = isset($productos[$i]["estado"]) ? (int)$productos[$i]["estado"] : 0;

        // Imagen
        $imgSrc = !empty($productos[$i]["imagen"]) ? $productos[$i]["imagen"] : "vistas/img/productos/default/anonymous.png";
        // Escapar atributos HTML básicos
        $imgSrcEsc = htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8');
        $imagen = "<img src='{$imgSrcEsc}' width='40' height='40' style='object-fit:cover;border-radius:4px;'>";

        // Categoría
        $catItem = "id";
        $cat     = ControladorCategorias::ctrMostrarCategorias($catItem, $idCategoria);
        $nomCat  = ($cat && isset($cat["categoria"])) ? (string)$cat["categoria"] : "Sin categoría";

        // Stock semáforo (HTML permitido por DataTables, pero va dentro de JSON encodeado)
        if ($stockVal <= 10) {
            $stock = "<button class='btn btn-danger'>{$stockVal}</button>";
        } elseif ($stockVal >= 11 && $stockVal <= 15) {
            $stock = "<button class='btn btn-warning'>{$stockVal}</button>";
        } else {
            $stock = "<button class='btn btn-success'>{$stockVal}</button>";
        }

        // Estado (0 activo, 1 inactivo)
        if ($estadoBD === 0) {
            $estado = "<button class='btn btn-success btnActivar' idProducto='{$id}' estadoProducto='0'>Activo</button>";
        } else {
            $estado = "<button class='btn btn-danger btnActivar' idProducto='{$id}' estadoProducto='1'>Inactivo</button>";
        }

        // Botones acciones
        $btnEditar = "<button class='btn btn-warning btnEditarProducto' idProducto='{$id}' data-toggle='modal' data-target='#modalEditarProducto'><i class='fa fa-pencil'></i></button>";
        $btnEliminar = "<button class='btn btn-danger btnEliminarProducto' idProducto='{$id}' codigo='" . htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8') . "' imagen='{$imgSrcEsc}'><i class='fa fa-times'></i></button>";

        if (isset($_GET["perfilOculto"]) && $_GET["perfilOculto"] === "Especial") {
            $botones = "<div class='btn-group'>{$btnEditar}</div>";
        } else {
            $botones = "<div class='btn-group'>{$btnEditar}{$btnEliminar}</div>";
        }

        // Armar la fila como array (no string). DataTables espera array de columnas.
        $rows[] = [
            (string)($i + 1),
            $imagen,
            htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($nomCat, ENT_QUOTES, 'UTF-8'),
            $stock,
            // Mantener precios como string o número; si usas formato con coma, mejor string
            (string)$precioCompra,
            (string)$precioVenta,
            $estado,
            htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8'),
            $botones
        ];
    }

    // Imprimir JSON limpio
    echo json_encode(["data" => $rows], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    // No rompas el JSON en la respuesta de DataTables.
    // Loguea internamente y devuelve data vacía con error opcional.
    error_log("DT Productos error: " . $e->getMessage());
    echo json_encode(["data" => [], "error" => "server_error"], JSON_UNESCAPED_UNICODE);
} finally {
    // Asegúrate de que no quede basura de salida previa
    ob_end_flush();
}
