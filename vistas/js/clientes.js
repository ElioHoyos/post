/*=============================================
EDITAR CLIENTE
=============================================*/
$(".tablas").on("click", ".btnEditarCliente", function(){

  var idCliente = $(this).attr("idCliente");

  var datos = new FormData();
  datos.append("idCliente", idCliente);

  $.ajax({
    url:"ajax/clientes.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType:"json",
    success:function(respuesta){
      // Rellenar campos del modal de edición
      $("#idCliente").val(respuesta["id"] || "");
      $("#editarCliente").val(respuesta["nombre"] || "");
      $("#editarDocumentoId").val(respuesta["documento"] || "");
      $("#editarEmail").val(respuesta["email"] || "");
      $("#editarTelefono").val(respuesta["telefono"] || "");
      $("#editarDireccion").val(respuesta["direccion"] || "");
      $("#editarFechaNacimiento").val(respuesta["fecha_nacimiento"] || "");

      // Establecer tipo de documento y limitar longitud
      var tipoDoc = respuesta["tipo_documento"] || "DNI";
      $("#editarTipoDocumento").val(tipoDoc);
      if(tipoDoc === 'RUC'){
        $("#editarDocumentoId").attr('maxlength','11');
      }else{
        $("#editarDocumentoId").attr('maxlength','8');
      }
    }
  });

});

/*=============================================
FUNCIÓN PARA MOSTRAR MENSAJES EN EL MODAL
=============================================*/
function mostrarMensajeModal(formTipo, mensaje, tipo) {
  var modalId = (formTipo === 'nuevo') ? '#modalAgregarCliente' : '#modalEditarCliente';
  var mensajeId = (formTipo === 'nuevo') ? '#mensajeNuevoCliente' : '#mensajeEditarCliente';
  
  // Crear el elemento de mensaje si no existe
  if($(mensajeId).length === 0) {
    var mensajeHtml = '<div id="' + mensajeId.substring(1) + '" class="mensaje-modal" style="margin: 10px 0; padding: 10px; border-radius: 4px; display: none;"></div>';
    $(modalId + ' .modal-body').prepend(mensajeHtml);
  }
  
  // Configurar estilos según el tipo
  var estilos = {
    'info': {
      'background-color': '#d1ecf1',
      'color': '#0c5460',
      'border': '1px solid #bee5eb'
    },
    'success': {
      'background-color': '#d4edda',
      'color': '#155724',
      'border': '1px solid #c3e6cb'
    },
    'error': {
      'background-color': '#f8d7da',
      'color': '#721c24',
      'border': '1px solid #f5c6cb'
    }
  };
  
  // Aplicar estilos y mostrar mensaje
  $(mensajeId).css(estilos[tipo] || estilos['info'])
             .html(mensaje)
             .fadeIn(300);
  
  // Auto-ocultar después de 5 segundos para mensajes de éxito y error
  if(tipo === 'success' || tipo === 'error') {
    setTimeout(function() {
      $(mensajeId).fadeOut(300);
    }, 5000);
  }
}

/*=============================================
FUNCIÓN PARA OCULTAR MENSAJES DEL MODAL
=============================================*/
function ocultarMensajeModal(formTipo) {
  var mensajeId = (formTipo === 'nuevo') ? '#mensajeNuevoCliente' : '#mensajeEditarCliente';
  $(mensajeId).fadeOut(300);
}

/*=============================================
 Ajustar longitud de documento en formularios
=============================================*/
function updateDocumentoMaxLength(prefix){
  var tipo = $("#" + prefix + "TipoDocumento").val();
  var $input = $("#" + prefix + "DocumentoId");
  if(tipo === 'DNI'){
    $input.attr('maxlength','8');
  }else if(tipo === 'RUC'){
    $input.attr('maxlength','11');
  }else{
    $input.removeAttr('maxlength');
  }
}

