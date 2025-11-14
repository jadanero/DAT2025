#!/usr/bin/env bash
while sleep 10; do
    echo "Envio de confirmacion de accion ejecutada :)"

    # Ejemplo: la sonda con ID 1 confirma que encendio la luz 2 correctamente
    curl -X POST \
     -u sonda1:1234 \
     -H "Content-Type: application/json" \
     -d '{"IDsonda":"1","accion":"encender_led","resultado":"ok"}' \
     http://192.168.8.1:8080/cambioestado_agregador.php 

done
