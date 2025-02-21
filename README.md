# Task Manager API

Una API RESTful robusta para gestión de proyectos y tareas, desarrollada con Laravel.

## 🚀 Características

- Autenticación de usuarios con Laravel Sanctum
- Gestión completa de proyectos
- Sistema de tareas con prioridades y estados
- Comentarios en tareas
- Gestión de archivos adjuntos
- Sistema de etiquetas
- Notificaciones
- Estadísticas de proyectos

## 🛠️ Tecnologías Utilizadas

- PHP 8.x
- Laravel 10.x
- MySQL/PostgreSQL
- Laravel Sanctum para autenticación
- Composer

## 📋 Requisitos Previos

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Node.js y NPM (para assets)

## ⚙️ Instalación

1. Clonar el repositorio
    git clone https://github.com/TU_USUARIO/Task-Manager.git
    cd Task-Manager

2. Instalar dependencias
    composer install

3. Configurar el entorno
    cp .env.example .env
    php artisan key:generate

4. Configurar la base de datos en el archivo .env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=task_manager
    DB_USERNAME=root
    DB_PASSWORD=

5. Ejecutar migraciones
php artisan migrate

🔍 Endpoints API
    Autenticación
        POST /api/login - Iniciar sesión
        POST /api/register - Registrar usuario
        POST /api/logout - Cerrar sesión
    Proyectos
        GET /api/projects - Listar proyectos
        POST /api/projects - Crear proyecto
        GET /api/projects/{id} - Obtener proyecto
        PUT /api/projects/{id} - Actualizar proyecto
        DELETE /api/projects/{id} - Eliminar proyecto
        GET /api/projects/{id}/stats - Estadísticas del proyecto
    Tareas
        GET /api/tasks - Listar tareas
        POST /api/tasks - Crear tarea
        GET /api/tasks/{id} - Obtener tarea
        PUT /api/tasks/{id} - Actualizar tarea
        DELETE /api/tasks/{id} - Eliminar tarea
🔐 Seguridad
    Autenticación mediante tokens (Sanctum)
    Validación de datos en todas las peticiones
    Protección CSRF
    Manejo de permisos por usuario

🧪 Pruebas
Para ejecutar las pruebas:
php artisan test

👥 Contribución
Las contribuciones son bienvenidas. Por favor, lee las guías de contribución antes de enviar un pull request.

🤝 Soporte
Si encuentras un bug o tienes una sugerencia, por favor abre un issue en GitHub.

Desarrollado por Michael Smith Correa Lopez | 2025
