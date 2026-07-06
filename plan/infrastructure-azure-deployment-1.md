---
goal: Desplegar el sistema Rooms Frontend en Microsoft Azure de la forma más barata posible ($0 - $13 USD/mes)
version: 1.0
date_created: 2026-07-06
last_updated: 2026-07-06
owner: Equipo de Arquitectura y DevOps
status: 'Completed'
tags: [infrastructure, azure, deployment, cost-optimization, php, mvc, supabase]
---

# Introduction

![Status: Completed](https://img.shields.io/badge/status-Completed-brightgreen)

El presente plan de implementación detalla la estrategia arquitectura y el plan de ejecución paso a paso para desplegar la aplicación **Rooms Frontend** en la nube de **Microsoft Azure** al **menor costo posible** ("la forma más barata posible"), cumpliendo estrictamente con los estándares de la skill de optimización de costos y planificación de infraestructura.

Tras un análisis exhaustivo del sistema en `rooms-frontend`, se ha determinado que la aplicación está construida en **PHP Nativo (MVC personalizado sin dependencias de Composer)** y se conecta a una base de datos **PostgreSQL hospedada externamente en Supabase**. Gracias a que la capa de datos ya se encuentra gestionada y sin costo adicional en Supabase, la infraestructura de Azure solo requiere hospedar el tiempo de ejecución (Runtime) web de PHP y el almacenamiento local de archivos (`public/uploads/`), permitiendo un despliegue viable con costo **$0.00 USD/mes** en el plan gratuito de Azure App Service o **~$13.14 USD/mes** en el nivel básico de producción.

---

## 1. Requirements & Constraints

- **REQ-001**: **Soporte de Runtime PHP 8.x**: La infraestructura elegida debe ejecutar PHP 8.2 o superior con extensiones PDO y `pdo_pgsql` habilitadas para comunicarse con Supabase.
- **REQ-002**: **Almacenamiento Persistente para Uploads**: El entorno debe permitir la conservación de los archivos subidos localmente a las rutas `public/uploads/alojamientos/`, `public/uploads/contratos/` y `public/uploads/usuarios/` sin pérdida de datos tras reinicios de instancia.
- **REQ-003**: **Externalización de Credenciales**: Se debe modificar la clase `App\Core\Database` para eliminar las credenciales hardcodeadas de conexión y leerlas dinámicamente desde variables de entorno del servidor.
- **CON-001**: **Presupuesto Mínimo (Cost Optimization)**: El diseño debe priorizar los niveles gratuitos (Free Tier) y de bajo costo en Azure, evitando sobreprovisionar máquinas virtuales o servicios de base de datos redundantes.
- **CON-002**: **Sin Alterar Arquitectura Core**: No se debe reescribir la lógica del patrón MVC nativo ni introducir frameworks pesados que requieran compilar recursos innecesarios.
- **GUD-001**: **Seguridad y SSL**: En un entorno de producción, la comunicación HTTP entre el cliente y Azure, así como entre Azure y la base de datos Supabase, debe estar cifrada con TLS/SSL.
- **PAT-001**: **Patrón Front Controller**: La redirección de todas las peticiones web debe encauzarse hacia `index.php` (mediante reglas de reescritura en Nginx o Apache en Linux App Service).

---

## 2. Implementation Steps

### Implementation Phase 1

- GOAL-001: Refactorización de Seguridad y Configuración del Entorno local y nube (Preparación del Código)

| Task | Description | Completed | Date |
|------|-------------|-----------|------|
| TASK-001 | Modificar [Database.php](file:///C:/Users/Jesus%20Huerta/OneDrive/Desktop/UNIVERSIDAD/CICLO%209/DESARROLLO%20DE%20APP%20WEB/MOD04/rooms-frontend/app/core/Database.php) para reemplazar las credenciales hardcodeadas por lectura de variables de entorno (`getenv('DB_HOST')`, etc.) con valores por defecto de desarrollo. | | |
| TASK-002 | Crear archivo de configuración `.env.example` en la raíz del proyecto para documentar las variables requeridas (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `APP_ENV`, `APP_URL`). | | |
| TASK-003 | Verificar y estandarizar la creación automática de las carpetas de carga en `public/uploads/` asegurando permisos de escritura en sistemas Linux/Windows. | | |

### Implementation Phase 2

- GOAL-002: Aprovisionamiento de Infraestructura en Azure App Service (Opción de Costo $0 / $13 USD)

| Task | Description | Completed | Date |
|------|-------------|-----------|------|
| TASK-004 | Crear Grupo de Recursos en Azure (`rg-rooms-app-dev`) usando Azure CLI o Portal en la región más cercana y económica (ej. `eastus` o `brazilsouth`). | | |
| TASK-005 | Crear un Azure App Service Plan en Linux seleccionando el SKU **F1 (Free - $0.00/mes)** para evaluación/desarrollo o **B1 (Basic - ~$13.14/mes)** para producción con dominio propio. | | |
| TASK-006 | Provisionar la Web App en Linux con Runtime **PHP 8.3** (`az webapp create --name rooms-frontend-app --resource-group rg-rooms-app-dev --plan asp-rooms-free --runtime "PHP|8.3"`). | | |
| TASK-007 | Configurar los Application Settings (variables de entorno) en el App Service con las credenciales de conexión al Pooler de Supabase (`az webapp config appsettings set ...`). | | |

### Implementation Phase 3

- GOAL-003: Configuración de Reescritura de Rutas, Despliegue y Verificación CI/CD

| Task | Description | Completed | Date |
|------|-------------|-----------|------|
| TASK-008 | Configurar el archivo de reescritura de URL para Nginx/Apache en Azure App Service (o configurar archivo `.htaccess` / `startup.sh`) para asegurar que todo el tráfico enrute hacia `index.php`. | | |
| TASK-009 | Configurar el despliegue automatizado desde GitHub Actions o mediante Local Git / Zip Deploy hacia la instancia de Azure App Service. | | |
| TASK-010 | Realizar pruebas de extremo a extremo (E2E): verificación de inicio de sesión, consultas SQL a Supabase, y prueba de subida de imagen de alojamiento en `/admin/alojamientos/crear` comprobando la persistencia en el disco del App Service. | | |

---

## 3. Alternatives

- **ALT-001**: **Azure Container Apps (Serverless Consumption Tier)**:
  - *Descripción*: Empaquetar la aplicación en un contenedor Docker con PHP 8.3-FPM + Nginx.
  - *Costo*: **~$0.00 a $1.50 USD/mes** (incluye 2 millones de peticiones gratis al mes).
  - *Razón de rechazo para v1*: Los contenedores en Container Apps son efímeros por defecto. Al apagarse o escalar a cero, los archivos subidos localmente a `public/uploads/` se eliminarían. Requeriría refactorizar los controladores para integrar Azure Blob Storage o un volumen de Azure Files (~$0.06/GB), añadiendo complejidad al desarrollo.
- **ALT-002**: **Azure Virtual Machine (B1s - Burstable Linux VM)**:
  - *Descripción*: Servidor Linux Ubuntu 22.04 LTS con 1 vCPU y 1 GB de RAM administrado como IaaS.
  - *Costo*: **~$7.50 USD/mes** (o gratis por 12 meses con cuenta nueva de Azure).
  - *Razón de rechazo para v1*: Requiere administración manual del sistema operativo, parches de seguridad, configuración de Nginx, renovación de certificados SSL (Let's Encrypt) y configuración de firewall, aumentando el costo operativo y tiempo de mantenimiento.
- **ALT-003**: **Migrar Base de Datos a Azure Database for PostgreSQL Flexible Server**:
  - *Descripción*: Mover la base de datos de Supabase a un servidor PostgreSQL en Azure (SKU B1ms).
  - *Costo*: **~$12.50 a $15.00 USD/mes adicionales**.
  - *Razón de rechazo para v1*: Viola la restricción de costo mínimo, ya que Supabase actualmente ofrece la capa de base de datos PostgreSQL de forma completamente funcional y gratuita/ya financiada en AWS.

---

## 4. Dependencies

- **DEP-001**: **Supabase PostgreSQL Pooler**: Disponibilidad del endpoint externo `aws-1-us-east-2.pooler.supabase.com` por el puerto `6543`. Se debe verificar que el firewall de Supabase permita conexiones entrantes desde las direcciones IP salientes del App Service de Azure.
- **DEP-002**: **PHP 8.2+ con extensiones**: Runtime de PHP con `pdo`, `pdo_pgsql`, `mbstring`, `gd` o `fileinfo` (necesarias para la validación y manipulación de imágenes subidas).
- **DEP-003**: **Azure CLI / Git**: Herramientas necesarias para la ejecución de comandos de despliegue desde el terminal del desarrollador o pipeline de CI/CD.

---

## 5. Files

- **FILE-001**: [app/core/Database.php](file:///C:/Users/Jesus%20Huerta/OneDrive/Desktop/UNIVERSIDAD/CICLO%209/DESARROLLO%20DE%20APP%20WEB/MOD04/rooms-frontend/app/core/Database.php) - Clase de conexión PDO que será refactorizada para leer variables de entorno (`getenv()`).
- **FILE-002**: [index.php](file:///C:/Users/Jesus%20Huerta/OneDrive/Desktop/UNIVERSIDAD/CICLO%209/DESARROLLO%20DE%20APP%20WEB/MOD04/rooms-frontend/index.php) - Front Controller principal del sistema y enrutador.
- **FILE-003**: `.env.example` - [NUEVO] Plantilla de variables de entorno para estandarizar configuración local y en nube.
- **FILE-004**: `startup.sh` / `.htaccess` - [NUEVO] Script de inicialización o archivo de configuración web para el servidor en Azure App Service Linux.

---

## 6. Testing

- **TEST-001**: **Test de Conectividad a Supabase desde Azure**: Verificar mediante un script CLI en la consola SSH de Azure App Service que PDO puede establecer conexión satisfactoriamente por el puerto 6543.
- **TEST-002**: **Test de Subida de Archivos (Upload Test)**: Ejecutar un flujo de prueba en el panel de administrador subiendo una imagen de alojamiento o contrato y verificando su accesibilidad pública desde la URL `/public/uploads/...`.
- **TEST-003**: **Test de Enrutamiento (Front Controller Test)**: Comprobar que rutas profundas (ej. `/admin/foros` o `/login`) no devuelvan error 404 del servidor web y sean procesadas por `App\Core\Router`.

---

## 7. Risks & Assumptions

- **RISK-001**: **Límite de CPU en el Plan Gratuito (F1)**: El plan F1 tiene un límite estricto de 60 minutos de CPU de cómputo por día. Si se excede, Azure detiene la aplicación hasta el día siguiente. *Mitigación*: Utilizar el plan F1 solo para desarrollo/pruebas. Para producción pública, pasar al plan B1 (~$13/mes).
- **RISK-002**: **Almacenamiento Local No Escalable horizontalmente**: Si en el futuro se escala el App Service a múltiples instancias (ej. 2 o más nodos), los archivos en `/home` podrían tener problemas de sincronización en peticiones recurrentes si no se usa almacenamiento compartido. *Mitigación*: En App Service Linux, el directorio `/home` está respaldado por Azure Storage compartido entre instancias, por lo que es seguro para escalado básico.
- **ASSUMPTION-001**: La base de datos de Supabase seguirá estando accesible remotamente y no se encuentra restringida por una lista blanca de IPs cerrada (o se agregarán las IPs de Azure).
- **ASSUMPTION-002**: El tráfico inicial del sistema no requerirá un ancho de banda de salida masivo que genere costos imprevistos de egreso de datos en Azure (los primeros 100 GB/mes de salida en Azure son gratuitos).

---

## 8. Related Specifications / Further Reading

- [Estándares Frontend y Arquitectura](file:///C:/Users/Jesus%20Huerta/OneDrive/Desktop/UNIVERSIDAD/CICLO%209/DESARROLLO%20DE%20APP%20WEB/MOD04/rooms-frontend/docs/ESTANDARES_FRONTEND.md)
- [Documentación del Backend](file:///C:/Users/Jesus%20Huerta/OneDrive/Desktop/UNIVERSIDAD/CICLO%209/DESARROLLO%20DE%20APP%20WEB/MOD04/rooms-frontend/docs/DOCUMENTACION_BACKEND.md)
- [Precios de Azure App Service](https://azure.microsoft.com/es-es/pricing/details/app-service/linux/)
