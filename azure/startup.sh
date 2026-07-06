#!/bin/bash
# Script de inicio (Startup Script) para Azure App Service Linux
# Se configura en Azure CLI con: --startup-file "/home/site/wwwroot/azure/startup.sh"

echo "=== [Rooms Frontend] Iniciando Startup Script en Azure App Service ==="

# 1. Aplicar configuración personalizada de Nginx para el enrutador MVC (try_files -> index.php)
if [ -f "/home/site/wwwroot/azure/nginx.conf" ]; then
    echo "Aplicando archivo de configuración Nginx personalizado..."
    cp -f /home/site/wwwroot/azure/nginx.conf /etc/nginx/sites-available/default 2>/dev/null
    cp -f /home/site/wwwroot/azure/nginx.conf /etc/nginx/sites-enabled/default 2>/dev/null
    cp -f /home/site/wwwroot/azure/nginx.conf /etc/nginx/conf.d/default.conf 2>/dev/null
    service nginx reload 2>/dev/null || nginx -s reload 2>/dev/null || true
    echo "Nginx configurado para MVC try_files exitosamente."
fi

# 2. Asegurar la existencia y permisos de las carpetas locales de carga de archivos (uploads)
echo "Verificando carpetas de subida de archivos (public/uploads)..."
mkdir -p /home/site/wwwroot/public/uploads/alojamientos
mkdir -p /home/site/wwwroot/public/uploads/contratos
mkdir -p /home/site/wwwroot/public/uploads/usuarios

# Asignar permisos de lectura y escritura para el servidor web en Linux
chmod -R 777 /home/site/wwwroot/public/uploads

echo "=== [Rooms Frontend] Configuración finalizada. Servidor listo ==="
