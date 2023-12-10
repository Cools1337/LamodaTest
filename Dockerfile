FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    zlib1g-dev \
    libpng-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]
