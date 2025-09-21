<?php
// Boleta de Venta en formato ticket usando TCPDF

// Dependencias de negocio
require_once "../../../controladores/ventas.controlador.php";
require_once "../../../modelos/ventas.modelo.php";
require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";
require_once "../../../controladores/usuarios.controlador.php";
require_once "../../../modelos/usuarios.modelo.php";

// TCPDF
require_once dirname(__FILE__).'/tcpdf_include.php';

// Helper: convertir número a letras (es-PE)
function numeroALetras($num){
  $num = (int)$num;
  if($num==0) return 'cero';
  $u = ['','uno','dos','tres','cuatro','cinco','seis','siete','ocho','nueve'];
  $e = [10=>'diez',11=>'once',12=>'doce',13=>'trece',14=>'catorce',15=>'quince'];
  $d = ['','diez','veinte','treinta','cuarenta','cincuenta','sesenta','setenta','ochenta','noventa'];
  $c = ['','ciento','doscientos','trescientos','cuatrocientos','quinientos','seiscientos','setecientos','ochocientos','novecientos'];
  $toWords = function($n) use (&$toWords,$u,$e,$d,$c){
    $n = (int)$n;
    if($n==0) return '';
    if($n<10) return $u[$n];
    if($n<16) return $e[$n];
    if($n<20) return 'dieci'.($n==16?'seis':$toWords($n-10));
    if($n==20) return 'veinte';
    if($n<30) return 'veinti'.($n==22?'dós':($n==23?'trés':($n==26?'seis':$toWords($n-20))));
    if($n<100){
      $dec=intval($n/10); $rest=$n%10; return $d[$dec].($rest? ' y '.$toWords($rest):'');
    }
    if($n==100) return 'cien';
    if($n<1000){ $cen=intval($n/100); $rest=$n%100; return $c[$cen].($rest?' '.$toWords($rest):''); }
    if($n<1000000){
      $mil=intval($n/1000); $rest=$n%1000;
      $pref = ($mil==1)?'mil':$toWords($mil).' mil';
      return trim($pref.($rest?' '.$toWords($rest):''));
    }
    if($n<1000000000){
      $mill=intval($n/1000000); $rest=$n%1000000;
      $pref = ($mill==1)?'un millón':$toWords($mill).' millones';
      return trim($pref.($rest?' '.$toWords($rest):''));
    }
    return (string)$n;
  };
  // Ajustes de UNO -> UN cuando corresponde
  $texto = $toWords($num);
  $texto = preg_replace('/\buno\b/u','un',$texto);
  return $texto;
}

// Obtener código de venta
$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null;
if (!$codigo) {
    die('Falta el parámetro codigo');
}

// Traer la venta por código
$venta = ControladorVentas::ctrMostrarVentas('codigo', $codigo);
if (!$venta) {
    die('Venta no encontrada');
}

// Traer cliente y vendedor
$cliente = ControladorClientes::ctrMostrarClientes('id', $venta['id_cliente']);
$vendedor = ControladorUsuarios::ctrMostrarUsuarios('id', $venta['id_vendedor']);

// Decodificar productos
$productos = json_decode($venta['productos'], true);
if (!is_array($productos)) $productos = [];

// Configurar PDF: Ticket 80mm de ancho, alto auto (usaremos A4 de alto y contenido se ajusta)
$medidaAncho = 80; // mm
$medidaAlto = 297; // mm (A4 alto estándar)
$pdf = new TCPDF('P', 'mm', array($medidaAncho, $medidaAlto), true, 'UTF-8', false);
$pdf->SetCreator('POS');
$pdf->SetAuthor('POS');
$pdf->SetTitle('Boleta '.$codigo);
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(TRUE, 5);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Encabezado (logo opcional)
$html = '';
$logoPath = '../../../vistas/img/plantilla/logo-blanco.png';
if (file_exists($logoPath)) {
    $html .= '<div style="text-align:center; margin-bottom:6px;"><img src="'.$logoPath.'" style="height:32px;"></div>';
}

// Datos de la empresa (ajusta aquí si tienes tabla de empresa)
$empresa = [
    'razon' => 'MI EMPRESA S.A.C.',
    'ruc' => 'RUC: 00000000000',
    'direccion' => 'DIRECCIÓN COMERCIAL',
    'telefono' => 'Tel: -',
    'correo' => '',
];

$html .= '<div style="text-align:center;">
  <strong>'.$empresa['razon'].'</strong><br>
  '.$empresa['ruc'].'<br>
  '.$empresa['direccion'].'<br>
  '.$empresa['telefono'].'<br>
</div>
<hr>
<div style="text-align:center; font-size:11px;">
  <strong>BOLETA DE VENTA ELECTRÓNICA</strong><br>
  <span style="font-size:12px;">'.$codigo.'</span>
