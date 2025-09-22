<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>Crear venta</h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Crear venta</li>
    </ol>
  </section>

  <section class="content">

    <div class="row">

      <!--=====================================
      EL FORMULARIO
      ======================================-->
      <div class="col-lg-5 col-xs-12">
        <div class="box box-success">
          <div class="box-header with-border"></div>

          <form role="form" method="post" class="formularioVenta">
            <div class="box-body">
              <div class="box">

                <!--=====================================
                ENTRADA DEL VENDEDOR
                ======================================-->
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" class="form-control" id="nuevoVendedor" value="<?php echo $_SESSION["nombre"]; ?>" readonly>
                    <input type="hidden" name="idVendedor" value="<?php echo $_SESSION["id"]; ?>">
                  </div>
                </div>

                <!--=====================================
                ENTRADA DEL CÓDIGO
                ======================================-->
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                    <?php
                      $item = null; $valor = null;
                      $ventas = ControladorVentas::ctrMostrarVentas($item, $valor);
                      if(!$ventas){
                        echo '<input type="text" class="form-control" id="nuevaVenta" name="nuevaVenta" value="10001" readonly>';
                      }else{
                        foreach ($ventas as $key => $value) { $codigo = $value["codigo"]; }
                        $codigoVenta = $codigo + 1;
                        echo '<input type="text" class="form-control" id="nuevaVenta" name="nuevaVenta" value="'.$codigoVenta.'" readonly>';
                      }
                    ?>
                  </div>
                </div>

                <!--=====================================
                BUSCAR / CREAR CLIENTE POR DNI o RUC
                (Reemplaza al select + botón Agregar cliente)
                ======================================-->
                <style>
                  :root{
    --cv-primary:#0ea5e9;
    --cv-primary-600:#0284c7;
    --cv-surface:#ffffff;
    --cv-border:#e6eef5;
    --cv-muted:#64748b;
    --cv-ring:0 0 0 3px rgba(14,165,233,.25);
  }
  .cv-card{
    background:var(--cv-surface);
    border:1px solid var(--cv-border);
    border-radius:14px;
    padding:16px;
    box-shadow:0 6px 20px rgba(2,8,20,.05);
  }
  .cv-title{
    display:flex;align-items:center;gap:8px;
    font-weight:600;margin-bottom:10px;
  }
  .cv-title i{ color:var(--cv-primary-600); }
  .cv-input-group .input-group-addon{
    background:#f4f7fb;border-right:0;border-color:var(--cv-border);
  }
  .cv-input-group .form-control{
    border-left:0;border-color:var(--cv-border);
    transition:border-color .15s, box-shadow .15s;
  }
  .cv-input-group .form-control:focus{
    border-color:var(--cv-primary); box-shadow:var(--cv-ring);
  }
  .cv-btn{
    border-radius:10px;
    background:var(--cv-primary);border-color:var(--cv-primary);color:#fff;
    transition:transform .12s ease, background .2s;
  }
  .cv-btn:hover{ background:var(--cv-primary-600); transform:translateY(-1px); }
  .cv-chip{
    font-weight:600;color:var(--cv-primary-600);
    background:rgba(14,165,233,.12);
    padding:2px 10px;border-radius:999px;
  }
  .cv-muted{ color:var(--cv-muted); }
  .mt-10{ margin-top:10px; }
  @media (max-width:480px){
    #tipoDocVenta{ max-width:110px; }
    .cv-card{ padding:12px; }
  }
                </style>

                <div class="form-group">
                  <label class="control-label"><i class="fa fa-users"></i> Buscar Cliente por Documento</label>
                  <div class="cv-card">
                    <div class="input-group cv-input-group" id="buscadorClienteVenta">
                      <span class="input-group-addon">
                        <!-- selector tipo -->
                        <select id="tipoDocVenta" style="border:none;background:transparent;">
                          <option value="DNI" selected>DNI</option>
                          <option value="RUC">RUC</option>
                        </select>
                      </span>
                      <!-- documento -->
                      <input type="text" class="form-control" id="docClienteVenta" placeholder="Ingresar DNI" maxlength="8" autocomplete="off">
                      <span class="input-group-addon">
                        <button type="button" class="btn btn-info btn-xs cv-btn" id="btnBuscarClienteVenta">
                          <i class="fa fa-search" id="icoBuscarVenta"></i> Buscar
                        </button>
                      </span>
                    </div>
                    <small class="text-muted">
                      <span id="chipTipoDoc" class="cv-chip">DNI</span> &nbsp;
                      Ingrese el DNI/RUC y presione buscar para cargar o registrar al cliente.
                    </small>

                    <!-- Nombre mostrado (en lugar del select) -->
                    <div class="input-group" style="margin-top:10px;">
                      <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      <input type="text" id="nombreCliente" class="form-control" placeholder="Nombre del cliente" readonly>
                    </div>

                    <!-- id del cliente seleccionado (hidden con el MISMO name que usabas en el select) -->
                    <input type="hidden" id="seleccionarCliente" name="seleccionarCliente">
                  </div>
                </div>

                <!--=====================================
                ENTRADA PARA AGREGAR PRODUCTO
                ======================================-->
                <div class="form-group row nuevoProducto"></div>
                <input type="hidden" id="listaProductos" name="listaProductos">

                <!--=====================================
                BOTÓN PARA AGREGAR PRODUCTO (mobile)
                ======================================-->
                <button type="button" class="btn btn-default hidden-lg btnAgregarProducto">Agregar producto</button>

                <hr>

                <div class="row">
                  <!--=====================================
                  ENTRADA IMPUESTOS Y TOTAL
                  ======================================-->
                  <div class="col-xs-8 pull-right">
                    <table class="table">
                      <thead>
                        <tr><th>Impuesto</th><th>Total</th></tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td style="width:50%">
                            <div class="input-group">
                              <input type="number" class="form-control input-lg" min="0" id="nuevoImpuestoVenta" name="nuevoImpuestoVenta" placeholder="0" required>
                              <input type="hidden" name="nuevoPrecioImpuesto" id="nuevoPrecioImpuesto" required>
                              <input type="hidden" name="nuevoPrecioNeto" id="nuevoPrecioNeto" required>
                              <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                            </div>
                          </td>
                          <td style="width:50%">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
                              <input type="text" class="form-control input-lg" id="nuevoTotalVenta" name="nuevoTotalVenta" total="" placeholder="00000" readonly required>
                              <input type="hidden" name="totalVenta" id="totalVenta">
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <hr>

                <!--=====================================
                ENTRADA MÉTODO DE PAGO
                ======================================-->
                <div class="form-group row">
                  <div class="col-xs-6" style="padding-right:0px">
                    <div class="input-group">
                      <select class="form-control" id="nuevoMetodoPago" name="nuevoMetodoPago" required>
                        <option value="">Seleccione método de pago</option>
                        <option value="Efectivo" selected>Efectivo</option>
                        <option value="TC">Tarjeta Crédito</option>
                        <option value="TD">Tarjeta Débito</option>
                      </select>
                    </div>
                  </div>
                  <div class="cajasMetodoPago"></div>
                  <input type="hidden" id="listaMetodoPago" name="listaMetodoPago">
                </div>

                <!--=====================================
                TIPO DE COMPROBANTE
                ======================================-->
                <div class="form-group row">
                  <div class="col-xs-12">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
                      <select class="form-control" id="tipoComprobante" name="tipoComprobante" required>
                        <option value="">Seleccione tipo de comprobante</option>
                        <option value="boleta">Boleta</option>
                        <option value="factura">Factura</option>
                        <option value="ticket">Ticket</option>
                      </select>
                    </div>
                  </div>
                </div>

                <br>

              </div> <!-- .box -->
            </div> <!-- .box-body -->

            <div class="box-footer">
              <button type="submit" class="btn btn-primary pull-right">Guardar venta</button>
            </div>

          </form>

          <?php
            $crearVenta = new ControladorVentas();
            $crearVenta -> ctrCrearVenta();
          ?>

        </div>
      </div>

      <!--=====================================
      LA TABLA DE PRODUCTOS
      ======================================-->
      <div class="col-lg-7 hidden-md hidden-sm hidden-xs">
        <div class="box box-warning">
          <div class="box-header with-border"></div>
          <div class="box-body">
            <table class="table table-bordered table-striped dt-responsive tablaVentas">
              <thead>
                <tr>
                  <th style="width:10px">#</th>
                  <th>Imagen</th>
                  <th>Código</th>
                  <th>Descripcion</th>
                  <th>Stock</th>
                  <th>Acciones</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>

    </div> <!-- .row -->
  </section>
