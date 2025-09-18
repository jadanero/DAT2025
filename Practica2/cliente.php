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
            echo $options[1];
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