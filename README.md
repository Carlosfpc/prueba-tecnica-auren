# Prueba Técnica Auren - Módulo de Gestión de Países

Este repositorio contiene la implementación de una aplicación web completa desarrollada como parte del proceso de selección de Auren. La aplicación, construida con **Laravel 11** y **Filament 3**, gestiona y expone información sobre países del mundo a través de un panel de administración y una API pública RESTful.

El proyecto está completamente **contenedorizado con Docker** para garantizar un entorno de desarrollo consistente y una puesta en marcha simplificada. Se ha puesto un fuerte énfasis en seguir los principios de **Código Limpio** y **SOLID**, así como en la implementación de características avanzadas como el procesamiento en segundo plano y la documentación de API interactiva.

## ✨ Funcionalidades Principales

- **Panel de Administración (Filament):** CRUD completo para la gestión de países con filtros, búsqueda y ordenación.
- **Sincronización de Datos Automatizada:** Acción para poblar la base de datos desde la API pública de [Rest Countries](https://restcountries.com/).
- **Procesamiento Asíncrono:** La sincronización se delega a una cola de trabajos para no bloquear la interfaz y mejorar la robustez.
- **API Pública RESTful:** Endpoints para listar países (con filtros) y consultar detalles por código.
- **Documentación Interactiva de API:** Interfaz de Swagger UI generada automáticamente para explorar y probar los endpoints.
- **Autorización Basada en Policies:** Control de acceso granular al módulo de países en el panel de administración.
- **Configuración Automatizada:** Gracias a un script de `entrypoint`, el proyecto se configura completamente con un único comando.

## 🚀 Stack Tecnológico

- **Backend:** PHP 8.2, Laravel 11
- **Panel de Administración:** Filament 3
- **Base de Datos:** MySQL 8
- **Servidor Web:** Nginx
- **Contenerización:** Docker & Docker Compose
- **Testing:** PHPUnit
- **Documentación de API:** OpenAPI (Swagger) a través de `l5-swagger`

---

## SETUP: Guía de Instalación y Despliegue Local

Sigue estos pasos para poner en marcha la aplicación.

### Prerrequisitos

- Tener **Docker** y **Docker Compose** instalados y en ejecución.
- (Opcional) Tener Composer instalado localmente si se desean ejecutar comandos fuera de Docker.

### Pasos de Instalación

1.  **Clonar el Repositorio**
    ```bash
    git clone https://github.com/Carlosfpc/prueba-tecnica-auren.git
    cd prueba-tecnica-auren
    ```

2.  **Crear el Archivo de Entorno (`.env`)**
    Este es el único paso manual requerido antes de iniciar los contenedores. Docker Compose lo necesita para configurar la base de datos en el primer arranque.
    ```bash
    cp .env.example .env
    ```

3.  **Levantar el Entorno con un Solo Comando**
    Este comando construirá las imágenes de Docker y ejecutará el script de configuración automática.
    ```bash
    docker compose up -d --build
    ```
    **¡Y ya está!** El script de `entrypoint` se encargará del resto:
    - Instalará las dependencias de Composer.
    - Generará la clave de la aplicación.
    - Esperará a que la base de datos esté lista.
    - Ejecutará las migraciones.
    - Ejecutará los seeders para crear los usuarios de prueba.
    - Ejecutará la sincronización inicial de países de forma síncrona.

    *Puedes monitorizar el progreso en tiempo real con `docker compose logs -f app`.*

---

## 📖 Guía de Uso

### Panel de Administración

- **URL:** [http://localhost:8000/admin](http://localhost:8000/admin)
- **Credenciales:** Se han creado dos usuarios para demostrar el sistema de permisos (Policies):

  - **Usuario con Permisos:**
    - **Email:** `user@auren-con-permiso.com`
    - **Contraseña:** `password`
    *(Este usuario podrá ver y gestionar el módulo de Países)*

  - **Usuario sin Permisos:**
    - **Email:** `user@auren-sin-permiso.com`
    - **Contraseña:** `password`
    *(Este usuario no verá el módulo de Países en el menú)*

### API Pública

- **Documentación Interactiva (Swagger):** [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)
  Desde aquí puedes explorar y probar todos los endpoints.

- **Endpoints Principales:**
  - `GET /api/countries`: Lista paginada de países.
  - `GET /api/countries/{cca3}`: Obtiene un país por su código de 3 letras.

### Tareas en Segundo Plano (Jobs)

Para procesar futuras sincronizaciones (ejecutadas desde Filament o el comando `countries:sync` sin la opción `--now`), necesitas tener un "worker" de la cola activo.

- **Iniciar el Worker:**
  Abre una **nueva terminal** y déjala corriendo con el siguiente comando:
  ```bash
  docker compose exec app php artisan queue:work