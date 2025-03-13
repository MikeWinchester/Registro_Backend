# Copiar el archivo nginx.conf al directorio de configuración de NGINX
cp /home/site/wwwroot/nginx.conf /etc/nginx/nginx.conf

# Reiniciar NGINX para aplicar la nueva configuración
service nginx restart