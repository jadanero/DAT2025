#!/usr/bin/env bash
# Consulta de acciones para una sonda
IDSONDA="1"
ROUTER_IP="192.168.8.1"

while sleep 10; do
    echo "Consultando acciones para sonda $IDSONDA..."
    curl -s -X GET "http://$ROUTER_IP:8080/comando.php?idSonda=$IDSONDA"
    echo -e "\n------------------------------------------"
done

