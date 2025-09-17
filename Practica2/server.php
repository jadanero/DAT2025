<?php

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
                    //echo join("\n",$parts);
                    unset($parts[0],$parts[2],$parts[3]); //nos quedamos con lo que queremos escribir del refresh
                    $parts = array_values($parts);
                    //print_r(matriz_peers_f());
                    refresh_peers($parts);





                }
                exit(-1);
            }
        }
        exit(-1);
    } 
}


function server_run(){
    global $server_host, $server_port;
    $sock = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));
    socket_bind($sock, $server_host, $server_port);
    socket_listen($sock, 10);
    $i = 0;
    while (true) {
        if (($newc = socket_accept($sock)) !== false) {
            socket_getpeername($newc, $address, $port);
            echo "Client has connected\n";
            cliente($newc);
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

function search_host_delete($host){ // para buscar en el refresh que archivos tiene ese peer y deletea el host y sus archivos
}

function search_archivo($archivo = null){ //busca el nombre de archivo en el txt
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
    echo print_r($newparts);
    $newnewparts = array_merge(...$newparts);
    $archivo = "peers/archivos.txt";
    $fp = fopen($archivo, "w");
    $body = implode("\n", $newnewparts);
    fwrite($fp, $body."\n");
    fclose($fp);
}