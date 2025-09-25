/*=============================================
CARGAR LA TABLA DINÁMICA DE VENTAS
=============================================*/

$('.tablaVentas').DataTable({
  "ajax": "ajax/datatable-ventas.ajax.php",
  "deferRender": true,
  "retrieve": true,
  "processing": true,
  "language": {
    "sProcessing": "Procesando...",
    "sLengthMenu": "Mostrar _MENU_ registros",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "Ningún dato disponible en esta tabla",
    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
    "sSearch": "Buscar:",
    "sLoadingRecords": "Cargando...",
    "oPaginate": { "sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior" },
    "oAria": { "sSortAscending": ": Activar para ordenar la columna de manera ascendente", "sSortDescending": ": Activar para ordenar la columna de manera descendente" }
  }
});

/*=============================================
CONFIGURACIÓN MAYORISTA
=============================================*/
var UMBRAL_MAYOR = 6; // cantidad mínima para aplicar precioMayor automáticamente

/*=============================================
UTILIDADES
=============================================*/
function n(v){ v=parseFloat(v); return isNaN(v)?0:v; }
function money(v){ return n(v).toFixed(2); }
function toastInfo(t,m){
  if(window.Swal){ Swal.fire({icon:'info',title:t,text:m,timer:1800,showConfirmButton:false}); }
}

/*=============================================
SET PARA EVITAR DUPLICADOS
=============================================*/
var productosSeleccionados = new Set();

function filaPorIdProducto(id){
  return $('.nuevaDescripcionProducto[idProducto="'+id+'"]').closest('.row');
}
function registrarProductoEnSet(){
  productosSeleccionados.clear();
  $('.nuevaDescripcionProducto[idProducto]').each(function(){
    var id = $(this).attr('idProducto');
    if(id) productosSeleccionados.add(String(id));
  });
}
function deshabilitarBotonesAdd(id){
  $('button.agregarProducto[idProducto="'+id+'"]').removeClass('btn-primary agregarProducto').addClass('btn-default');
}
function habilitarBotonesAdd(id){
  $('button.recuperarBoton[idProducto="'+id+'"]').removeClass('btn-default').addClass('btn-primary agregarProducto');
}

/*=============================================
UI MAYORISTA
=============================================*/
function actualizarUIMayorista($fila){
  if(!$fila || !$fila.length) return;
  var $precio   = $fila.find('.nuevoPrecioProducto');
  var $chkMayor = $fila.find('.chkMayor');
  var $badge    = $fila.find('.mayor-badge');
  var $cantInp  = $fila.find('input.nuevaCantidadProducto');

  var cantidad  = n($cantInp.val() || 0);
  var pNormal   = n($precio.attr('data-precio-normal') || 0);
  var pMayor    = n($precio.attr('data-precio-mayor') || 0);
  var usarMayor = ($chkMayor.is(':checked')) || (UMBRAL_MAYOR>0 && cantidad>=UMBRAL_MAYOR && pMayor>0);

  if(usarMayor && pMayor>0){ $badge.show(); $fila.addClass('aplica-mayor'); }
  else { $badge.hide(); $fila.removeClass('aplica-mayor'); }

  var unit = (usarMayor && pMayor>0) ? pMayor : pNormal;
  var total = cantidad * unit;
  $precio.attr('title', cantidad+' x '+money(unit)+' = '+money(total));
}

