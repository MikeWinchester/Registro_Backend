#!/bin/bash

# Configuración de variables
DB_NAME="bd_registro"
DB_USER="winchester"
DB_HOST="bd-registro.mysql.database.azure.com"
DB_PORT="3306"
SQL_FILE="database.sql"

echo -n "Ingrese la contraseña para el usuario '$DB_USER': "
read -s DB_PASS
echo ""

# Conectar a MySQL y crear la base de datos
echo "Creando la base de datos '$DB_NAME' en Azure MySQL..."
mysql --host="$DB_HOST" \
      --port="$DB_PORT" \
      --user="$DB_USER" \
      --password="$DB_PASS" \
      --execute="DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [ $? -eq 0 ]; then
    echo "Base de datos '$DB_NAME' creada exitosamente."
else
    echo "Error al crear la base de datos."
    exit 1
fi

# Ejecutar script SQL para poblar la base de datos
if [ -f "$SQL_FILE" ]; then
    echo "Ejecutando script SQL '$SQL_FILE' en la base de datos '$DB_NAME'..."
    mysql --host="$DB_HOST" \
          --port="$DB_PORT" \
          --user="$DB_USER" \
          --password="$DB_PASS" \
          "$DB_NAME" < "$SQL_FILE"

    if [ $? -eq 0 ]; then
        echo "Script SQL ejecutado correctamente."
    else
        echo "Error al ejecutar el script SQL."
        exit 1
    fi
else
    echo "No se encontró el archivo '$SQL_FILE'."
    exit 1
fi
