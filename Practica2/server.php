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
                    $archivo = "peers/archivos.txt";
                    $lineas = [];
                    $fp = fopen($archivo, "a+");
                    
                    print_r(crear_matriz_peers());





                    // $lineaActual = current($lineas);
                    // $count = 0;
                    // while ($lineaActual !== false) { //recorro el array de lineas  
                    //     if ($lineaActual === $parts[0]) {
                    //         echo "Encontrada la línea exacta: $lineaActual\n";
                    //         echo $count."\n";
                    //         break;
                    //     }
                    //     $count++;
                    //     $lineaActual = next($lineas); // Mover al siguiente elemento
                    // }
                    // }
                    $body = join("\n",$parts);
                    fwrite($fp, $body."\n");
                    fclose($fp);
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


function crear_matriz_peers(){
    $archivo = "peers/archivos.txt";
    $lineas = [];
    $fp = fopen($archivo, "r");
    if ($fp) {
    while (($linea = fgets($fp)) !== false) { //se obtine array de lineas
        $lineas[] = trim($linea); // trim elimina saltos de línea   
    }
    $a = false; 
    $matriz = [];
    $fila = []; 
    foreach ($lineas as $i => $valor) {
    if (strpos($valor,"/host") !== false) {
        if ($a === false){
            $a = true;
        }else{
        $matriz[] = $fila; // agregamos la fila completa a la matriz
        $fila = [];        // reiniciamos la fila
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

function search_host($host){ // para buscar en el refresh que archivos tiene ese peer
    $matriz_peers = crear_matriz_peers();
    foreach ($matriz_peers as $fila) {
        foreach ($fila as $valor) {
            if ($valor == $host){
                //LO DEJO AQUI
            }else{
                break;
            }    
        }
        echo "\n"; // salto de línea entre filas
    }        
}

function search_archivo($archivo = null){ //busca el nombre de archivo en el txt
    $matriz_peers = crear_matriz_peers();
    if ($archivo == null){ 
        $body = join("\n",$matriz_peers);
        echo $body;
    }else{

        return $host;
    }
}