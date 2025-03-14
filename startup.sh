#!/bin/bash

# Ejecutar el script para configurar NGINX y reiniciar el servicio
/home/site/wwwroot/start_nginx.sh

# Mantener el contenedor en ejecución (esto es importante en Azure App Service)
tail -f /dev/null
