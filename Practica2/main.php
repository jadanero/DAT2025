<?php

require_once "server.php";
require_once "cliente.php";
require_once "config.php";

if (in_array("--server", $argv)) {
    server_run();
} elseif (in_array("--refresh-only",$argv)) {
    client_refresh($argv);
} elseif (in_array("--ux-only",$argv)) {
    client_ux($argv);
}

?>
