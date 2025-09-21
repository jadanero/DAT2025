<?php

require_once "server.php";
require_once "cliente.php";

if (in_array("--server", $argv)) {
    server_run();
} elseif (in_array("--ux-only",$argv)) {
    client_ux($argv);
}else{
    $opciones = ["--server","--ux-only"];
    echo "type of execution not specified or incorrect\n";
}
?>
