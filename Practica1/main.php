<?php

require_once "server.php";
require_once "cliente.php";
require_once "config.php";

if (in_array("--server", $argv)) {
    server_run();
} elseif ($argc < 3) {
    echo "Uso: php main.php <URL> <ArchivoDestino>\n";
    exit(1);
} elseif ($argc == 3) {
    client_run($argv);
}

?>
