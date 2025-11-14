#!/usr/bin/env bash
# Enviamos unos datos inventados como parte de la simulacion
while sleep 10; do
    echo "Envio de datos de CO2"
    curl -X POST -d '{"CO2":0.45,"IDsonda":"1"}' -H "Content-Type:application/json" http://192.168.8.1:8080/dato.php 
done