/*=============================================
 Consultar nombre y dirección según documento
=============================================*/
function buscarDocumento(formTipo){

  // Default: si no envían parámetro, asumir "nuevo"
  formTipo = (formTipo === 'editar') ? 'editar' : 'nuevo';

  // Contexto del modal
  var $ctx        = (formTipo === 'editar') ? $('#modalEditarCliente') : $('#modalAgregarCliente');
  var idPrefix    = (formTipo === 'editar') ? 'editar' : 'nuevo';
  var idDir       = (formTipo === 'editar') ? '#editarDireccion' : '#nuevaDireccion';

  var tipoDocumento = $("#" + idPrefix + "TipoDocumento").val();
  var documento     = ($("#" + idPrefix + "DocumentoId").val() || "").trim();

  if(!documento){
    Swal.fire({ icon:'info', title:'Por favor ingrese un número de documento.' });
    return;
  }

  if(tipoDocumento === 'DNI' && documento.length !== 8){
    Swal.fire({ icon:'warning', title:'DNI inválido', text:'Debe tener 8 dígitos.' });
    return;
  }
  if(tipoDocumento === 'RUC' && documento.length !== 11){
    Swal.fire({ icon:'warning', title:'RUC inválido', text:'Debe tener 11 dígitos.' });
    return;
  }

  var urlConsulta = (tipoDocumento === 'RUC') ? 'ajax/consultaruc.php' : 'ajax/consultadni.php';

  console.log('Buscando información del documento...');

  $.post(urlConsulta, { documento: documento })
    .done(function(data){

      var res;
      try { res = (typeof data === 'string') ? JSON.parse(data) : data; } catch(e){ res = null; }

      // Seleccionar inputs de forma robusta
      var $nombreInput = $ctx.find('#' + idPrefix + 'Cliente');
      if(!$nombreInput.length) $nombreInput = $ctx.find('input[name="' + idPrefix + 'Cliente"]');
      if(!$nombreInput.length) $nombreInput = $('#' + idPrefix + 'Cliente'); // fallback global

      var $dirInput = $ctx.find(idDir);
      if(!$dirInput.length) $dirInput = $(idDir); // fallback global

      if($nombreInput.length) $nombreInput.val('');
      if($dirInput.length)    $dirInput.val('');

      if(!res){
        Swal.fire({ icon:'error', title:'No se pudo leer la respuesta del servicio.' });
        return;
      }

      if(String(res.codigo) === '1'){

        var nombreCompleto = (tipoDocumento === 'RUC')
          ? (res.data.razon_social || res.data.nombre || '')
          : (res.data.nombre || '');
        nombreCompleto = nombreCompleto.toString().trim();

        var direccion = (res.data.direccion ||
                         res.data.direccion_completa ||
                         res.data.domicilio_fiscal ||
                         '').toString().trim();

        // Escribir SIN trigger para evitar que otro listener lo borre
        if($nombreInput.length){
          $nombreInput.val(nombreCompleto).prop('value', nombreCompleto);
          // re-afirma el valor por si otro handler lo limpia async
          setTimeout(function(){ $nombreInput.val(nombreCompleto).prop('value', nombreCompleto); }, 25);
        } else {
          console.warn('No se encontró el input de nombre ('+idPrefix+'Cliente). Revisa el id o name.');
        }

        if($dirInput.length && direccion){
          $dirInput.val(direccion).prop('value', direccion);
        }

        console.log('Información encontrada: ' + nombreCompleto);
        setTimeout(function(){ if($nombreInput.length){ $nombreInput.focus(); } }, 0);

      } else {
        Swal.fire({ icon:'info', title: (res.mensaje || 'No se encontró información para el documento ingresado.') });
      }
    })
    .fail(function(){
      Swal.fire({ icon:'error', title:'Error de conexión. Verifique su internet.' });
    });
}


/*=============================================
 Enlazar cambios de tipo de documento
=============================================*/
$(function(){
  // Para el modal "Agregar cliente"
  $("#nuevoTipoDocumento").on('change', function(){
    updateDocumentoMaxLength('nuevo');
    $("#nuevoDocumentoId").val('');
  });

  // Para el modal "Editar cliente"
  $("#editarTipoDocumento").on('change', function(){
    updateDocumentoMaxLength('editar');
    $("#editarDocumentoId").val('');
  });
});


/*=============================================
ELIMINAR CLIENTE
=============================================*/
$(".tablas").on("click", ".btnEliminarCliente", function(){

  var idCliente = $(this).attr("idCliente");

  Swal.fire({
      title: '¿Está seguro de borrar el cliente?',
      text: "¡Si no lo está puede cancelar la acción!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Si, borrar cliente!'
    }).then(function(result){
      if (result.value) {
        window.location = "index.php?ruta=clientes&idCliente="+idCliente;
      }
  });

});

