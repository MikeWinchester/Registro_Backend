#!/bin/bash

echo "Creando script de arranque con permisos correctos..."

# Reescribir el script en tiempo de ejecución
cat <<EOF > /home/site/start.sh
#!/bin/bash
nginx -g "daemon off;"
EOF

# Dar permisos de ejecución
chmod +x /home/site/start.sh

# Ejecutar el script generado
/home/site/start.sh
