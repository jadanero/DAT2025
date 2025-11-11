<?php

// Lee el cuerpo JSON recibido
$body = file_get_contents('php://input');

// echo $body;

// Genera nombre único con marca temporal
$time = time();

// Ruta absoluta en el router
$filename = '/mnt/usb/www/practica3/agregador/datos/' . $time . '.json';

// Guarda el contenido
if (file_put_contents($filename, $body)) {
    header("HTTP/1.0 200 OK");
} else {
    header("HTTP/1.0 500 Internal Server Error");
}