/*=============================================
AGREGAR PRODUCTO DESDE TABLA (EVITA DUPLICADOS)
=============================================*/
$(document).on("click", "button.agregarProducto", function(){
  var idProducto = $(this).attr("idProducto");
  if(!idProducto) return;

  // Bloqueo anti-duplicado - CORREGIDO: Paréntesis extra eliminado
  if (productosSeleccionados.has(String(idProducto)) || filaPorIdProducto(idProducto).length) {
    var $ex = filaPorIdProducto(idProducto);
    toastInfo('Ya agregado','Ese producto ya está en la venta.');
    if($ex.length){ $ex.addClass('atencion'); setTimeout(function(){$ex.removeClass('atencion')},400); }
    return;
  }

  var datos = new FormData();
  datos.append("idProducto", idProducto);

  // Evita doble click mientras carga
  var $btn = $(this);
  if($btn.data('busy')) return;
  $btn.data('busy', true);

  $.ajax({
    url:"ajax/productos.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType:"json",
    success:function(respuesta){
      if(!respuesta){ Swal.fire({icon:'error', title:'Error', text:'Respuesta vacía del servidor'}); return; }

      var descripcion  = respuesta["descripcion"];
      var stock        = n(respuesta["stock"]);
      var precioNormal = n(respuesta["precio_venta"] || 0);
      var precioMayor  = n(respuesta["precioMayor"]   || 0);

      // Sin stock
      if(stock == 0){
        Swal.fire({ title: "No hay stock disponible", icon: "error", confirmButtonText: "¡Cerrar!" });
        return;
      }

      // Render fila
      $(".nuevoProducto").append(
        '<div class="row" style="padding:5px 15px">'+
          '<div class="col-xs-6" style="padding-right:0px">'+
            '<div class="input-group">'+
              '<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarProducto" idProducto="'+idProducto+'"><i class="fa fa-times"></i></button></span>'+
              '<input type="text" class="form-control nuevaDescripcionProducto" idProducto="'+idProducto+'" name="agregarProducto" value="'+descripcion+'" readonly required>'+
            '</div>'+
          '</div>'+
          '<div class="col-xs-3">'+
            '<input type="number" class="form-control nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" value="1" stock="'+stock+'" nuevoStock="'+(stock-1)+'" required>'+
          '</div>'+
          '<div class="col-xs-3 ingresoPrecio" style="padding-left:0px">'+
            '<div class="input-group">'+
              '<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>'+
              '<input type="text" class="form-control nuevoPrecioProducto" '+
                'data-precio-normal="'+precioNormal+'" data-precio-mayor="'+precioMayor+'" '+
                'precioReal="'+precioNormal+'" name="nuevoPrecioProducto" value="'+precioNormal+'" readonly required>'+
            '</div>'+
            '<div class="mayor-toggle" style="margin-top:6px; display:flex; align-items:center; gap:8px;">'+
              '<label class="switch" title="Aplicar precio por mayor">'+
                '<input type="checkbox" class="chkMayor">'+
                '<span class="slider round"></span>'+
              '</label>'+
              '<span class="mayor-badge" style="display:none;">Mayorista</span>'+
            '</div>'+
          '</div>'+
        '</div>'
      );

      // Totales y formato
      sumarTotalPrecios();
      agregarImpuesto();
      listarProductos();
      $(".nuevoPrecioProducto").number(true, 2);

      // UI mayorista
      var $ultima = $(".nuevoProducto .row").last();
      actualizarUIMayorista($ultima);

      // Registrar en set y deshabilitar TODOS los botones de ese id - CORREGIDO
      productosSeleccionados.add(String(idProducto));
      deshabilitarBotonesAdd(idProducto);

      localStorage.removeItem("quitarProducto");
    },
    error:function(xhr,status,err){
      console.error('Error AJAX productos.ajax.php (idProducto):', status, err, xhr && xhr.responseText);
      Swal.fire({icon:'error', title:'Error', text:'No se pudo cargar el producto.'});
    },
    complete:function(){
      $btn.data('busy', false);
    }
  });
});

/*=============================================
TOGGLE MANUAL MAYORISTA (FUERA del click → sin duplicar bindings)
=============================================*/
$(document).on('change', '.chkMayor', function(){
  var $fila = $(this).closest('.row');
  var $precio = $fila.find('.nuevoPrecioProducto');
  var $inputCantidad = $fila.find('.nuevaCantidadProducto');

  var cantidad = n($inputCantidad.val() || 0);
  var pNormal  = n($precio.attr('data-precio-normal') || 0);
  var pMayor   = n($precio.attr('data-precio-mayor')   || 0);

  if ($(this).is(':checked') && !(pMayor>0)){
    $(this).prop('checked', false);
    Swal.fire({icon:'info', title:'Sin precio mayorista', text:'Este producto no tiene precio al por mayor.'});
    actualizarUIMayorista($fila);
    return;
  }

  var usarMayor = $(this).is(':checked') || (UMBRAL_MAYOR>0 && cantidad>=UMBRAL_MAYOR && pMayor>0);
  var unit = (usarMayor && pMayor>0) ? pMayor : pNormal;

  $precio.attr('precioReal', unit);
  $precio.val(cantidad * unit);

  sumarTotalPrecios();
  agregarImpuesto();
  listarProductos();
  actualizarUIMayorista($fila);
});

