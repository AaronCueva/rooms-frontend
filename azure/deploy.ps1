param (
    [string]$ResourceGroup = "rg-rooms-app-dev",
    [string]$Location = "eastus",
    [string]$AppName = "rooms-frontend-" + ((Get-Random -Minimum 1000 -Maximum 9999).ToString()),
    [string]$Sku = "F1",
    [string]$DbHost = "aws-1-us-east-2.pooler.supabase.com",
    [string]$DbPort = "6543",
    [string]$DbName = "postgres",
    [string]$DbUser = "postgres.lokjiueialuwrulybgut",
    [string]$DbPassword = "tu_password_aqui"
)

Write-Host "===================================================================" -ForegroundColor Cyan
Write-Host " Aprovisionamiento en Microsoft Azure - Rooms Frontend ($Sku)" -ForegroundColor Cyan
Write-Host "===================================================================" -ForegroundColor Cyan

if (-not (Get-Command "az" -ErrorAction SilentlyContinue)) {
    Write-Error "El comando 'az' no esta instalado. Instala Azure CLI."
    exit 1
}

Write-Host "`n1. Verificando sesion en Azure..." -ForegroundColor Yellow
$loginCheck = az account show 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "Iniciando sesion en Azure..." -ForegroundColor Yellow
    az login | Out-Null
}
$subscription = (az account show --query name -o tsv)
Write-Host "   Conectado a la suscripcion: $subscription" -ForegroundColor Green

Write-Host "`n2. Creando Grupo de Recursos '$ResourceGroup' en '$Location'..." -ForegroundColor Yellow
az group create --name $ResourceGroup --location $Location --output none
Write-Host "   Grupo de Recursos preparado." -ForegroundColor Green

Write-Host "`n3. Creando App Service Plan '$ResourceGroup-plan' con SKU '$Sku' (Linux)..." -ForegroundColor Yellow
az appservice plan create --name "$ResourceGroup-plan" --resource-group $ResourceGroup --sku $Sku --is-linux --output none
Write-Host "   App Service Plan preparado." -ForegroundColor Green

Write-Host "`n4. Creando Web App '$AppName' con Runtime PHP 8.3..." -ForegroundColor Yellow
az webapp create --name $AppName --resource-group $ResourceGroup --plan "$ResourceGroup-plan" --runtime "PHP:8.3" --output none
Write-Host "   Web App '$AppName' creada." -ForegroundColor Green

Write-Host "`n5. Configurando Variables de Entorno (Conexion a Supabase)..." -ForegroundColor Yellow
$appUrl = "https://" + $AppName + ".azurewebsites.net"
az webapp config appsettings set --name $AppName --resource-group $ResourceGroup --settings DB_HOST=$DbHost DB_PORT=$DbPort DB_NAME=$DbName DB_USER=$DbUser DB_PASSWORD=$DbPassword APP_ENV="production" APP_URL=$appUrl --output none
Write-Host "   Application Settings configuradas exitosamente." -ForegroundColor Green

Write-Host "`n6. Configurando Script de Inicio para enrutamiento Nginx..." -ForegroundColor Yellow
az webapp config set --name $AppName --resource-group $ResourceGroup --startup-file "bash /home/site/wwwroot/azure/startup.sh" --output none
Write-Host "   Startup script asignado." -ForegroundColor Green

Write-Host "`n===================================================================" -ForegroundColor Green
Write-Host " Infraestructura lista!" -ForegroundColor Green
Write-Host " URL de tu aplicacion: $appUrl" -ForegroundColor Cyan
Write-Host " Para desplegar tu codigo ejecuta:" -ForegroundColor White
Write-Host "    az webapp deployment source config-local-git --name $AppName --resource-group $ResourceGroup" -ForegroundColor Gray
Write-Host "    git remote add azure [URL-GIT-DEVUELTA]" -ForegroundColor Gray
Write-Host "    git push azure master" -ForegroundColor Gray
Write-Host "===================================================================" -ForegroundColor Green
