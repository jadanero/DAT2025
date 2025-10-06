<?php
require_once "config_client.php";
function CrearCarpetaCliente($ip_host,$port_host){
    global $dir_files;
    $origen = $dir_files;
    $destino = "cliente".$ip_host.":".$port_host;
    if (!is_dir($origen)) {
        die("La carpeta de origen no existe.");
    }
    if (!is_dir($destino)) {
        mkdir($destino, 0777, true);
        mkdir($destino."/uploads", 0777, true);
        mkdir($destino."/downloads", 0777, true);
    }
    $archivos = array_diff(scandir($origen), array('..', '.'));
    $archivos = array_values(array_filter($archivos, function($f) use ($origen) {
        return is_file($origen . "/" . $f);
    }));
    if (empty($archivos)) {
        die("No hay archivos en la carpeta de origen.");
    }
    $cantidad = mt_rand(1, 12);
    shuffle($archivos);
    $seleccionados = array_slice($archivos, 0, min($cantidad, count($archivos)));
    foreach ($seleccionados as $archivo) {
        copy($origen . "/" . $archivo, $destino . "/uploads/" . $archivo);
    }
}
function borrarCarpeta($ruta) {
    if (!is_dir($ruta)) {
        return false;
    }
    $archivos = array_diff(scandir($ruta), array('.', '..'));
    foreach ($archivos as $archivo) {
        $rutaCompleta = $ruta . DIRECTORY_SEPARATOR . $archivo;
        if (is_dir($rutaCompleta)) {
            borrarCarpeta($rutaCompleta);
        } else {
            unlink($rutaCompleta);
        }
    }
    return rmdir($ruta);
}

function download_archive($newc,$inst,$ip,$port){ //descarga el archivo del peer
    socket_write($newc,"GET /peers/$inst[1] HTTP/1.1 OK\r\n");
    while ($out = socket_read($newc, 2048)) {
        $parts = preg_split('/[\r\n ]+/', $out, -1, PREG_SPLIT_NO_EMPTY);
        if ($parts[4] === "no"){
            echo "No se han encontrado resultados\n";
            break;
        }else{
            $result = str_replace("/host/", "", $parts[5]);
            list($peer_ip,$peer_port) = explode(":",$result);
            $socketpeer = socket_create(AF_INET, SOCK_STREAM,getprotobyname('tcp'));
            if (socket_connect($socketpeer,$peer_ip,$peer_port) == false){
                echo "Error al conectar con el peer";
                break;
            }else{
                $archivo = $parts[6];
                $ruta_archivo = "cliente".$peer_ip.":".$peer_port."/uploads/".$archivo;
                socket_write($socketpeer,"GET ".$ruta_archivo." HTTP/1.1 OK\r\n");
                $ruta_descarga = "cliente".$ip.":".$port."/downloads/".$archivo;
                $fp = fopen($ruta_descarga, "w");
            }
        
            $buffer = "";
            while ($out = socket_read($socketpeer, 2048)) {
                $buffer .= $out;
            }
            $partes = explode("\r\n\r\n", $buffer, 2);
            $contenido = $partes[1] ?? "";
            fwrite($fp, $contenido);
            fclose($fp);
            socket_close($socketpeer);
            echo "Archivo descargado correctamente\n";
            break;
        }
    }
    
}

function client_refresh($newc,$clientHost,$clientPort){
    $dir_files = "cliente".$clientHost.":".$clientPort."/uploads";
    $list = scandir($dir_files);
    $list1 = array_slice($list,2);
    $body = join("\n",$list1);
    $len = strlen($body);
    socket_write($newc,  
                    "PUT /host/$clientHost:$clientPort HTTP/1.1 OK\r\n".
                    "Content-Length: $len\r\n".
                    "\r\n".$body);
}

function search_archivo_client($newc,$arch){ //busca el archivo con el nombre completo o parcial
    socket_write($newc,"GET /search/$arch HTTP/1.1 OK\r\n");
    $response = "";
    while ($out = socket_read($newc, 2048)) {
        $parts = preg_split('/[\r\n ]+/', $out, -1, PREG_SPLIT_NO_EMPTY);
        unset($parts[0],$parts[1],$parts[2],$parts[3],$parts[4],$parts[5]);
        if ($parts[6] === "no"){
            echo "No se han encontrado resultados\n";
            break;
        }else{
            unset($parts[6]);
            $response = "Se han encontrado los siguientes resultados:\n";
            $response .= join("\n",$parts);
            echo $response."\n";
            break;
        }
    }

}

function peer_run($sock){
    while(true){
        if(($newc = socket_accept($sock)) !== false){
            while(true){
                $lee = socket_read($newc, 1024);
                $parts = preg_split('/[\r\n ]+/', $lee, -1, PREG_SPLIT_NO_EMPTY);
                if ($lee === false || $lee === "") {
                    socket_close($newc);
                    break;
                }
                if ($parts[0] === "GET"){
                    $content = file_get_contents($parts[1]);
                    if ($content === false) {
                        $content = "no";
                    }
                    $len = strlen($content);
                    socket_write($newc,  
                        "HTTP/1.1 200 OK\r\n".
                        "Content-lenght: $len\r\n".
                        "\r\n".$content);
                    socket_close($newc);
                    break;
                }
            }
        }
    }
    exit(-1);
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

    $lsocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    $hostname = gethostname();
    $local_ip = gethostbyname($hostname);
    socket_bind($lsocket,$local_ip);
    socket_listen($lsocket);
    socket_getsockname($lsocket, $clientHost, $clientPort);
    CrearCarpetaCliente($clientHost,$clientPort);
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

    $listen = pcntl_fork();
    if ($listen == -1) {
        die("Error al crear proceso hijo\n");
    } elseif ($listen === 0) {
        peer_run($lsocket);
    }
    // Proceso padre: sigue con la interacción normal
    while (true) {
        echo ">";
        $instnew = trim(fgets(STDIN));
        $inst = explode(" ", $instnew);
        if ($inst[0] === $options[0]) {
            search_archivo_client($newc,$inst[1]);
        } elseif ($inst[0] === $options[1]) {
            download_archive($newc,$inst,$clientHost,$clientPort);
        } elseif ($inst[0] === $options[2]) {
            echo "Saliendo...\n";
            borrarCarpeta("cliente".$clientHost.":".$clientPort);
            socket_close($newc);
            socket_close($lsocket);
            posix_kill($listen, SIGTERM); // matamos al proceso hijo si salimos
            posix_kill($pid, SIGTERM); // matamos al proceso hijo si salimos
            exit(0);
        } else {
            echo "Tienes estas opciones: \n"
                .$options[0]." trozo de archivo que quieras encontrar\n"
                .$options[1]." nombre de archivo completo\n"
                .$options[2]." para salir\n";
        }
    }
    exit(-1);
}
?>