/*=============================================
CUANDO REDIBUJA LA TABLA
=============================================*/
$(".tablaVentas").on("draw.dt", function(){
  if(localStorage.getItem("quitarProducto") != null){
    var listaIdProductos = JSON.parse(localStorage.getItem("quitarProducto"));
    for(var i=0;i<listaIdProductos.length;i++){
      $("button.recuperarBoton[idProducto='"+listaIdProductos[i]["idProducto"]+"']")
        .removeClass('btn-default')
        .addClass('btn-primary agregarProducto');
    }
  }
  // Deshabilitar botones de productos ya en carrito
  registrarProductoEnSet();
  productosSeleccionados.forEach(function(id){ deshabilitarBotonesAdd(id); });
});

/*=============================================
QUITAR PRODUCTO DEL CARRITO
=============================================*/
var idQuitarProducto = [];
localStorage.removeItem("quitarProducto");

$(".formularioVenta").on("click", "button.quitarProducto", function(){
  var idProducto = $(this).attr("idProducto");
  $(this).closest('.row').remove();

  // LocalStorage para recuperar botón (compatibilidad con tu plantilla)
  if(localStorage.getItem("quitarProducto")==null){ idQuitarProducto=[]; }
  else { idQuitarProducto = JSON.parse(localStorage.getItem("quitarProducto")); }
  idQuitarProducto.push({"idProducto":idProducto});
  localStorage.setItem("quitarProducto", JSON.stringify(idQuitarProducto));

  // Rehabilitar botón(es) de ese producto
  habilitarBotonesAdd(idProducto);

  if($(".nuevoProducto").children().length == 0){
    $("#nuevoImpuestoVenta").val(0);
    $("#nuevoTotalVenta").val(0);
    $("#totalVenta").val(0);
    $("#nuevoTotalVenta").attr("total",0);
  }else{
    sumarTotalPrecios();
    agregarImpuesto();
    listarProductos();
  }

  // actualizar set
  productosSeleccionados.delete(String(idProducto));
});

/*=============================================
AGREGAR PRODUCTOS (MÓVIL) → SELECTOR
=============================================*/
var numProducto = 0;
$(".btnAgregarProducto").click(function(){
  numProducto ++;

  var datos = new FormData();
  datos.append("traerProductos", "ok");

  $.ajax({
    url:"ajax/productos.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType:"json",
    success:function(respuesta){
      if(!Array.isArray(respuesta)) return;

      $(".nuevoProducto").append(
        '<div class="row" style="padding:5px 15px">'+
          '<div class="col-xs-6" style="padding-right:0px">'+
            '<div class="input-group">'+
              '<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarProducto"><i class="fa fa-times"></i></button></span>'+
              '<select class="form-control nuevaDescripcionProducto" id="producto'+numProducto+'" name="nuevaDescripcionProducto" required>'+
                '<option value="">Seleccione el producto</option>'+
              '</select>'+
            '</div>'+
          '</div>'+
          '<div class="col-xs-3 ingresoCantidad">'+
            '<input type="number" class="form-control nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" value="0" stock nuevoStock required>'+
          '</div>'+
          '<div class="col-xs-3 ingresoPrecio" style="padding-left:0px">'+
            '<div class="input-group">'+
              '<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>'+
              '<input type="text" class="form-control nuevoPrecioProducto" precioReal="" name="nuevoPrecioProducto" readonly required>'+
            '</div>'+
            '<div class="mayor-toggle" style="margin-top:6px; display:flex; align-items:center; gap:8px;">'+
              '<label class="switch" title="Aplicar precio por mayor">'+
                '<input type="checkbox" class="chkMayor">'+
                '<span class="slider round"></span>'+
              '</label>'+
              '<span class="mayor-badge" style="display:none;">Mayorista</span>'+
            '</div>'+
          '</div>'+
        '</div>'
      );

      respuesta.forEach(function(item){
        if(item.stock != 0){
          $("#producto"+numProducto).append(
            '<option idProducto="'+item.id+'" value="'+(item.descripcion||'')+'">'+(item.descripcion||'')+'</option>'
          );
        }
      });

      sumarTotalPrecios();
      agregarImpuesto();
      $(".nuevoPrecioProducto").number(true,2);
    }
  });
});

