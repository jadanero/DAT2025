<?php
require_once "config.php";
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

function client_refresh($argv){
    global $server_port, $server_host;
    $list = scandir("htcdocs");
    $list1 = array_slice($list,2);
    $body = join("\n",$list1);
    $len = strlen($body);
    $newc = socket_create(AF_INET, SOCK_STREAM,getprotobyname('tcp'));
    if ($newc === false){
        echo "Error al crear el socket";
        exit(-1);
    }elseif(socket_connect($newc,$server_host,$server_port) == false){
        echo "Error al conectar con el servidor";
        exit(-1);
    }
    socket_write($newc,  
                    "PUT /host/$argv[2]\r\n".
                    "Content-lenght: $len\r\n".
                    "\r\n".$body);
    
    echo "Refresh hecho\n";
}
?>