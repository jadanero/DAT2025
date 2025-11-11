#!/usr/bin/env bash
while sleep 10; do
    echo "Enviando confirmación de acción ejecutada..."

    # Ejemplo: la sonda con ID 34 confirma que encendió la luz 2 correctamente
    curl -X POST \
     -u sonda1:1234 \
     -H "Content-Type: application/json" \
     -d '{"IDsonda":"1","accion":"encender_led","resultado":"ok"}' \
     http://192.168.8.1:8080/cambioestado_agregador.php -v

done
