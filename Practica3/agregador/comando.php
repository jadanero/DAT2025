<?php
// Consulta de la sonda para saber si tiene alguna acción pendiente

require_once(__DIR__ . '/autentification.php');
header('Content-Type: application/json; charset=UTF-8');

$user = $_SERVER['PHP_AUTH_USER'];

$IDsonda = preg_replace('/\D+/', '', $user);
if ($IDsonda === '') {
  echo json_encode(['status'=>'ERROR','msg'=>'ID de sonda inválido']);
  exit;
}

$file = __DIR__ . '/accionesPendientes.txt';
if (!is_file($file)) {
  echo json_encode(['status'=>'OK','acciones'=>[]]);
  exit;
}

$acciones = [];
$fh = fopen($file,'r');
if ($fh) {
  while (($line = fgets($fh)) !== false) {
    $line = trim($line);
    if ($line==='') continue;
    $a = json_decode($line, true);
    if (is_array($a) && isset($a['IDsonda']) &&
        strval($a['IDsonda']) === strval($IDsonda)) {
      $acciones[] = $a;
    }
  }
  fclose($fh);
}

echo json_encode(['status'=>'OK','acciones'=>$acciones], JSON_UNESCAPED_UNICODE);