/*=============================================
SELECCIONAR PRODUCTO DESDE SELECT (EVITA DUPLICADOS)
=============================================*/
$(".formularioVenta").on("change", "select.nuevaDescripcionProducto", function(){
  var nombreProducto = $(this).val();
  if(!nombreProducto){ return; }

  var $row = $(this).closest('.row');
  var nuevaDescripcionProducto = $row.find(".nuevaDescripcionProducto");
  var nuevoPrecioProducto      = $row.find(".nuevoPrecioProducto");
  var nuevaCantidadProducto    = $row.find(".nuevaCantidadProducto");

  var datos = new FormData();
  datos.append("nombreProducto", nombreProducto);

  $.ajax({
    url:"ajax/productos.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType:"json",
    success:function(respuesta){
      if(!respuesta){ Swal.fire({icon:'error', title:'Error', text:'Respuesta vacía del servidor'}); return; }

      var id = String(respuesta["id"]);

      // DUPLICADO: si ya existe, no lo agregues
      if(productosSeleccionados.has(id) || filaPorIdProducto(id).length){
        toastInfo('Ya agregado','Ese producto ya está en la venta.');
        $(".formularioVenta select.nuevaDescripcionProducto").val("");
        // limpiar fila temporal
        $row.remove();
        return;
      }

      $(nuevaDescripcionProducto).attr("idProducto", id);
      $(nuevaCantidadProducto).attr("stock", respuesta["stock"]);
      $(nuevaCantidadProducto).attr("nuevoStock", Number(respuesta["stock"])-1);

      var pNormal = n(respuesta["precio_venta"] || 0);
      var pMayor  = n(respuesta["precioMayor"]   || 0);

      $(nuevoPrecioProducto).val(pNormal);
      $(nuevoPrecioProducto).attr("precioReal", pNormal);
      $(nuevoPrecioProducto).attr("data-precio-normal", pNormal);
      $(nuevoPrecioProducto).attr("data-precio-mayor",  pMayor);

      // registrar, deshabilitar botones de ese producto
      productosSeleccionados.add(id);
      deshabilitarBotonesAdd(id);

      listarProductos();
      actualizarUIMayorista($row);
    }
  });
});

/*=============================================
MODIFICAR CANTIDAD
=============================================*/
$(".formularioVenta").on("change", "input.nuevaCantidadProducto", function(){

  var $row    = $(this).closest('.row');
  var $precio = $row.find(".nuevoPrecioProducto");
  var $chk    = $row.find(".chkMayor");

  var cantidad = n($(this).val() || 0);
  var pNormal  = n($precio.attr("data-precio-normal") || 0);
  var pMayor   = n($precio.attr("data-precio-mayor")   || 0);

  var usarMayor = ($chk.is(':checked')) || (UMBRAL_MAYOR>0 && cantidad>=UMBRAL_MAYOR && pMayor>0);
  var unit = (usarMayor && pMayor>0) ? pMayor : pNormal;

  $precio.attr('precioReal', unit);
  $precio.val(cantidad * unit);

  var nuevoStock = Number($(this).attr("stock")) - cantidad;
  $(this).attr("nuevoStock", nuevoStock);

  if(cantidad > Number($(this).attr("stock"))){
    $(this).val(0);
    $(this).attr("nuevoStock", $(this).attr("stock"));
    $precio.val(0);
    sumarTotalPrecios();
    Swal.fire({ title: "La cantidad supera el Stock", text: "¡Sólo hay "+$(this).attr("stock")+" unidades!", icon: "error", confirmButtonText: "¡Cerrar!" });
    return;
  }

  sumarTotalPrecios();
  agregarImpuesto();
  listarProductos();
  actualizarUIMayorista($row);
});

