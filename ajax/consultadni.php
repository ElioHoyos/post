<?php
// Datos
if(!empty($_POST)){

  // Token actualizado para la API
  $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';
  $dni = isset($_POST['documento'])?trim($_POST['documento']):0;
  
  // Validar que el DNI tenga 8 dígitos
  if(!preg_match('/^[0-9]{8}$/', $dni)) {
    $respuesta_js = [
      'mensaje' => 'El DNI debe tener exactamente 8 dígitos',
      'codigo' => 0,
      'data' => null
    ];
    echo json_encode($respuesta_js);
    return;
  }
  
  // Iniciar llamada a API
  $curl = curl_init();
  
  // Buscar dni
  curl_setopt_array($curl, array(
    // para user api versión 2
    //CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
    // para user api versión 1
    CURLOPT_URL => 'https://api.apis.net.pe/v1/dni?numero=' . $dni,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 2,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Referer: https://apis.net.pe/consulta-dni-api',
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
      'mensaje' => 'DNI inválido o no encontrado',
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
  
  // Datos listos para usar
  $persona = json_decode($response, true);
  
  $codigo = 0;
  $msg = "";
  $data = null;
  
  if($persona === null) {
    $codigo = 0;
    $msg = "Error al procesar respuesta del servidor";
  } else if(isset($persona['message'])) {
    $codigo = -1;
    $msg = $persona['message'];
  } else if(isset($persona['error'])) {
    $codigo = 0;
    $msg = $persona['error'];
  } else if(isset($persona['numeroDocumento'])) {
    $codigo = 1;
    $data = $persona;
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