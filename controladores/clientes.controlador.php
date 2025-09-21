<?php

class ControladorClientes
{

    /*=============================================
	CREAR CLIENTES
	=============================================*/

    static public function ctrCrearCliente()
    {

        /*
         * Procesa el formulario de creación de cliente. El nombre y el documento son
         * obligatorios. El tipo de documento debe ser DNI o RUC. Si el tipo de
         * documento es DNI, el número de documento debe tener 8 dígitos. Si es
         * RUC, debe tener 11 dígitos. El resto de campos (email, teléfono,
         * dirección, fecha de nacimiento) son opcionales.
         */
        if (isset($_POST["nuevoCliente"]) && isset($_POST["nuevoTipoDocumento"]) && isset($_POST["nuevoDocumentoId"])) {

            $nombre          = trim($_POST["nuevoCliente"]);
            $tipoDocumento   = trim($_POST["nuevoTipoDocumento"]);
            $documento       = trim($_POST["nuevoDocumentoId"]);

            // Validaciones básicas
            $validNombre = preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $nombre);
            $validTipo   = in_array($tipoDocumento, array('DNI', 'RUC'));
            $validDoc    = false;
            if ($tipoDocumento == 'DNI') {
                $validDoc = preg_match('/^[0-9]{8}$/', $documento);
            } else if ($tipoDocumento == 'RUC') {
                $validDoc = preg_match('/^[0-9]{11}$/', $documento);
            }

            // Campos opcionales
            $email          = isset($_POST["nuevoEmail"]) ? trim($_POST["nuevoEmail"]) : '';
            $telefono       = isset($_POST["nuevoTelefono"]) ? trim($_POST["nuevoTelefono"]) : '';
            $direccion      = isset($_POST["nuevaDireccion"]) ? trim($_POST["nuevaDireccion"]) : '';
            $fechaNacimiento = isset($_POST["nuevaFechaNacimiento"]) ? trim($_POST["nuevaFechaNacimiento"]) : '';

            $validEmail = true;
            if ($email !== '') {
                $validEmail = preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $email);
            }
            $validTelefono = true;
            if ($telefono !== '') {
                $validTelefono = preg_match('/^[()\-0-9 ]+$/', $telefono);
            }
            $validDireccion = true;
            if ($direccion !== '') {
                $validDireccion = preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $direccion);
            }

            if ($validNombre && $validTipo && $validDoc && $validEmail && $validTelefono && $validDireccion) {

                $tabla = "clientes";
                $datos = array(
                    "nombre"          => $nombre,
                    "tipo_documento"   => $tipoDocumento,
                    "documento"        => $documento,
                    "email"           => $email,
                    "telefono"         => $telefono,
                    "direccion"        => $direccion,
                    "fecha_nacimiento" => $fechaNacimiento
                );

                $respuesta = ModeloClientes::mdlIngresarCliente($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
  Swal.fire({
    icon: "success",
    title: "¡Cliente registrado!",
    text: "Se guardó correctamente.",
    timer: 5000,
    timerProgressBar: true,
    showConfirmButton: false
  }).then(function(){

    // Cierra el modal si sigue abierto
    if (window.$) {
      if ($("#modalAgregarCliente").hasClass("show")) {
        $("#modalAgregarCliente").modal("hide");
      }
      // Refresca DataTable si existe
      if ($.fn && $.fn.dataTable && $.fn.dataTable.isDataTable(".tablaClientes")) {
        $(".tablaClientes").DataTable().ajax.reload(null, false); // false = conserva paginación
        return; // no redirige, ya se refrescó
      }
    }

    // Fallback: recarga la página/lista
    window.location = "clientes";
  });
</script>';
                } else {
                    echo '<script>
                        swal({
                            type: "error",
                            title: "¡Error al guardar el cliente!",
                            text: "Hubo un problema al registrar el cliente en la base de datos.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if (result.value) {
                                window.location = "clientes";
                            }
                        });
                    </script>';
                }
            } else {
                // Determinar qué campo específico está causando el error
                $mensajeError = "¡Los datos del cliente son inválidos!";
                $detalleError = "";

                if (!$validNombre) {
                    $detalleError = "El nombre debe contener solo letras, números y espacios.";
                } elseif (!$validTipo) {
                    $detalleError = "Debe seleccionar un tipo de documento válido (DNI o RUC).";
                } elseif (!$validDoc) {
                    if ($tipoDocumento == 'DNI') {
                        $detalleError = "El DNI debe tener exactamente 8 dígitos numéricos.";
                    } else {
                        $detalleError = "El RUC debe tener exactamente 11 dígitos numéricos.";
                    }
                } elseif (!$validEmail) {
                    $detalleError = "El formato del email no es válido.";
                } elseif (!$validTelefono) {
                    $detalleError = "El teléfono solo puede contener números, espacios, paréntesis y guiones.";
                } elseif (!$validDireccion) {
                    $detalleError = "La dirección contiene caracteres no válidos.";
                }

                echo '<script>
                    swal({
                        type: "error",
                        title: "' . $mensajeError . '",
                        text: "' . $detalleError . '",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "clientes";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
	MOSTRAR CLIENTES
	=============================================*/

    static public function ctrMostrarClientes($item, $valor)
    {

        $tabla = "clientes";

        $respuesta = ModeloClientes::mdlMostrarClientes($tabla, $item, $valor);

        return $respuesta;
    }

    /*=============================================
	EDITAR CLIENTE
	=============================================*/

    static public function ctrEditarCliente()
    {

        if (isset($_POST["editarCliente"]) && isset($_POST["editarTipoDocumento"]) && isset($_POST["editarDocumentoId"])) {

            $nombre          = trim($_POST["editarCliente"]);
            $tipoDocumento   = trim($_POST["editarTipoDocumento"]);
            $documento       = trim($_POST["editarDocumentoId"]);
            $idCliente       = (int)$_POST["idCliente"];

            $validNombre = preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $nombre);
            $validTipo   = in_array($tipoDocumento, array('DNI', 'RUC'));
            $validDoc    = false;
            if ($tipoDocumento == 'DNI') {
                $validDoc = preg_match('/^[0-9]{8}$/', $documento);
            } elseif ($tipoDocumento == 'RUC') {
                $validDoc = preg_match('/^[0-9]{11}$/', $documento);
            }

            // Campos opcionales
            $email     = isset($_POST["editarEmail"]) ? trim($_POST["editarEmail"]) : '';
            $telefono  = isset($_POST["editarTelefono"]) ? trim($_POST["editarTelefono"]) : '';
            $direccion = isset($_POST["editarDireccion"]) ? trim($_POST["editarDireccion"]) : '';
            $fechaNac  = isset($_POST["editarFechaNacimiento"]) ? trim($_POST["editarFechaNacimiento"]) : '';

            $validEmail     = true;
            if ($email !== '') {
                $validEmail = preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $email);
            }
            $validTelefono  = true;
            if ($telefono !== '') {
                $validTelefono = preg_match('/^[()\-0-9 ]+$/', $telefono);
            }
            $validDireccion = true;
            if ($direccion !== '') {
                $validDireccion = preg_match('/^[#\.\-a-zA-Z0-9 ]+$/', $direccion);
            }

            if ($validNombre && $validTipo && $validDoc && $validEmail && $validTelefono && $validDireccion) {

                $tabla = "clientes";
                $datos = array(
                    "id"             => $idCliente,
                    "nombre"         => $nombre,
                    "tipo_documento" => $tipoDocumento,
                    "documento"      => $documento,
                    "email"          => $email,
                    "telefono"        => $telefono,
                    "direccion"       => $direccion,
                    "fecha_nacimiento" => $fechaNac
                );

                $respuesta = ModeloClientes::mdlEditarCliente($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
  Swal.fire({
    icon: "success",
    title: "¡Cliente actualizado!",
    timer: 5000,
    timerProgressBar: true,
    showConfirmButton: false
  }).then(function(){
    if (window.$) {
      if ($("#modalEditarCliente").hasClass("show")) {
        $("#modalEditarCliente").modal("hide");
      }
      if ($.fn && $.fn.dataTable && $.fn.dataTable.isDataTable(".tablaClientes")) {
        $(".tablaClientes").DataTable().ajax.reload(null, false);
        return;
      }
    }
    window.location = "clientes";
  });
</script>';
                }
            } else {
                echo '<script>
                    swal({
                        type: "error",
                        title: "¡Los datos del cliente son inválidos!",
                        text: "Revise el nombre o el número de documento.",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "clientes";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
	ELIMINAR CLIENTE
	=============================================*/

    static public function ctrEliminarCliente()
    {

        if (isset($_GET["idCliente"])) {

            $tabla = "clientes";
            $datos = $_GET["idCliente"];

            $respuesta = ModeloClientes::mdlEliminarCliente($tabla, $datos);

            if ($respuesta == "ok") {

                echo '<script>

				swal({
					  type: "success",
					  title: "El cliente ha sido borrado correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result){
								if (result.value) {

								window.location = "clientes";

								}
							})

				</script>';
            }
        }
    }
}