/*=============================================
SUMAR TODOS LOS PRECIOS
=============================================*/
function sumarTotalPrecios(){
  var arraySuma = [];
  $(".nuevoPrecioProducto").each(function(){ arraySuma.push(n($(this).val())); });
  var suma = arraySuma.reduce(function(a,b){ return a+b; }, 0);
  $("#nuevoTotalVenta").val(suma);
  $("#totalVenta").val(suma);
  $("#nuevoTotalVenta").attr("total", suma);
}

/*=============================================
FUNCIÓN AGREGAR IMPUESTO
=============================================*/
function agregarImpuesto(){
  var impuesto    = n($("#nuevoImpuestoVenta").val());
  var precioTotal = n($("#nuevoTotalVenta").attr("total"));
  var precioImp   = precioTotal * impuesto/100;
  var totalConImp = precioImp + precioTotal;

  $("#nuevoTotalVenta").val(totalConImp);
  $("#totalVenta").val(totalConImp);
  $("#nuevoPrecioImpuesto").val(precioImp);
  $("#nuevoPrecioNeto").val(precioTotal);
}

/*=============================================
CAMBIO DE IMPUESTO
=============================================*/
$("#nuevoImpuestoVenta").change(agregarImpuesto);

/*=============================================
FORMATO AL PRECIO FINAL
=============================================*/
$("#nuevoTotalVenta").number(true, 2);

/*=============================================
MÉTODO DE PAGO (sin cambios de lógica)
=============================================*/
$("#nuevoMetodoPago").change(function(){
  var metodo = $(this).val();
  if(metodo == "Efectivo"){
    $(this).parent().parent().removeClass("col-xs-6").addClass("col-xs-4");
    $(this).parent().parent().parent().children(".cajasMetodoPago").html(
      '<div class="col-xs-4">'+
        '<div class="input-group">'+
          '<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>'+
          '<input type="text" class="form-control" id="nuevoValorEfectivo" placeholder="000000" required>'+
        '</div>'+
      '</div>'+
      '<div class="col-xs-4" id="capturarCambioEfectivo" style="padding-left:0px">'+
        '<div class="input-group">'+
          '<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>'+
          '<input type="text" class="form-control" id="nuevoCambioEfectivo" placeholder="000000" readonly required>'+
        '</div>'+
      '</div>'
    );
    $('#nuevoValorEfectivo').number(true, 2);
    $('#nuevoCambioEfectivo').number(true, 2);
    listarMetodos();
  }else{
    $(this).parent().parent().removeClass('col-xs-4').addClass('col-xs-6');
    $(this).parent().parent().parent().children('.cajasMetodoPago').html(
      '<div class="col-xs-6" style="padding-left:0px">'+
        '<div class="input-group">'+
          '<input type="number" min="0" class="form-control" id="nuevoCodigoTransaccion" placeholder="Código transacción" required>'+
          '<span class="input-group-addon"><i class="fa fa-lock"></i></span>'+
        '</div>'+
      '</div>'
    );
  }
});

$(".formularioVenta").on("change", "input#nuevoValorEfectivo", function(){
  var efectivo = n($(this).val());
  var cambio = efectivo - n($('#nuevoTotalVenta').val());
  var nuevoCambioEfectivo = $(this).closest('.row').find('#nuevoCambioEfectivo');
  nuevoCambioEfectivo.val(cambio);
});

$(".formularioVenta").on("change", "input#nuevoCodigoTransaccion", listarMetodos);

/*=============================================
LISTAR PRODUCTOS EN INPUT OCULTO
=============================================*/
function listarProductos(){
  var lista = [];
  var descripcion = $(".nuevaDescripcionProducto");
  var cantidad    = $(".nuevaCantidadProducto");
  var precio      = $(".nuevoPrecioProducto");

  for(var i=0;i<descripcion.length;i++){
    lista.push({
      "id": $(descripcion[i]).attr("idProducto"),
      "descripcion": $(descripcion[i]).val(),
      "cantidad": $(cantidad[i]).val(),
      "stock": $(cantidad[i]).attr("nuevoStock"),
      "precio": $(precio[i]).attr("precioReal"),
      "total": $(precio[i]).val()
    });
  }
  $("#listaProductos").val(JSON.stringify(lista));
}

