FROM php:8.4-fpm-alpine

# Install packages
RUN apk add --no-cache \
    nginx \
    git \
    unzip \
    curl \
    zip \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure nginx
COPY ./.docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Create nginx folders
RUN mkdir -p /run/nginx

# Set workdir
WORKDIR /var/www

# Copy project
COPY . .

# Laravel
WORKDIR /var/www/backend

RUN composer install

# Permissions
RUN chmod -R 777 storage bootstrap/cache

# Expose port
EXPOSE 80
CMD ["sh", "-c", "until nc -z mysql 3306; do echo waiting mysql; sleep 2; done; php artisan migrate --force; php artisan migrate:fresh --seed; php-fpm -D; nginx -g 'daemon off;'"]