<?php
require_once "config_server.php";
function cliente($newc){
    global $server_host, $server_port;
    if (pcntl_fork() == 0) {
        while (true) {
            $lee = socket_read($newc, 1024);
            if ($lee === false || $lee === "") {
                echo "Conexión cerrada por el cliente $client_ip:$client_port\n";
                borrar_peer($client_ip,$client_port);
                exit();
            }
            elseif($lee!=false){
                $options1 = ["PUT","GET"];
                $options2 = ["host","search","peers"];
                $parts = preg_split('/[\r\n ]+/', $lee, -1, PREG_SPLIT_NO_EMPTY);
                $aux = explode("/", $parts[1]);
                $arguments = [$parts[0],$aux[1],$aux[2]];
                if ($arguments[0] == $options1[0]){
                    if ($arguments[1] == $options2[0]){
                        refresh_peers($parts);
                        echo "refresh hecho de $arguments[2]\n";
                    }                        
                }
                elseif($arguments[0] == $options1[1]){  
                    if($arguments[1] == $options2[2]){
                        //logica de descarga
                    }elseif($arguments[1] == $options2[1]){
                        $trozo_archivo = $parts[2];
                        $resultados = search_archivo_server($trozo_archivo);
                        if(!empty($resultados)){
                            $body = join("\n",$resultados);
                            $len = strlen($body);
                        }else{
                            $body = "No se han encontrado resultados";
                            $len = strlen($body);
                            
                        }
                    }elseif($arguments[1]==$options2[1]){
                        //Logica opcional de listado de clientes
                        if ($arguments[2] != null){
                            //archivo de un peer concreto
                        }else{
                            //peers disponibles
                        }
                    }
                }
            }
        }
        exit(-1);
    } 
}


function server_run(){
    global $server_host,$server_port;
    $host = $server_host;
    $port = $server_port;

    // Crear socket del servidor
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
    socket_bind($sock, $host, $port);
    socket_listen($sock);
    $file = "peers/archivos.txt";
    $handle = fopen($file, "w");
    fclose($handle);
    echo "Servidor escuchando en $host:$port\n";

    // Bucle principal
    while (true) {
        // Evitar procesos zombie
        pcntl_waitpid(-1, $status, WNOHANG);
        $client = socket_accept($sock);
        if ($client === false) {
            continue;
        }
        socket_getpeername($client, $client_ip, $client_port);
        echo "Conexión entrante desde $client_ip:$client_port\n";

        $pid = pcntl_fork();
        if ($pid == -1) {
            echo "Error al forkear proceso\n";
            socket_close($client);
        } elseif ($pid === 0) {
            // Hijo: atiende al cliente
            cliente($client);
            socket_close($client);
            exit(0);
        } else {
            // Padre: no usa este socket
            socket_close($client);
        }
    }

    socket_close($sock);
}


function matriz_peers_f(){ //crea la matriz de los peers para operar mas facil
    $archivo = "peers/archivos.txt";
    $lineas = [];
    $fp = fopen($archivo, "r");
    if ($fp) {
    while (($linea = fgets($fp)) !== false) {
        $lineas[] = trim($linea);  
    }
    $a = false; 
    $matriz = [];
    $fila = []; 
    foreach ($lineas as $i => $valor) {
    if (strpos($valor,"/host") !== false) {
        if ($a === false){
            $a = true;
        }else{
        $matriz[] = $fila;
        $fila = [];
        }
    }
        $fila[] = $valor;
    }
    if (!empty($fila)) {
        $matriz[] = $fila;
    }
    fclose($fp);
    }
    return $matriz;  
}

function search_archivo_server($archivo = null){ //busca el nombre de archivo en el txt
    $matriz_peers = matriz_peers_f();
    $resultados = [];
    foreach($matriz_peers as $linea){
        foreach($linea as $valor){
            if(strpos($valor,$archivo) !== false && strpos($valor,"/host") === false){
                $resultados[] = $valor." -> ".$linea[0];
            }
        }
    }
}

function borrar_peer($client_ip,$client_port){ //borra el peer que se ha desconectado
    $matriz_peers = matriz_peers_f();
    $newmatriz = [];
    foreach($matriz_peers as $linea){
        if($linea[0] != "/host/".$client_ip.":".$client_port){
            $newmatriz[] = $linea;
        }
    }
    $archivo = "peers/archivos.txt";
    $fp = fopen($archivo, "w");
    $body = implode("\n", array_merge(...$newmatriz));
    fwrite($fp, $body);
    fclose($fp);
}

function refresh_peers($parts){ //escribe despues de lo que esté escrito lo que ha mandado el cliente
    //no es lo ideal pero funciona
    unset($parts[0],$parts[2],$parts[3]);
    $parts = array_values($parts);
    $clean = str_replace("/host/", "", $parts[0]);
    list($client_ip,$client_port) = explode(":", $clean);
    $matriz_peers = matriz_peers_f();
    $newparts = [];
    foreach($matriz_peers as $linea){
        if($linea[0]!=$parts[0]){
            $newparts[] = $linea;
        }
    }
    $newparts[] = $parts;
    $newnewparts = array_merge(...$newparts);
    $archivo = "peers/archivos.txt";
    $fp = fopen($archivo, "w");
    $body = implode("\n", $newnewparts);
    fwrite($fp, $body."\n");
    fclose($fp);
}