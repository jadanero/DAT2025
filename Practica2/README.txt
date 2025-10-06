Practica 2

Javier Adanero 
Arturo Labajo

Para lanzar el servidor:
php main.php --server

Para lanzar un único el interfaz:
php main.php --ux-only 127.0.0.1:5000

Para lanzar 5 a la vez hemos hecho un pequeño script de bash:
./run_php_clients.sh
En el script se explica como funciona

Se generan aleatoriamente las carpetas de cliente una vez conectadas. Y al desconectarse se borran con todo su contenido. Cada cliente tiene una carpeta de downloads y de uploads.
El cliente puede escribir por terminal ["search","descargar","exit"]. No se ha implementado la API opcional aunque existe la estructura y no deberia de llevar mucho tiempo.





