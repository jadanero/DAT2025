<?php
require_once __DIR__ . '/autentification.php';
header('Content-Type: application/json; charset=UTF-8');

// Lee JSON del cuerpo
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data) || !isset($data['IDsonda']) || !isset($data['accion']) || !isset($data['resultado'])) {
  echo json_encode(['status'=>'ERROR','msg'=>'JSON inválido o incompleto']);
  exit;
}

$idS   = strval($data['IDsonda']);
$acc   = strval($data['accion']);
$resp  = strval($data['resultado']);

// 1) Añade a accionesEjecutadas.txt
$log = __DIR__ . '/accionesEjecutadas.txt';
$line = json_encode([
  'timestamp' => gmdate('Y-m-d H:i:s') . ' UTC', // evita warning zona horaria
  'IDsonda'   => $idS,
  'accion'    => $acc,
  'resultado' => $resp
], JSON_UNESCAPED_UNICODE);

file_put_contents($log, $line . PHP_EOL, FILE_APPEND | LOCK_EX);

// 2) Elimina la acción correspondiente en accionesPendientes.txt
$pend = __DIR__ . '/accionesPendientes.txt';
if (is_file($pend)) {
  $lines = file($pend, FILE_IGNORE_NEW_LINES);
  $keep  = [];
  foreach ($lines as $l) {
    $lt = trim($l);
    if ($lt==='') continue;
    $a = json_decode($lt, true);
    if (!is_array($a) || !isset($a['IDsonda']) || !isset($a['accion'])) {
      // línea dañada: consérvala para no romper el formato
      $keep[] = $lt;
      continue;
    }
    // SI coincide IDsonda + accion -> NO la guardes (se elimina)
    if (strval($a['IDsonda']) === $idS && strval($a['accion']) === $acc) {
      continue;
    }
    // en otro caso, se conserva
    $keep[] = json_encode($a, JSON_UNESCAPED_UNICODE);
  }
  file_put_contents($pend, $keep ? (implode(PHP_EOL, $keep) . PHP_EOL) : "", LOCK_EX);
}

echo json_encode(['status'=>'OK','msg'=>'Acción registrada y eliminada si existía'], JSON_UNESCAPED_UNICODE);

