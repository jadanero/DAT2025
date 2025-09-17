<?php
function client_run($argv)
{
    $url = $argv[1];
    $archivoDestino = $argv[2];

    // Descargar contenido de la URL
    $contenido = file_get_contents($url);

    if ($contenido === false) {
        echo "Error al descargar el archivo.\n";
        exit(1);
    }

    // Guardar en archivo local
    if (file_put_contents($archivoDestino, $contenido) === false) {
        echo "Error al guardar el archivo.\n";
        exit(1);
    }

    echo "Archivo descargado correctamente: $archivoDestino\n";
}
?>