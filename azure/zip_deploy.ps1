param (
    [string]$ResourceGroup = "rg-rooms-app-dev",
    [string]$AppName = "rooms-frontend-4973"
)

Write-Host "===================================================================" -ForegroundColor Cyan
Write-Host " Empaquetando proyecto para Despliegue Rapido (ZIP Deploy)..." -ForegroundColor Cyan
Write-Host "===================================================================" -ForegroundColor Cyan

Remove-Item -Path "app.zip" -ErrorAction SilentlyContinue

# Empaquetar usando git archive para garantizar estructura POSIX (/) compatible con servidores Linux de Azure
git add .
git commit -m "deploy: update for Azure" -q 2>$null
git archive --format=zip -o app.zip HEAD

Write-Host " Archivo app.zip creado con exito. Configurando SCM y subiendo por Kudu ZipDeploy..." -ForegroundColor Yellow
az webapp config appsettings set --resource-group $ResourceGroup --name $AppName --settings SCM_DO_BUILD_DURING_DEPLOYMENT=false --output none
az webapp deployment source config-zip --resource-group $ResourceGroup --name $AppName --src "app.zip" --output none

Write-Host "===================================================================" -ForegroundColor Green
Write-Host " Despliegue Completado con Exito!" -ForegroundColor Green
Write-Host " URL en vivo: https://$AppName.azurewebsites.net" -ForegroundColor Cyan
Write-Host " Nota: El contenedor Linux puede tardar de 30 a 60 segundos en refrescarse." -ForegroundColor Gray
Write-Host "===================================================================" -ForegroundColor Green

Remove-Item -Path "app.zip" -ErrorAction SilentlyContinue
