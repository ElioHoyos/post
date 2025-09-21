<?php
// Datos
if(!empty($_POST)){
$token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';
$ruc = isset($_POST['documento'])?trim($_POST['documento']):0;

  // Validar que el RUC tenga 11 dígitos
  if(!preg_match('/^[0-9]{11}$/', $ruc)) {
    $respuesta_js = [
      'mensaje' => 'El RUC debe tener exactamente 11 dígitos',
      'codigo' => 0,
      'data' => null
    ];
    echo json_encode($respuesta_js);
    return;
  }

// Iniciar llamada a API
$curl = curl_init();

// Buscar ruc sunat
curl_setopt_array($curl, array(
  // para usar la versión 2
  //CURLOPT_URL =>0 'https://api.apis.net.pe/v2/sunat/ruc?numero=' . $ruc,
  // para usar la versión 1
  CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $ruc,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Referer: http://apis.net.pe/api-ruc',
    'Authorization: Bearer ' . $token,
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
  ),
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);

curl_close($curl);

// Verificar si hubo error en la petición
if($error) {
  $respuesta_js = [
    'mensaje' => 'Error de conexión: ' . $error,
    'codigo' => 0,
    'data' => null
  ];
  echo json_encode($respuesta_js);
  return;
}

// Verificar código HTTP
if($httpCode == 429) {
  $respuesta_js = [
    'mensaje' => 'Demasiadas consultas. Intente nuevamente en unos minutos.',
    'codigo' => 0,
    'data' => null
  ];
  echo json_encode($respuesta_js);
  return;
}

if($httpCode == 422) {
  $respuesta_js = [
    'mensaje' => 'RUC inválido o no encontrado',
    'codigo' => 0,
    'data' => null
  ];
  echo json_encode($respuesta_js);
  return;
}

if($httpCode !== 200) {
  $respuesta_js = [
    'mensaje' => 'Error del servidor: Código ' . $httpCode,
    'codigo' => 0,
    'data' => null
  ];
  echo json_encode($respuesta_js);
  return;
}

// Datos de empresas según padron reducido
$empresa = json_decode($response, true);

$codigo = 0;
$msg = "";
$data = null;

if($empresa === null) {
  $codigo = 0;
  $msg = "Error al procesar respuesta del servidor";
} else if(isset($empresa['message'])) {
  $codigo = -1;
  $msg = $empresa['message'];
} else if(isset($empresa['error'])) {
  $codigo = 0;
  $msg = $empresa['error'];
} else if(isset($empresa['numeroDocumento'])) {
  $codigo = 1;
  $data = $empresa;
} else {
  $codigo = 0;
  $msg = "No se encontró información para el documento";
}

$respuesta_js = [
  'mensaje' => $msg,
  'codigo' => $codigo,
  'data' => $data
];
echo json_encode($respuesta_js);
}
?>