# Guía de Despliegue en Microsoft Azure (Rooms Frontend)

Este directorio contiene los scripts y archivos de configuración para desplegar **Rooms Frontend** en Microsoft Azure de la forma más económica posible ($0/mes en Plan Gratuito F1 o ~$13/mes en Plan Básico B1).

## Contenido de la Carpeta
- **`deploy.ps1`**: Script automatizado para **Windows PowerShell**. Aprovisiona el grupo de recursos, el App Service Plan y la Web App en Linux, configurando automáticamente variables de entorno y script de arranque.
- **`deploy.sh`**: Script de despliegue equivalente en **Bash** para entornos Linux, macOS o pipelines de CI/CD (GitHub Actions).
- **`nginx.conf`**: Configuración personalizada de Nginx para el patrón MVC *Front Controller* (`try_files -> index.php`) y protección de directorios sensibles (`/app`, `/docs`, `.env`).
- **`startup.sh`**: Script que ejecuta Azure App Service al iniciar la instancia para cargar el archivo `nginx.conf` y verificar que las carpetas de carga de archivos (`public/uploads`) tengan permisos de escritura.

---

## 🚀 Cómo Desplegar en 3 Pasos (Desde Windows)

### Paso 1: Instalar y loguearse en Azure CLI
Asegúrate de tener instalado [Azure CLI para Windows](https://aka.ms/installazurecliwindows). Abre tu terminal PowerShell e inicia sesión en tu cuenta de Microsoft Azure:
```powershell
az login
```

### Paso 2: Ejecutar el script de aprovisionamiento
Desde la raíz del proyecto, ejecuta el script de PowerShell (por defecto creará el plan **F1 Gratuito** con costo **$0.00/mes**):
```powershell
.\azure\deploy.ps1 -DbPassword "contraseña_real_de_supabase"
```

> **Nota para producción:** Si deseas utilizar tu propio dominio HTTPS o requieres más de 60 minutos diarios de CPU, añade el parámetro `-Sku "B1"` para crear el nivel Básico (~$13 USD/mes):
> ```powershell
> .\azure\deploy.ps1 -Sku "B1" -DbPassword "contraseña_real_de_supabase"
> ```

### Paso 3: Subir tu código vía Git local o Zip Deploy

**Opción A: Despliegue rápido con archivo Zip (Recomendado para pruebas instantáneas)**
En la terminal PowerShell desde la raíz del proyecto:
```powershell
# Empaquetar el código (excluyendo git y archivos innecesarios)
Compress-Archive -Path * -DestinationPath app.zip -Force

# Desplegar en Azure (reemplaza <nombre-app> por el nombre generado por el script)
az webapp deploy --resource-group rg-rooms-app-dev --name <nombre-app> --src-path app.zip --type zip
```

**Opción B: Despliegue con Git Local (Estándar)**
1. Habilita el despliegue Git en tu Web App:
   ```powershell
   az webapp deployment source config-local-git --name <nombre-app> --resource-group rg-rooms-app-dev
   ```
2. Añade el repositorio remoto de Azure y haz push de tu rama principal:
   ```powershell
   git remote add azure <URL_DE_GIT_DEVUELTA_POR_AZURE>
   git add .
   git commit -m "feat: Azure deployment configuration"
   git push azure master
   ```

---

## 🛠️ Verificación y Solución de Problemas (Troubleshooting)

1. **Verificar que Nginx enrute correctamente (`/login`, `/admin`):**
   Si recibes error `404 Not Found`, verifica en el portal de Azure en **Configuration -> General settings -> Startup Command** que el comando sea: `/home/site/wwwroot/azure/startup.sh`.
2. **Revisar logs en vivo:**
   Puedes ver la consola de salida de PHP y Nginx en tiempo real con:
   ```powershell
   az webapp log tail --name <nombre-app> --resource-group rg-rooms-app-dev
   ```
3. **Conexión a Base de Datos Supabase:**
   Si la base de datos no conecta, asegúrate de que el firewall del proyecto en Supabase permita conexiones públicas (o que las IPs de salida de tu App Service de Azure estén autorizadas por el puerto 6543).
