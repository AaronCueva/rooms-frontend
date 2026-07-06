#!/bin/bash
# Script automatizado en Bash para aprovisionar y desplegar Rooms Frontend en Azure App Service.
# Diseñado para el menor costo posible (Plan F1 Gratis o Plan B1 Básico).

set -e

RESOURCE_GROUP="${1:-rg-rooms-app-dev}"
LOCATION="${2:-eastus}"
APP_NAME="${3:-rooms-frontend-$RANDOM}"
SKU="${4:-F1}" # F1 para Gratis ($0/mes), B1 para Básico (~$13/mes)

DB_HOST="aws-1-us-east-2.pooler.supabase.com"
DB_PORT="6543"
DB_NAME="postgres"
DB_USER="postgres.lokjiueialuwrulybgut"
DB_PASSWORD="${DB_PASSWORD:-tu_password_aqui}"

echo "==================================================================="
echo " 🚀 Aprovisionamiento en Microsoft Azure - Rooms Frontend ($SKU)"
echo "==================================================================="

if ! command -v az &> /dev/null; then
    echo "Error: Azure CLI ('az') no está instalado."
    exit 1
fi

echo ""
echo "1. Creando Grupo de Recursos '$RESOURCE_GROUP' en '$LOCATION'..."
az group create --name "$RESOURCE_GROUP" --location "$LOCATION" --output none
echo "   ✅ Grupo de Recursos preparado."

echo ""
echo "2. Creando App Service Plan '$RESOURCE_GROUP-plan' con SKU '$SKU' (Linux)..."
az appservice plan create --name "$RESOURCE_GROUP-plan" --resource-group "$RESOURCE_GROUP" --sku "$SKU" --is-linux --output none
echo "   ✅ App Service Plan preparado."

echo ""
echo "3. Creando Web App '$APP_NAME' con Runtime PHP 8.3..."
az webapp create --name "$APP_NAME" --resource-group "$RESOURCE_GROUP" --plan "$RESOURCE_GROUP-plan" --runtime "PHP|8.3" --output none
echo "   ✅ Web App '$APP_NAME' creada."

echo ""
echo "4. Configurando Variables de Entorno (Conexión a Supabase)..."
az webapp config appsettings set --name "$APP_NAME" --resource-group "$RESOURCE_GROUP" --settings \
    DB_HOST="$DB_HOST" \
    DB_PORT="$DB_PORT" \
    DB_NAME="$DB_NAME" \
    DB_USER="$DB_USER" \
    DB_PASSWORD="$DB_PASSWORD" \
    APP_ENV="production" \
    APP_URL="https://$APP_NAME.azurewebsites.net" --output none
echo "   ✅ Application Settings configuradas exitosamente."

echo ""
echo "5. Configurando Script de Inicio (startup.sh) para enrutamiento Nginx..."
az webapp config set --name "$APP_NAME" --resource-group "$RESOURCE_GROUP" --startup-file "/home/site/wwwroot/azure/startup.sh" --output none
echo "   ✅ Startup script asignado."

echo ""
echo "==================================================================="
echo " 🎯 ¡Infraestructura lista!"
echo " 👉 URL de tu aplicación: https://$APP_NAME.azurewebsites.net"
echo "==================================================================="
