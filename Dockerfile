# Użyj oficjalnego obrazu PHP 8.2 z FPM
FROM php:8.2-fpm

# Zainstaluj niezbędne rozszerzenia PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    librabbitmq-dev \
    libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Wyczyść niepotrzebne pliki
RUN apt-get clean && rm -rf /var/lib/apt/lists/*