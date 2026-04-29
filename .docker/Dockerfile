FROM php:8.4-fpm-alpine

# Installer dépendances système
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# Installer extensions PHP nécessaires
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip

# Installer Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Définir dossier de travail
WORKDIR /var/www

# Copier le projet
COPY . .

# Permissions Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Exposer port PHP-FPM
EXPOSE 9000

# Lancer PHP-FPM
CMD ["php-fpm"]
