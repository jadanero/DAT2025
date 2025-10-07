<?php

$body = file_get_contents('php://input');

echo $body;
$time = time();

if (file_put_contents('datos/'.$time.'.json', $body)){
    header("HTTP/1.0 200 OK");
}else{
    header("HTTP/1.0 404 Internal Server Error");
}
