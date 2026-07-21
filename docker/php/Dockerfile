FROM php:8.2-fpm

# Instalar extensiones de sistema y utilidades
RUN apt-get update && apt-get install -y \
    libmariadb-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar y habilitar extensión mysqli para conexión a la DB
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Ajustar permisos (opcional, pero buena práctica)
RUN chown -R www-data:www-data /var/www/html