</div>
<hr>';

// Datos del cliente
$html .= '<table cellpadding="2" cellspacing="0" style="font-size:10px;">
  <tr><td style="width:22mm;">Cliente:</td><td>'.htmlspecialchars($cliente['nombre'] ?? '-').'</td></tr>
  <tr><td>DNI/RUC:</td><td>'.htmlspecialchars($cliente['documento'] ?? '-').'</td></tr>
  <tr><td>Fecha:</td><td>'.substr($venta['fecha'],0,10).'</td></tr>
  <tr><td>Hora:</td><td>'.substr($venta['fecha'],11,5).'</td></tr>
</table>
<hr>';

// Detalle de productos
$html .= '<style>
  .tbl-items { width:100%; font-size:10px; border-top:0.5px solid #000; }
  .tbl-items th, .tbl-items td { padding:2px 1px; }
  .tbl-items thead th { border-bottom:0.5px solid #000; font-weight:bold; }
  .nowrap { white-space: nowrap; }
  .right { text-align: right; }
  .left { text-align: left; }
  .total-table { width:100%; font-size:10px; margin-top:2px; }
  .total-table td { padding:2px 1px; }
  .total-row { border-top:0.6px solid #000; border-bottom:0.6px solid #000; font-weight:bold; }
</style>';

$html .= '<table class="tbl-items" cellpadding="0" cellspacing="0">
  <thead>
    <tr>
      <th class="left nowrap" style="width:16%;">Cant.</th>
      <th class="left" style="width:48%;">Descripción</th>
      <th class="right nowrap" style="width:18%;">Precio</th>
      <th class="right nowrap" style="width:18%;">Total</th>
    </tr>
  </thead>
  <tbody>';

foreach ($productos as $p) {
    $cant = isset($p['cantidad']) ? (float)$p['cantidad'] : 1;
    $desc = isset($p['descripcion']) ? $p['descripcion'] : '';
    $precio = isset($p['precio']) ? (float)$p['precio'] : (isset($p['precio'])? (float)$p['precio'] : 0);
    // En muchos sistemas el campo 'precio' es el total de la línea cuando se arma la venta
    // Si tienes 'precio' como unitario, ajusta: $totalLinea = $cant * $precio
    $totalLinea = $precio; 
    $unitario = ($cant>0? $totalLinea/$cant : 0);
    $html .= '<tr>
        <td class="left nowrap">'.number_format($cant,2).'</td>
        <td class="left">'.htmlspecialchars($desc).'</td>
        <td class="right nowrap">'.number_format($unitario,2).'</td>
        <td class="right nowrap">'.number_format($totalLinea,2).'</td>
      </tr>';
}

$html .= '</tbody></table><hr>';

// Totales
$neto = (float)$venta['neto'];
$igv = (float)$venta['impuesto'];
$total = (float)$venta['total'];

$html .= '<table class="total-table" cellpadding="0" cellspacing="0">
  <tr>
    <td class="left">Total Gravado</td>
    <td class="right">S/ '.number_format($neto,2).'</td>
  </tr>
  <tr>
    <td class="left">I.G.V</td>
    <td class="right">S/ '.number_format($igv,2).'</td>
  </tr>
  <tr class="total-row">
    <td class="left">TOTAL</td>
    <td class="right">S/ '.number_format($total,2).'</td>
  </tr>
</table>
<hr>';

// SON: {monto en letras}
$enteros = floor($total);
$centavos = (int)round(($total - $enteros)*100);
$montoLetras = strtoupper(numeroALetras($enteros)).' CON '.sprintf('%02d',$centavos).'/100 SOLES';
$html .= '<div style="font-size:10px; margin-top:2px;"><strong>SON:</strong> '.$montoLetras.'</div><hr>';

// Forma de pago
$html .= '<div style="font-size:10px;">
  <strong>Forma de pago:</strong> '.htmlspecialchars($venta['metodo_pago']).'<br>
  <strong>Vendedor:</strong> '.htmlspecialchars($vendedor['nombre'] ?? '-').'
</div>';

// Renderizar HTML
$pdf->writeHTML($html, true, false, true, false, '');

// QR opcional con el código de la boleta
if (class_exists('TCPDF2DBarcode')) {
    $pdf->Ln(2);
    $style = array(
        'border' => 0,
        'padding' => 0,
        'fgcolor' => array(0,0,0),
        'bgcolor' => false
    );
    $pdf->write2DBarcode('BOLETA-'.$codigo, 'QRCODE,H', '', '', 28, 28, $style, 'N');
}

// Salida
$pdf->lastPage();
$pdf->Output('boleta_'.$codigo.'.pdf', 'I');

?>
<h1>Reporte de Boleta</h1>