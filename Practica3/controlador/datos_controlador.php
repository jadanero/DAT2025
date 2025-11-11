<?php
$input = file_get_contents("php://");
$data = json_decode($input, true);

if ($data === null) {
    http_response_code(400);
    echo "JSON inválido";
    exit();
}

// Añadir marca de tiempo
$data["recibido_en"] = date("Y-m-d H:i:s");
echo "Controlador ha recibido: " . json_encode($data) . "\n";
?>