/*=============================================
LISTAR MÉTODO DE PAGO
=============================================*/
function listarMetodos(){
  if($("#nuevoMetodoPago").val()=="Efectivo"){
    $("#listaMetodoPago").val("Efectivo");
  }else{
    $("#listaMetodoPago").val($("#nuevoMetodoPago").val()+"-"+$("#nuevoCodigoTransaccion").val());
  }
}

/*=============================================
EDITAR / ELIMINAR / IMPRIMIR / RANGO FECHAS — SIN CAMBIOS
=============================================*/
$(".tablas").on("click", ".btnEditarVenta", function(){
  var idVenta = $(this).attr("idVenta");
  window.location = "index.php?ruta=editar-venta&idVenta="+idVenta;
});

$('.tablaVentas').on('draw.dt', function(){ quitarAgregarProducto(); });

function quitarAgregarProducto(){
  var idProductos = $(".quitarProducto");
  var botonesTabla = $(".tablaVentas tbody button.agregarProducto");
  for(var i=0;i<idProductos.length;i++){
    var id = $(idProductos[i]).attr("idProducto");
    for(var j=0;j<botonesTabla.length;j++){
      if($(botonesTabla[j]).attr("idProducto")==id){
        $(botonesTabla[j]).removeClass("btn-primary agregarProducto").addClass("btn-default");
      }
    }
  }
}

$(".tablas").on("click", ".btnEliminarVenta", function(){
  var idVenta = $(this).attr("idVenta");
  Swal.fire({
    title: '¿Está seguro de borrar la venta?',
    text: "¡Si no lo está puede cancelar la acción!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Si, borrar venta!'
  }).then(function(result){
    if (result.value) window.location = "index.php?ruta=ventas&idVenta="+idVenta;
  })
});

$(".tablas").on("click", ".btnImprimirFactura", function(){
  var codigoVenta = $(this).attr("codigoVenta");
  window.open("extensiones/tcpdf/pdf/factura.php?codigo="+codigoVenta, "_blank");
});
$(".tablas").on("click", ".btnImprimirBoleta", function(){
  var codigoVenta = $(this).attr("codigoVenta");
  window.open("extensiones/tcpdf/pdf/boleta.php?codigo="+codigoVenta, "_blank");
});

$('#daterange-btn').daterangepicker(
  {
    ranges: {
      'Hoy': [moment(), moment()],
      'Ayer': [moment().subtract(1,'days'), moment().subtract(1,'days')],
      'Últimos 7 días': [moment().subtract(6,'days'), moment()],
      'Últimos 30 días': [moment().subtract(29,'days'), moment()],
      'Este mes': [moment().startOf('month'), moment().endOf('month')],
      'Último mes': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
    },
    startDate: moment(),
    endDate: moment()
  },
  function (start, end) {
    $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    var fechaInicial = start.format('YYYY-MM-DD');
    var fechaFinal   = end.format('YYYY-MM-DD');
    var capturarRango = $("#daterange-btn span").html();
    localStorage.setItem("capturarRango", capturarRango);
    window.location = "index.php?ruta=ventas&fechaInicial="+fechaInicial+"&fechaFinal="+fechaFinal;
  }
);

$(".daterangepicker.opensleft .range_inputs .cancelBtn").on("click", function(){
  localStorage.removeItem("capturarRango");
  localStorage.clear();
  window.location = "ventas";
});

$(".daterangepicker.opensleft .ranges li").on("click", function(){
  var textoHoy = $(this).attr("data-range-key");
  if(textoHoy == "Hoy"){
    var d=new Date(), dia=d.getDate(), mes=d.getMonth()+1, anio=d.getFullYear();
    var fechaInicial = anio+"-"+(mes<10?"0"+mes:mes)+"-"+(dia<10?"0"+dia:dia);
    var fechaFinal   = fechaInicial;
    localStorage.setItem("capturarRango", "Hoy");
    window.location = "index.php?ruta=ventas&fechaInicial="+fechaInicial+"&fechaFinal="+fechaFinal;
  }
});

