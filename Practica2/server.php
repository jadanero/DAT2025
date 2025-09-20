<?php
require_once "config_server.php";
function cliente($newc)
{
    global $server_host, $server_port;
    if (pcntl_fork() == 0) {
        while (true) {
            $lee = socket_read($newc, 1024);
            if ($lee == false) {
                exit();
            }
            elseif($lee!=false){
                $parts = preg_split('/[\r\n ]+/', $lee, -1, PREG_SPLIT_NO_EMPTY);
                if ($parts[0] == "PUT"){
                    unset($parts[0],$parts[2],$parts[3]); //nos quedamos con lo que queremos escribir del refresh
                    $parts = array_values($parts);
                    refresh_peers($parts);
                }
                elseif($parts[0] == "GET"){

                }
            }
        }
        exit(-1);
    } 
}


function server_run(){
    global $server_host, $server_ports;
    $host = $server_host;
    $servers = [];
    // Crear y preparar cada servidor
    foreach ($server_ports as $port) {
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1); // reutilizar puerto rápido
        socket_bind($sock, $host, $port);
        socket_listen($sock);
        $servers[] = $sock;
        echo "Servidor escuchando en $host:$port\n";
    }

    // Bucle principal
    while (true) {
        // Lista de sockets que queremos vigilar
        $read = $servers; 
        $write = $except = null;

        // Esperar actividad en alguno
        $changed = socket_select($read, $write, $except, null);

        if ($changed === false) {
            echo "Error en socket_select: " . socket_strerror(socket_last_error()) . "\n";
            break;
        }

        // Revisar qué socket tuvo actividad
        foreach ($read as $serverSock) {
            if ($client = socket_accept($serverSock) !== false){
                // Saber en qué puerto entró
                socket_getsockname($serverSock, $addr, $port);
                socket_getpeername($client, $client_ip, $client_port);
                echo "Cliente desde $client_ip:$client_port conectado al puerto $port\n";
                cliente($client);
            }
        }
    }
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
}

function refresh_peers($parts){ //escribe despues de lo que esté escrito lo que ha mandado el cliente
    //Falta hacer que busque el host y las lineas en las que está su info para borrarlo y escribir los que tiene nuevos
    //no es lo ideal pero funciona
    $matriz_peers = matriz_peers_f();
    $newparts = [];
    foreach($matriz_peers as $linea){
        if($linea[0]!=$parts[0]){
            $newparts[] = $linea;
        }
    }
    $newparts[] = $parts;
    //echo print_r($newparts);
    $newnewparts = array_merge(...$newparts);
    $archivo = "peers/archivos.txt";
    $fp = fopen($archivo, "w");
    $body = implode("\n", $newnewparts);
    fwrite($fp, $body."\n");
    fclose($fp);
    echo "refresh hecho\n";
}