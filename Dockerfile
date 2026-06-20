FROM php:8.4-apache

# Extensions PHP nécessaires pour Laravel
RUN apt-get update && apt-get install -y \
      git curl zip unzip libzip-dev libpng-dev \
      libonig-dev libxml2-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Apache : pointer vers /public et activer mod_rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
    && a2enmod rewrite

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dépendances PHP (sans dev)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Dépendances Node + build Vite
COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources
RUN npm ci && npm run build

# Reste du projet
COPY . .

# Permissions Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Cache Laravel pour la production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 80

# Migrations au démarrage, puis Apache
CMD php artisan migrate --force --seed --seeder=AdminSeeder && apache2-foreground
