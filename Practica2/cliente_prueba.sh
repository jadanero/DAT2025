#!/bin/bash
nc 127.0.0.1 5001 <<< "GET /peers/10
Content-Length:

prueba1.txt
prueba2.txt"