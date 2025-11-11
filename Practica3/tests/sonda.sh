#!/usr/bin/env bash
while sleep 10; do
    echo "Enviando dato de CO2..."
    curl -X POST -d '{"CO2":0.45,"IDsonda":"34"}' -H "Content-Type:application/json" http://192.168.8.1:8080/dato.php 
done
