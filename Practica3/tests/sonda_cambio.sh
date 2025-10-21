#!/usr/bin/env bash
while sleep 10; do
    curl -X GET -u sonda1:1234 http://localhost:5000/comando.php -v
done
