#!/usr/bin/env bash
echo "Ejecutando reenvío de datos al controlador..."
curl -X GET http://localhost:5000/datos_controlador.php -v