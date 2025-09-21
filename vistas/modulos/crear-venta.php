<?php

if($_SESSION["perfil"] == "Especial"){

  echo '<script>

    window.location = "inicio";

  </script>';

  return;

}

?>

<style>
/* Estilos modernos para el POS estilo Odoo */
.pos-container {
  background: #f8f9fa;
  min-height: calc(100vh - 120px);
}

.pos-header {
  background: #2c3e50;
  color: white;
  padding: 20px;
  margin-bottom: 0;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.pos-header h1 {
  margin: 0;
  font-size: 28px;
  font-weight: 300;
}

.pos-header .breadcrumb {
  background: transparent;
  margin: 10px 0 0 0;
  padding: 0;
}

.pos-header .breadcrumb a {
  color: rgba(255,255,255,0.8);
}

.pos-header .breadcrumb a:hover {
  color: white;
}

.pos-header .breadcrumb .active {
  color: white;
}

/* Panel izquierdo mejorado */
.pos-left-panel {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  overflow: hidden;
}

.pos-left-header {
  background: #34495e;
  color: white;
  padding: 15px 20px;
  font-weight: 600;
  font-size: 16px;
  border-bottom: 1px solid #2c3e50;
}

.pos-left-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 500;
}

.pos-left-body {
  padding: 20px;
}

/* Formulario mejorado */
.form-group {
  margin-bottom: 20px;
}

.input-group {
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  border-radius: 8px;
  overflow: hidden;
}

.input-group-addon {
  background: #ecf0f1;
  border: 1px solid #bdc3c7;
  color: #7f8c8d;
  font-weight: 500;
}

.form-control {
  border: 1px solid #bdc3c7;
  border-radius: 6px;
  padding: 12px 15px;
  font-size: 14px;
  transition: all 0.3s;
  background: white;
}