</div>

<!-- (El modal “Agregar cliente” lo dejamos por compatibilidad, pero ya no se usa en esta vista) -->
<div id="modalAgregarCliente" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post">
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar cliente</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoCliente" placeholder="Ingresar nombre" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                <input type="number" min="0" class="form-control input-lg" name="nuevoDocumentoId" placeholder="Ingresar documento" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="email" class="form-control input-lg" name="nuevoEmail" placeholder="Ingresar email" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar teléfono" data-inputmask="'mask':'(999) 999-9999'" data-mask required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                <input type="text" class="form-control input-lg" name="nuevaDireccion" placeholder="Ingresar dirección" required>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" class="form-control input-lg" name="nuevaFechaNacimiento" placeholder="Ingresar fecha nacimiento" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar cliente</button>
        </div>
      </form>
      <?php
        $crearCliente = new ControladorClientes();
        $crearCliente -> ctrCrearCliente();
      ?>
    </div>
  </div>
</div>

<!--=====================================
SCRIPTS NECESARIOS
======================================-->
<script src="vistas/js/ventas.js"></script>

<script>
/* =========================================================================
   BUSCAR / CREAR CLIENTE POR DNI O RUC (Ventas)
   - Busca en BD; si no existe, consulta consultadni.php / consultaruc.php
   - Crea automáticamente al cliente y lo selecciona
   - Muestra aviso 5s
============================================================================ */
(function(){

  const $tipo = $("#tipoDocVenta");
  const $doc  = $("#docClienteVenta");
  const $nom  = $("#nombreCliente");
  const $id   = $("#seleccionarCliente");
  const $ico  = $("#icoBuscarVenta");
  const $chip = $("#chipTipoDoc");

  function setPlaceholder(){ $doc.attr("placeholder", $tipo.val()==="RUC" ? "Ingresar RUC" : "Ingresar DNI"); }
  function setMaxlength(){   $doc.attr("maxlength", $tipo.val()==="RUC" ? "11" : "8"); }
  function setChip(){ $chip.text($tipo.val()); }

  // UI
  $tipo.on("change", function(){ setPlaceholder(); setMaxlength(); setChip(); $doc.val("").focus(); $nom.val(""); $id.val(""); });
  $doc.on("input", function(){ $nom.val(""); $id.val(""); });
  $("#btnBuscarClienteVenta").on("click", buscarClientePorDocumentoVenta);
  $doc.on("keydown", function(e){ if(e.key === "Enter"){ e.preventDefault(); buscarClientePorDocumentoVenta(); } });

  function toggleLoading(on){
    if(on){ $ico.removeClass("fa-search").addClass("fa-spinner fa-spin"); }
    else  { $ico.removeClass("fa-spinner fa-spin").addClass("fa-search"); }
  }

  function validar(){
    const tipo = ($tipo.val() || "").trim();
    const doc  = ($doc.val()   || "").trim();
    if(!doc){ Swal.fire({icon:"info", title:"Ingrese un documento"}); return false; }
    if(tipo==="DNI" && doc.length!==8){ Swal.fire({icon:"warning", title:"DNI inválido", text:"Debe tener 8 dígitos."}); return false; }
    if(tipo==="RUC" && doc.length!==11){ Swal.fire({icon:"warning", title:"RUC inválido", text:"Debe tener 11 dígitos."}); return false; }
    return true;
  }

  // Paso 1: buscar en BD
  function buscarClientePorDocumentoVenta(){
    if(!validar()) return;

    const tipo = $tipo.val();
    const doc  = $doc.val().trim();

    toggleLoading(true);

    $.ajax({
      url: "ajax/clientes.ajax.php",
      method: "POST",
      data: { accion: "buscarPorDocumento", tipoDocumento: tipo, documento: doc },
      dataType: "json"
    }).done(function(res){
      if(res && res.ok){
        // encontrado en BD
        $id.val(res.id);
        $nom.val(res.nombre || "");
        Swal.fire({toast:true, position:"top-end", icon:"success", title:"Cliente cargado", timer:2000, showConfirmButton:false});
      }else{
        // no existe → consultar RENIEC / SUNAT y crear
        consultarApiYCrear(tipo, doc);
      }
    }).fail(function(){
      Swal.fire({icon:"error", title:"Error de conexión"});
    }).always(function(){
      toggleLoading(false);
    });
  }

  // Paso 2: API RENIEC/SUNAT y creación automática
  function consultarApiYCrear(tipo, doc){
    const urlApi = (tipo === "RUC") ? "ajax/consultaruc.php" : "ajax/consultadni.php";

    toggleLoading(true);

    $.post(urlApi, { documento: doc }).done(function(data){
      let res;
      try { res = (typeof data === "string") ? JSON.parse(data) : data; } catch(e){ res = null; }

      if(!res || String(res.codigo) !== "1"){
        Swal.fire({icon:"info", title: (res && res.mensaje) ? res.mensaje : "No se encontró información externa."});
        return;
      }

      // Mapear nombre/dirección
      const nombre = (tipo === "RUC"
                      ? (res.data.razon_social || res.data.nombre || "")
                      : (res.data.nombre || "")
                     ).toString().trim();

      const direccion = (res.data.direccion ||
                         res.data.direccion_completa ||
                         res.data.domicilio_fiscal ||
                         "").toString().trim();

      if(!nombre){
        Swal.fire({icon:"info", title:"No se obtuvo nombre/razón social desde la API"});
        return;
      }

      // Crear cliente rápidamente en BD
      $.ajax({
        url: "ajax/clientes.ajax.php",
        method: "POST",
        data: {
          accion: "crearRapido",
          nombre: nombre,
          tipo_documento: tipo,
          documento: doc,
          direccion: direccion
        },
        dataType: "json"
      }).done(function(r){
        if(r && r.ok){
          $id.val(r.id);
          $nom.val(nombre);
          Swal.fire({
            icon:"success",
            title:"¡Cliente registrado!",
            text:"Se creó y seleccionó automáticamente.",
            timer: 5000,
            timerProgressBar: true,
            showConfirmButton: false
          });
        }else{
          Swal.fire({icon:"error", title: (r && r.mensaje) ? r.mensaje : "No se pudo crear el cliente"});
        }
      }).fail(function(){
        Swal.fire({icon:"error", title:"Error al crear el cliente"});
      });

    }).fail(function(){
      Swal.fire({icon:"error", title:"Error al consultar el documento"});
    }).always(function(){
      toggleLoading(false);
    });
  }

  // init
  setPlaceholder(); setMaxlength(); setChip();

})();
</script>

<script>
// Configurar efectivo como método por defecto al cargar la página
$(document).ready(function(){
  $('#nuevoMetodoPago').val('Efectivo').trigger('change');
});
</script>