/*=============================================
VALIDACIÓN DEL FORMULARIO DE AGREGAR CLIENTE
=============================================*/
function validarFormularioCliente(formTipo) {
    var nombre = $("#" + formTipo + "Cliente").val().trim();
    var documento = $("#" + formTipo + "DocumentoId").val().trim();
    var tipoDocumento = $("#" + formTipo + "TipoDocumento").val();
    
    // Validar que el nombre no esté vacío
    if(nombre === '' || nombre.length < 2) {
        Swal.fire({
            type: "error",
            title: "¡Campo nombre requerido!",
            text: "Por favor ingrese el nombre del cliente (mínimo 2 caracteres).",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        });
        $("#" + formTipo + "Cliente").focus();
        return false;
    }
    
    // Validar que el documento no esté vacío
    if(documento === '') {
        Swal.fire({
            type: "error",
            title: "¡Campo documento requerido!",
            text: "Por favor ingrese el número de documento.",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        });
        $("#" + formTipo + "DocumentoId").focus();
        return false;
    }
    
    // Validar longitud del documento según tipo
    if(tipoDocumento === 'DNI' && documento.length !== 8) {
        Swal.fire({
            type: "error",
            title: "¡Documento DNI inválido!",
            text: "El DNI debe tener exactamente 8 dígitos.",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        });
        $("#" + formTipo + "DocumentoId").focus();
        return false;
    }
    
    if(tipoDocumento === 'RUC' && documento.length !== 11) {
        Swal.fire({
            type: "error",
            title: "¡Documento RUC inválido!",
            text: "El RUC debe tener exactamente 11 dígitos.",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        });
        $("#" + formTipo + "DocumentoId").focus();
        return false;
    }
    
    return true;
}

/*=============================================
INTERCEPTAR ENVÍO DEL FORMULARIO DE AGREGAR CLIENTE
=============================================*/
$(document).ready(function() {
    // Limpiar mensajes cuando se abren los modales
    $('#modalAgregarCliente').on('show.bs.modal', function() {
        ocultarMensajeModal('nuevo');
    });
    
    $('#modalEditarCliente').on('show.bs.modal', function() {
        ocultarMensajeModal('editar');
    });
    
    // Interceptar el envío del formulario de agregar cliente
    $("#modalAgregarCliente form").on("submit", function(e) {
        if(!validarFormularioCliente('nuevo')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Interceptar el envío del formulario de editar cliente
    $("#modalEditarCliente form").on("submit", function(e) {
        if(!validarFormularioCliente('editar')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Validación en tiempo real para campos de nombre
    $('#nuevoCliente, #editarCliente').on('input', function() {
        var $campo = $(this);
        var valor = $campo.val().trim();
        var formTipo = $campo.attr('id').includes('nuevo') ? 'nuevo' : 'editar';
        
        if (valor.length >= 2) {
            $campo.removeClass('error').addClass('success');
            ocultarMensajeModal(formTipo);
        } else if (valor.length > 0) {
            $campo.removeClass('success').addClass('error');
        } else {
            $campo.removeClass('success error');
        }
    });
    
    // Validación en tiempo real para campos de documento
    $('#nuevoDocumentoId, #editarDocumentoId').on('input', function() {
        var $campo = $(this);
        var valor = $campo.val().trim();
        var formTipo = $campo.attr('id').includes('nuevo') ? 'nuevo' : 'editar';
        var tipoDocumento = $('#' + formTipo + 'TipoDocumento').val();
        
        var longitudCorrecta = false;
        if (tipoDocumento === 'DNI' && valor.length === 8) {
            longitudCorrecta = true;
        } else if (tipoDocumento === 'RUC' && valor.length === 11) {
            longitudCorrecta = true;
        }
        
        if (longitudCorrecta) {
            $campo.removeClass('error').addClass('success');
            ocultarMensajeModal(formTipo);
        } else if (valor.length > 0) {
            $campo.removeClass('success').addClass('error');
        } else {
            $campo.removeClass('success error');
        }
    });
});