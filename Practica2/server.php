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
                echo $lee;
                exit(-1);
            }
            elseif($lee != false){
                $line = strtok($lee, "\r\n");
                $parts = explode(' ', $line, 3);
                $path = $parts[1] ?? '/';
                $path  = parse_url($path, PHP_URL_PATH) ?? '/';
                $file_path = __DIR__ . $path;

                $content = file_get_contents($file_path);
                $len = strlen($content);
                socket_write($newc,  
                    "HTTP/1.0 200 OK\r\n".
                    "Content-lenght: $len\r\n".
                    "\r\n".$content);
                echo "client close";
                exit(-1);
            }
        }
        exit(-1);
    } else {
        socket_close($newc);
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