.form-control:focus {
  border-color: #3498db;
  box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

/* Productos en el carrito */
.nuevoProducto .row {
  background: #ffffff;
  border-radius: 10px;
  margin-bottom: 12px;
  padding: 12px 14px;
  border: 1px solid #e9ecef;
  transition: all 0.25s ease;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.nuevoProducto .row:hover {
  background: #f7fbff;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

/* Marca visual cuando aplica mayorista */
.nuevoProducto .row.aplica-mayor {
  border-left: 4px solid #2ecc71;
  background: #f8fff9;
}

/* Badge mayorista */
.mayor-badge {
  background: #eafaf1;
  color: #27ae60;
  border: 1px solid #c6f0d9;
  padding: 2px 8px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
}

/* Switch moderno (reutiliza clases existentes) */
.switch { position: relative; display: inline-block; width: 42px; height: 22px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #dfe6e9; transition: .2s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 2px; bottom: 2px; background-color: white; transition: .2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
input:checked + .slider { background-color: #27ae60; }
input:focus + .slider { box-shadow: 0 0 1px #27ae60; }
input:checked + .slider:before { transform: translateX(20px); }
.slider.round { border-radius: 22px; }
.slider.round:before { border-radius: 50%; }

/* Inputs más compactos en línea */
.nuevoProducto .form-control { height: 36px; padding: 6px 10px; }
.nuevoProducto .input-group-addon { height: 36px; padding: 6px 10px; }

/* Totales destacados */
#nuevoTotalVenta { font-size: 20px; font-weight: 700; color: #2c3e50; }
.table thead th i { margin-right: 6px; }

/* Resumen sticky */
.summary-sticky { position: sticky; top: 10px; }
.summary-card { background: #fff; border: 1px solid #e9ecef; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 10px; }

/* Miniatura en línea del carrito */
.line-thumb { width: 38px; height: 38px; border-radius: 6px; object-fit: cover; border: 1px solid #e9ecef; }

/* Tabla de totales mejorada */
.table {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.table thead th {
  background: #2c3e50;
  color: white;
  border: none;
  padding: 15px;
  font-weight: 600;
}

.table tbody td {
  padding: 15px;
  border: none;
  border-bottom: 1px solid #f8f9fa;
}

/* Botones mejorados */
.btn {
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 500;
  transition: all 0.3s;
  border: none;
}

.btn-primary {
  background: #3498db;
  box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
}

.btn-primary:hover {
  background: #2980b9;
  transform: translateY(-1px);
  box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
}

.btn-success {
  background: #27ae60;
  box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
}

.btn-warning {
  background: linear-gradient(135deg, #ffc107, #e0a800);
  box-shadow: 0 4px 15px rgba(255,193,7,0.3);
}

.btn-danger {
  background: linear-gradient(135deg, #dc3545, #c82333);
  box-shadow: 0 4px 15px rgba(220,53,69,0.3);
}

.btn-default {
  background: #6c757d;
  color: white;
  box-shadow: 0 4px 15px rgba(108,117,125,0.3);
}

.btn-default:hover {
  background: #5a6268;
  color: white;
}

/* Panel derecho mejorado */
.pos-right-panel {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  overflow: hidden;
}

.pos-right-header {
  background: #34495e;
  color: white;
  padding: 15px 20px;
  font-weight: 600;
  font-size: 16px;
  border-bottom: 1px solid #2c3e50;
}

.pos-right-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 500;
}

.pos-right-body {
  padding: 20px;
}

/* Tabla de productos mejorada */
.tablaVentas {
  border-radius: 8px;
  overflow: hidden;
}

.tablaVentas thead th {
  background: #ecf0f1;
  color: #2c3e50;
  border: none;
  padding: 15px 10px;
  font-weight: 600;
  text-align: center;
  text-transform: uppercase;
  font-size: 12px;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #bdc3c7;
}

.tablaVentas tbody td {
  padding: 12px 10px;
  border: none;
  border-bottom: 1px solid #f8f9fa;
  text-align: center;
  vertical-align: middle;
}

.tablaVentas tbody tr:hover {
  background: #e8f4f8;
  transition: all 0.3s ease;
}

/* Imágenes de productos */
.tablaVentas img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e9ecef;
}

/* Responsive mejorado */
@media (max-width: 768px) {
  .pos-left-panel {
    margin-bottom: 20px;
  }
  
  .pos-header h1 {
    font-size: 24px;
  }
  
  .form-control {
    font-size: 16px; /* Evita zoom en iOS */
  }
}

/* Animaciones */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.pos-left-panel, .pos-right-panel {
  animation: fadeInUp 0.6s ease-out;
}

/* Estados de carga */
.loading {
  opacity: 0.6;
  pointer-events: none;
}

/* Mejoras en los selects */
.Select-cliente, select.form-control {
  width: 100% !important;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  padding: 8px 12px;
  font-size: 14px;
  color: #333;
  transition: all 0.3s;
}

.Select-cliente:focus, select.form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
  outline: none;
}

/* Mejorar apariencia de los selects */
select.form-control {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 16px;
  padding-right: 40px;
}

/* Estilos para el modal de productos */
.modal-body .form-group label {
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
  display: block;
}

.modal-body .input-group {
  margin-bottom: 0;
}

.modal-body .form-control {
  border-radius: 4px;
  border: 1px solid #dee2e6;
  padding: 10px 12px;
  font-size: 14px;
  transition: all 0.3s;
}

.modal-body .form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.modal-body .input-group-addon {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  color: #6c757d;
  font-weight: 500;
  border-radius: 4px 0 0 4px;
}

.modal-body .input-group .form-control {
  border-radius: 0 4px 4px 0;
  border-left: none;
}

.modal-body .input-group .form-control:focus {
  border-left: 1px solid #007bff;
}

/* Estilos para el modal */
.modal-content {
  border-radius: 12px;
  border: none;
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-header {
  border-radius: 12px 12px 0 0;
  border-bottom: none;
}

.modal-footer {
  border-radius: 0 0 12px 12px;
  border-top: none;
}

/* Grid de productos estilo supermercado */
.products-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
.view-toggle .btn {
  margin-left: 8px;
}
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 15px;
}
.product-card {
  background: #fff;
  border: 1px solid #e9ecef;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.06);
  padding: 12px;
  display: flex;
  flex-direction: column;
  transition: transform .2s ease, box-shadow .2s ease;
}
.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}
.product-thumb {
  width: 100%;
  height: 120px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #f1f3f5;
}
.product-title {
  margin: 10px 0 4px 0;
  font-size: 14px;
  font-weight: 600;
  color: #343a40;
}
.product-meta {
  font-size: 12px;
  color: #6c757d;
}
.product-actions {
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.price-badge {
  background: #e8f4f8;
  color: #0d6efd;
  padding: 6px 10px;
  border-radius: 6px;
  font-weight: 600;
}
.stock-badge {
  font-size: 12px;
  color: #198754;
}

/* Keypad estilo POS */
/* keypad removido */

</style>

<div class="content-wrapper pos-container">

  <section class="content-header pos-header">
    
    <h1>
      <i class="fa fa-shopping-cart"></i> Crear Venta
    </h1>

    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Crear venta</li>
    </ol>

  </section>

  <section class="content">

    <div class="row">

      <!--=====================================
      EL FORMULARIO MEJORADO
      ======================================-->
      
      <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
        
        <div class="pos-left-panel">
          
          <div class="pos-left-header">
            <h3><i class="fa fa-shopping-cart"></i> Carrito de Ventas</h3>
          </div>

          <div class="pos-left-body">

            <form role="form" method="post" class="formularioVenta">

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

                  $item = null;
                  $valor = null;

                  $ventas = ControladorVentas::ctrMostrarVentas($item, $valor);

                  if(!$ventas){

                    echo '<input type="text" class="form-control" id="nuevaVenta" name="nuevaVenta" value="10001" readonly>';
                

                  }else{

                    foreach ($ventas as $key => $value) {
                      
                      
                    
                    }

                    $codigo = $value["codigo"] + 1;



                    echo '<input type="text" class="form-control" id="nuevaVenta" name="nuevaVenta" value="'.$codigo.'" readonly>';
                

                  }

                  ?>
                  
                  
                </div>
              
              </div>

              <!--=====================================
              ENTRADA DEL CLIENTE
              ======================================--> 

              <div class="form-group">
                <label><i class="fa fa-users"></i> Buscar Cliente por DNI</label>
                <!-- Campo de búsqueda de DNI y botón para buscar -->
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-key"></i></span>
                  <input type="text" class="form-control" id="buscarClienteDni" placeholder="Ingresar DNI" maxlength="8" autocomplete="off">
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-info btnBuscarCliente"><i class="fa fa-search"></i></button>
                  </span>
                </div>
                <small class="form-text text-muted">Ingrese el DNI y presione el ícono de búsqueda para cargar el cliente.</small>
                <!-- Mostrar el nombre del cliente seleccionado -->
                <div class="input-group" style="margin-top:5px">
                  <span class="input-group-addon"><i class="fa fa-user"></i></span>
                  <input type="text" class="form-control" id="nombreCliente" placeholder="Nombre del cliente" readonly>
                </div>
                <!-- Campo oculto para guardar el ID del cliente seleccionado -->
                <input type="hidden" id="seleccionarCliente" name="seleccionarCliente">
                <!-- Campo oculto con el id del cliente ficticio.  Se usará al enviar el formulario
                     cuando el usuario no haya elegido ningún cliente. -->
                <?php
                  // Obtener el cliente ficticio (DNI 00000000) para usarlo como predeterminado.
                  $defaultCliente   = ControladorClientes::ctrMostrarClientes("documento", "00000000");
                  $defaultClienteId = $defaultCliente ? $defaultCliente["id"] : "";
                ?>
                <input type="hidden" id="defaultClienteId" value="<?php echo $defaultClienteId; ?>">
                <!-- Botón para abrir modal y agregar nuevo cliente -->
                <div class="input-group" style="margin-top:5px">
                  <span class="input-group-addon"><i class="fa fa-plus"></i></span>
                  <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#modalAgregarCliente">Agregar cliente</button>
                </div>
              </div>

              <!--=====================================
              ENTRADA PARA AGREGAR PRODUCTO
              ======================================--> 

              <div class="form-group row nuevoProducto">

              

              </div>

              <input type="hidden" id="listaProductos" name="listaProductos">

              <!--=====================================
              BOTÓN PARA AGREGAR PRODUCTO
              ======================================-->

              <button type="button" class="btn btn-warning hidden-lg btnAgregarProducto">
                <i class="fa fa-plus"></i> Agregar producto
              </button>

              <hr>

              <div class="row">

                <!--=====================================
                ENTRADA IMPUESTOS Y TOTAL
                ======================================-->
                
                <div class="col-xs-8 pull-right summary-sticky">
                  
                  <div class="summary-card">
                  <table class="table" style="margin-bottom:0;">

                    <thead>

                      <tr>
                        <th><i class="fa fa-percent"></i> Impuesto</th>
                        <th><i class="fa fa-dollar"></i> Total</th>      
                      </tr>

                    </thead>

                    <tbody>
                    
                      <tr>
                        
                        <td style="width: 50%">
                          
                          <div class="input-group">
                         
                            <input type="number" class="form-control input-lg" min="0" id="nuevoImpuestoVenta" name="nuevoImpuestoVenta" placeholder="0" required>

                             <input type="hidden" name="nuevoPrecioImpuesto" id="nuevoPrecioImpuesto" required>

                             <input type="hidden" name="nuevoPrecioNeto" id="nuevoPrecioNeto" required>

                            <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                      
                          </div>

                        </td>

                         <td style="width: 50%">
                          
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

              </div>

              <hr>

              <!--=====================================
              ENTRADA TIPO DE COMPROBANTE
              ======================================-->

              <div class="form-group">
                
                <label for="nuevoTipoComprobante"><i class="fa fa-file-text"></i> Tipo de comprobante</label>
                
                <div class="input-group">
                  
                  <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
                  
                  <!-- Nuevo selector para elegir el tipo de comprobante (boleta, factura o ticket).
                       Este valor se envía junto con el formulario y se puede manejar en
                       el backend para generar el comprobante correspondiente. -->
                  <select class="form-control" id="nuevoTipoComprobante" name="nuevoTipoComprobante" required>
                    <!-- Establecemos "Boleta" como valor predeterminado para evitar envíos con valor vacío.  -->
                    <option value="Boleta" selected>Boleta</option>
                    <option value="Factura">Factura</option>
                    <option value="Ticket">Ticket</option>
                  </select>
                
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
                      <!-- Seleccionamos "Efectivo" por defecto. Si el usuario no cambia esta opción,
                           el valor "Efectivo" será enviado al servidor. -->
                      <option value="">Seleccione método de pago</option>
                      <option value="Efectivo" selected>Efectivo</option>
                      <option value="Tarjeta">Tarjeta</option>
                      <!-- Puede añadirse más métodos de pago en el futuro -->
                    </select>

                  </div>

                </div>

                <div class="cajasMetodoPago"></div>

                <!--
                  listaMetodoPago almacena el método de pago y, cuando corresponde,
                  el código de transacción. Si no se cambia el método de pago, se
                  establecerá por defecto en "Efectivo" para que el backend reciba
                  un valor válido.
                -->
                <input type="hidden" id="listaMetodoPago" name="listaMetodoPago" value="Efectivo">

              </div>

              <br>
        
            </div>

            <div class="box-footer" style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e9ecef;">

              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-save"></i> Guardar Venta
              </button>

            </div>

          </form>

          <?php

            $guardarVenta = new ControladorVentas();
            $guardarVenta -> ctrCrearVenta();
            
          ?>

          </div>
              
        </div>

        <!--=====================================
        LA TABLA DE PRODUCTOS MEJORADA
        ======================================-->

        <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
          
          <div class="pos-right-panel">

            <div class="pos-right-header">
              <h3><i class="fa fa-cube"></i> Catálogo de Productos</h3>
            </div>

            <div class="pos-right-body">
              
        <!-- Barra de búsqueda -->
        <div class="row" style="margin-bottom: 10px;">
          <div class="col-sm-12">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-search"></i></span>
              <input type="text" class="form-control" id="buscarProducto" placeholder="Buscar productos por nombre o código...">
            </div>
          </div>
        </div>
        
        <!-- Vista en cuadrícula (supermercado) -->
        <div id="gridView" style="display:none;">
          <div class="product-grid" id="productGrid">
            <!-- Cards cargadas por AJAX -->
          </div>
        </div>

        <!-- Vista en lista (DataTable existente) -->
        <div id="listView">
          <table class="table table-bordered table-striped dt-responsive tablaVentas">
            <thead>
              <tr>
                <th style="width: 10px">#</th>
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

  </div>

      </div>
   
    </section>

  </div>


<!--=====================================
MODAL AGREGAR CLIENTE
======================================-->

<div id="modalAgregarCliente" class="modal fade" role="dialog">
  
  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar nuevo cliente cliente</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- BUSCAR POR DNI/RUC -->
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-th"></i></span> 

                  <select name="" id="" class="form-control input-sm">
                    <option value="">Seleccionar Consulta</option>
                    <option value="">RENIEC</option>
                    <option value="">SUNAT</option>
                  </select>

              </div>

            </div>

            <!-- ENTRADA PARA EL DOCUMENTO ID -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-key"></i></span> 

                <input type="text" onkeyup="limpiarNumero(this)" class="form-control input-sm" name="nuevoDocumentoId" placeholder="Ingresar documento" required autocomplete="off">

              </div>

            </div>

            <!-- ENTRADA PARA EL NOMBRE -->

            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-user"></i></span> 

                <input type="text" class="form-control input-sm" name="nuevoCliente" placeholder="Ingresar nombre" required autocomplete="off">

              </div>

            </div>

            <!-- ENTRADA PARA EL EMAIL -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span> 

                <input type="email" class="form-control input-sm" name="nuevoEmail" placeholder="Ingresar email" autocomplete="off">

              </div>

            </div>

            <!-- ENTRADA PARA EL TELÉFONO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-phone"></i></span> 

                <input type="text" class="form-control input-sm" name="nuevoTelefono" placeholder="Ingresar teléfono" data-inputmask="'mask':'(999) 999-999'" data-mask required autocomplete="off">

              </div>

            </div>

            <!-- ENTRADA PARA LA DIRECCIÓN -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span> 

                <input type="text" class="form-control input-sm" name="nuevaDireccion" placeholder="Ingresar dirección" required autocomplete="off">

              </div>

            </div>

             <!-- ENTRADA PARA LA FECHA DE NACIMIENTO -->
            
            <div class="form-group">
              
              <div class="input-group">
              
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 

                <input type="text" class="form-control input-sm" name="nuevaFechaNacimiento" placeholder="Ingresar fecha nacimiento" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask autocomplete="off">

              </div>

            </div>
  
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

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

<script>
$(document).ready(function() {
  
  //=====================================
  // BÚSQUEDA DE PRODUCTOS
  //=====================================

  // Búsqueda de productos en tiempo real
  $('#buscarProducto').on('keyup', function() {
    var searchTerm = $(this).val().toLowerCase();
    // Filtrar DataTable si está inicializada
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('.tablaVentas')) {
      $('.tablaVentas').DataTable().search(searchTerm).draw();
    }

    // Filtrar tarjetas del grid
    $('#productGrid .product-card').each(function() {
      var texto = $(this).text().toLowerCase();
      if (texto.includes(searchTerm)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });
  
  //=====================================
  // MEJORAS EN LA TABLA DE PRODUCTOS
  //=====================================
  
  // Notificación cuando se agrega un producto
  $('.tablaVentas tbody').on('click', 'button.agregarProducto', function() {
    var nombreProducto = $(this).closest('tr').find('td:eq(3)').text();
    
    // Mostrar notificación
    Swal.fire({
      icon: 'success',
      title: 'Producto agregado',
      text: nombreProducto + ' agregado al carrito',
      timer: 2000,
      showConfirmButton: false
    });
  });

  // Notificación al agregar desde el grid
  $(document).on('click', '#productGrid button.agregarProducto', function() {
    var nombreProducto = $(this).closest('.product-card').find('.product-title').text();
    Swal.fire({
      icon: 'success',
      title: 'Producto agregado',
      text: nombreProducto + ' agregado al carrito',
      timer: 2000,
      showConfirmButton: false
    });
  });
  
  //=====================================
  // MEJORAS EN EL CARRITO
  //=====================================
  
  // Efecto de animación al agregar productos al carrito
  $('.nuevoProducto').on('DOMNodeInserted', function() {
    $(this).find('.row:last').hide().fadeIn(500);
  });

  // Controles de keypad y toggle removidos

  //=====================================
  // CARGAR PRODUCTOS AL GRID
  //=====================================

  function loadGridProducts() {
    var datos = new FormData();
    datos.append('traerProductos', 'ok');

    $.ajax({
      url: 'ajax/productos.ajax.php',
      method: 'POST',
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(respuesta) {
        var $grid = $('#productGrid');
        $grid.empty();

        respuesta.forEach(function(item) {
          if (item.stock == 0) return; // no mostrar sin stock

          var imgSrc = item.imagen ? item.imagen : 'vistas/img/productos/default/anonymous.png';
          var codigo = item.codigo ? item.codigo : '';
          var precio = item.precio_venta ? item.precio_venta : 0;

          var card = [
            '<div class="product-card">',
              '<img class="product-thumb" src="'+ imgSrc +'" alt="'+ (item.descripcion||'Producto') +'">',
              '<div class="product-title">'+ (item.descripcion||'') +'</div>',
              '<div class="product-meta">'+ (codigo ? 'Código: '+codigo+' • ' : '') +'<span class="stock-badge">Stock: '+ item.stock +'</span></div>',
              '<div class="product-actions">',
                '<span class="price-badge">$ '+ parseFloat(precio).toFixed(2) +'</span>',
                '<button type="button" class="btn btn-primary btn-xs agregarProducto" idProducto="'+ item.id +'">',
                  '<i class="fa fa-cart-plus"></i> Añadir',
                '</button>',
              '</div>',
            '</div>'
          ].join('');

          $grid.append(card);
        });
      }
    });
  }

  // Cargar al entrar a la vista
  loadGridProducts();
  
});
</script>
