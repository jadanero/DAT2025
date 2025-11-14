////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                                PRACTICA 3 
                                                Grupo: 01
                                                Miembros: Arturo Labajo y Javier Adanero
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
En esta practica de ha desarrollado un sistema IoT compuesto por:
    - Sonda: Simulada mediante tests 
    - Agregador: Router que actua como intermediario 
    - Controlador: Servidor webalumnos que es el centro de control del sistema

La idea general es:
    1- La sonda envia datos -> el router los guarda y los reenvia al servidor
    2- El servidor genera acciones -> el router las descarga y se las entrega a la sonda correspondiente
    3- Las sondas ejecuta acciones -> el router recoge el resultado y lo reenvia al servidor

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
SONDA:

Las sondas son las encargadas de recoger informacion, consultar ordenes y confirmar acciones

En esta practica, las sondas estan simuladas mediante scripts en Bash  que realizan peticiones al router y al servidor

    1- Envio de datos (sonda.sh)
    2- Consultar acciones (sonda_cambio.sh)
    3- Inpeccionar los resultados que tiene almacenado el router
    4- Simula la taria del router de enviar los datos JSON al servidor
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
AGREGADOR (Router)

El agregador hace de intermediario entre la sonda y el Controlador

    1- Recibir datos de la sonda (dato.php)
    2- Enviar al servidor los datos (datos_agregador.php)
    3- Descargar acciones pendientes desde el servidor (actualizarAcciones.php)
    4- La sonda consulta si tiene acciones pendientes (comando.php)
    5- Recibir resultado de una accion ejecutada por la sonda (cambioestado_agregador.php)
    6- Enviar al servidor los resultado de ejecucion (resultados_agregador.php)
    7- Validar la sonda (autentification.php)
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
CONTROLADOR (Servidor)

El servidor es el controlador principal del sistema

    1- Recibir datos del router (recibirDatos.php)
    2- Enviar al router las acciones pendientes (enviarAcciones.php)
    3- Recibe los resultados de acciones ejecutadas por las sondas (recibirResultados.php)
    4- (Opcional) Panel web para visualizar y gestionar (panelControl.php)
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                                    FIN
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////