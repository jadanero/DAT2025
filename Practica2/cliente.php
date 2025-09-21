<?php
require_once "config_client.php";
function download_archive($argv){
    global $dir_downloads;
    $host = $argv[0];
    $port = $argv[1];
    $archivoDestino = $argv[0];
    $newc = socket_create(AF_INET, SOCK_STREAM,getprotobyname('tcp'));
    if ($newc === false){
        echo "Error al crear el socket";
        exit(-1);
    }elseif(socket_connect($newc,$host,$port) == false){
        echo "Error al conectar con el servidor";
        exit(-1);
    }
    socket_write($newc,  
                    "GET /$argv[2]\r\n".
                    "Content-Length: $len\r\n".
                    "Connection: close"."\r\n"."\r\n");
    
    socket_write($newc, $request, strlen($request));

    $archivo = $dir_downloads."/".$argv[2];
    $fp = fopen($archivo, "w");
    while ($out = socket_read($newc, 2048)) {
        fwrite($fp, $out);
    }
    fclose($fp);
    echo "Archivo descargado correctamente en: $dir_downloads\n";
}

function client_refresh($newc,$clientHost,$clientPort){
    global $dir_files;
    $list = scandir($dir_files);
    $list1 = array_slice($list,2);
    $body = join("\n",$list1);
    $len = strlen($body);
    socket_write($newc,  
                    "PUT /host/$clientHost:$clientPort\r\n".
                    "Content-Length: $len\r\n".
                    "\r\n".$body);
}

function search_archivo_client($arch){ //busca el archivo con el nombre completo o parcial

}

function client_ux($argv) { 
    $options = ["search","descargar","exit"];
    list($serverHost,$serverPort) = explode(":",$argv[2]);
    $newc = socket_create(AF_INET, SOCK_STREAM,getprotobyname('tcp'));

    if ($newc === false){
        echo "Error al crear el socket";
        exit(-1);
    }elseif(socket_connect($newc,$serverHost,$serverPort) == false){
        echo "Error al conectar con el servidor";
        exit(-1);
    }
    socket_getsockname($newc, $clientHost, $clientPort);
    client_refresh($newc,$clientHost,$clientPort);
    $pid = pcntl_fork();
    if ($pid == -1) {
        die("Error al crear proceso hijo\n");
    } elseif ($pid === 0) {
        while (true) {
            sleep(10);
            client_refresh($newc,$clientHost,$clientPort);
        }
        exit(0);
    }
    // Proceso padre: sigue con la interacción normal
    while (true) {
        echo ">";
        $instnew = trim(fgets(STDIN));
        $inst = explode(" ", $instnew);

        if ($inst[0] === $options[0]) {
            echo $options[0];
            exit(-1);
        } elseif ($inst[0] === $options[1]) {
            download_archive($host, $port, $inst);
            exit(-1);
        } elseif ($inst[0] === $options[2]) {
            echo "Saliendo...\n";
            posix_kill($pid, SIGTERM); // matamos al proceso hijo si salimos
            exit(0);
        } else {
            echo "Tienes estas opciones: \n"
                .$options[0]." trozo de archivo que quieras encontrar\n"
                .$options[1]." nombre de archivo completo\n>"
                .$options[2]." para salir\n";
        }
    }
    exit(-1);
}
?>