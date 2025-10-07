#!/usr/bin/env bash
while sleep 10; do
    echo "Enviando dato de CO2..."
    curl -X POST -d '{"CO2":0.45,"IDsonda":"34"}' -H "Content-Type:application/json" http://localhost:5000/dato.php -v
done
