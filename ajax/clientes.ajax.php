<?php
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

// Manejar solicitudes AJAX para clientes
if (isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'buscarPorDocumento':
            // Buscar cliente por documento
            $tipoDocumento = $_POST['tipoDocumento'];
            $documento = $_POST['documento'];
            
            $item = 'documento';
            $valor = $documento;
            
            $cliente = ControladorClientes::ctrMostrarClientes($item, $valor);
            
            if ($cliente && $cliente['tipo_documento'] == $tipoDocumento) {
                echo json_encode([
                    'ok' => true,
                    'id' => $cliente['id'],
                    'nombre' => $cliente['nombre'],
                    'documento' => $cliente['documento'],
                    'tipo_documento' => $cliente['tipo_documento']
                ]);
            } else {
                echo json_encode(['ok' => false]);
            }
            break;
            
        case 'crearRapido':
            // Crear cliente rápidamente
            $datos = array(
                "nombre" => $_POST['nombre'],
                "tipo_documento" => $_POST['tipo_documento'],
                "documento" => $_POST['documento'],
                "direccion" => $_POST['direccion'],
                "email" => "",
                "telefono" => "",
                "fecha_nacimiento" => ""
            );
            
            $idCliente = ControladorClientes::ctrCrearClienteAjax($datos);
            
            if ($idCliente) {
                echo json_encode([
                    'ok' => true,
                    'id' => $idCliente,
                    'mensaje' => 'Cliente creado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Error al crear el cliente'
                ]);
            }
            break;
            
        default:
            echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida']);
            break;
    }
    exit;
}
?>