/*=============================================
ASIGNAR CLIENTE FICTICIO SI NO SE SELECCIONA CLIENTE
=============================================*/
// Al enviar el formulario de venta, si el usuario no ha seleccionado un cliente en el
// listado, se utilizará el cliente ficticio (con DNI 00000000) cuyo ID está en el
// campo oculto #defaultClienteId.  Esto evita errores por cliente vacío y permite
// procesar la venta correctamente.
$(document).ready(function(){
  $(".formularioVenta").on("submit", function(){
    var $clienteSelect = $("#seleccionarCliente");
    if($clienteSelect.length && $clienteSelect.val() === ""){
      var defaultId = $("#defaultClienteId").val();
      if(defaultId){
        $clienteSelect.val(defaultId);
      }
    }
  });
});

/*=============================================
DISPARAR EVENTO POR DEFECTO PARA MÉTODO DE PAGO
=============================================*/
// Si el método de pago ya está preseleccionado (por ejemplo, "Efectivo"),
// disparamos manualmente el evento change para que se generen los campos
// correspondientes (monto entregado y cambio) y se actualice la lista
// de métodos de pago. Esto asegura que el formulario muestre los campos
// correctos desde el principio, sin que el usuario tenga que interactuar.
$(document).ready(function(){
  var $metodoPago = $("#nuevoMetodoPago");
  if($metodoPago.length && $metodoPago.val() === "Efectivo"){
    $metodoPago.trigger('change');
  }
});

/*=============================================
BUSCAR CLIENTE POR DNI
=============================================*/
// Este bloque permite buscar un cliente por su DNI en lugar de usar el select
// desplegable. Al hacer clic en el botón de búsqueda, se envía una solicitud
// AJAX a clientes.ajax.php con el parámetro 'buscarClienteDni'.  Si el
// cliente existe, se carga el nombre en el input #nombreCliente y se
// establece el valor del input oculto #seleccionarCliente con el ID
// correspondiente.  Si no se encuentra, se informa al usuario.

$(document).on('click', '.btnBuscarCliente', function(){
  var dni = $('#buscarClienteDni').val().trim();
  if(dni === ''){
    // Si no se ingresa un DNI, no hacer nada
    Swal.fire({
      icon: 'warning',
      title: 'Ingrese DNI',
      text: 'Por favor ingrese el DNI del cliente para buscar.'
    });
    return;
  }
  // Llamada AJAX para buscar el cliente por DNI
  $.ajax({
    url: 'ajax/clientes.ajax.php',
    method: 'POST',
    data: { buscarClienteDni: dni },
    dataType: 'json',
    success: function(respuesta){
      if(respuesta){
        // Cliente encontrado: cargar nombre e ID
        $('#nombreCliente').val(respuesta.nombre || respuesta.nombre_completo || '');
        // Algunos sistemas devuelven "nombre" como clave, otros "nombre_completo".
        // Se intenta ambos por compatibilidad.
        $('#seleccionarCliente').val(respuesta.id);
      } else {
        // Cliente no encontrado: informar y limpiar campos
        $('#nombreCliente').val('');
        $('#seleccionarCliente').val('');
        Swal.fire({
          icon: 'error',
          title: 'Cliente no encontrado',
          text: 'No se encontró un cliente con el DNI ingresado. Verifique y vuelva a intentar o registre uno nuevo.'
        });
      }
    },
    error: function(xhr, status, error){
      console.error('Error al buscar cliente por DNI:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error de búsqueda',
        text: 'Ocurrió un error al buscar el cliente. Intente nuevamente más tarde.'
      });
    }
  });
});

// Permitir buscar al presionar Enter en el campo de DNI
$(document).on('keypress', '#buscarClienteDni', function(e){
  if(e.which === 13){
    e.preventDefault();
    $('.btnBuscarCliente').click();
  }
});
