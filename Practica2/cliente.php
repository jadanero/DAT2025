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

function client_refresh($argv){
    //global $server_port, $server_host;
    global $dir_files;
    //print_r($argv);
    list($host,$port) = explode(":",$argv[2]);
    $list = scandir($dir_files);
    $list1 = array_slice($list,2);
    $body = join("\n",$list1);
    $len = strlen($body);
    $newc = socket_create(AF_INET, SOCK_STREAM,getprotobyname('tcp'));
    if ($newc === false){
        echo "Error al crear el socket";
        exit(-1);
    }elseif(socket_connect($newc,$host,$port) == false){
        echo "Error al conectar con el servidor";
        exit(-1);
    }
    socket_write($newc,  
                    "PUT /host/$argv[2]\r\n".
                    "Content-Length: $len\r\n".
                    "\r\n".$body);
}

function search_archivo_client($arch){ //busca el archivo con el nombre completo o parcial

}

function client_ux($argv){ //quedaría siempre cuando se conecte hacerle un refresh y cada x tiempo hacer un refresh por estar conectado
    echo ">";
    $options = ["search","descargar"];
    while(true){
        $instnew = trim(fgets(STDIN));
        $inst = explode(" ",$instnew);
        if($inst[0] === $options[0]){
            echo $options[0];
            exit(-1);
        }elseif($inst[0] === $options[1]){
            download_archive($host,$port,$inst);
            exit(-1);
        }else{
            echo "Tienes estas opciones: \n"
            .$options[0]." trozo de archivo que quieras encontrar\n"
            .$options[1]." nombre de archivo completo\n>";
        }
    }
    exit(-1);
}
?>