#!/usr/bin/env bash
while sleep 10; do
    echo "Enviando confirmación de acción ejecutada..."
    
    # Ejemplo: la sonda con ID 34 confirma que encendió la luz 2 correctamente
    curl -X POST \  
        -u sonda1:1234 \
        -d '{"IDsonda":34,"accion":"luz2","resultado":"ok"}' \
        -H "Content-Type: application/json" \
        http://localhost:5000/cambioestado_agregador.php -v

done
