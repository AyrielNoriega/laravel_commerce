# Laravel Commerce API

Este proyecto es una API creada con Laravel 10 y configurada para ejecutarse en un entorno Docker.

## Requisitos

- Docker 25.0.3
- Docker Compose v2.24.6-desktop.1
- PHP 8.1.17
- Composer 2.5.8


## Instalación

1. Clona el repositorio:
    ```bash
    git clone https://github.com/tu_usuario/laravel_commerce.git
    cd laravel_commerce
    ```

2. Copia el archivo de entorno:
    ```bash
    cp .env.example .env
    ```

3. Genera la clave de la aplicación:
    ```bash
    php artisan key:generate
    ```
4. Construye y levanta los contenedores Docker:
    ```bash
    docker-compose up -d
    ```
5. Ejecuta las migraciones de la base de datos:
    ```bash
    docker-compose exec app php artisan migrate
    ```
6. Ejecuta los seeder:
    ```bash
    docker-compose exec app php artisan db:seed
    ```


## Uso

Para acceder a la API, abre tu navegador y ve a `http://localhost:8000/`.

## Endpoints

La documentación de la API está generada con Swagger. Puedes acceder a la documentación interactiva en `http://localhost:8000/api/documentation`.


## Contribuir

Si deseas contribuir a este proyecto, por favor sigue estos pasos:

1. Haz un fork del repositorio.
2. Crea una nueva rama (`git checkout -b feature/nueva-funcionalidad`).
3. Realiza tus cambios y haz commit (`git commit -am 'Añadir nueva funcionalidad'`).
4. Sube tus cambios (`git push origin feature/nueva-funcionalidad`).
5. Abre un Pull Request.
