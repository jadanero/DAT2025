#!/usr/bin/env bash
while sleep 10; do
    curl -X GET -u sonda1:1234 http://192.168.8.1:8080/comando.php 
done
