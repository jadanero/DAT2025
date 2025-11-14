#!/usr/bin/env bash
echo "Ejecutando reenvio de datos al controlador"
curl -X GET http://192.168.8.1:8080/practica3/agregador/datos_controlador.php 