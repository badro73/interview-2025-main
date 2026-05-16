# --- STAGE 1 : BASE (Commun) ---
FROM php:8.3-fpm-alpine AS base

# Installation des dépendances système nécessaires
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    rabbitmq-c-dev \
    build-base \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install bcmath intl pdo_mysql zip gd mbstring opcache \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && apk del $PHPIZE_DEPS # On nettoie les outils de compilation pour gagner de la place

# On récupère Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# --- STAGE 2 : DEVELOPMENT (Pour ton local) ---
FROM base AS dev
WORKDIR /app
# En local, on garde php-fpm seul car Nginx est dans un autre container
CMD ["php-fpm"]

# --- STAGE 3 : PRODUCTION (Pour AWS ECS) ---
FROM base AS prod

ENV APP_ENV=prod
ENV COMPOSER_ALLOW_SUPERUSER=1

# 1. Installation Nginx + Supervisor et création du dossier de logs
RUN apk add --no-cache nginx supervisor \
    && mkdir -p /var/log/supervisor

# 2. Configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
RUN sed -i 's/fastcgi_pass php:9000/fastcgi_pass 127.0.0.1:9000/g' /etc/nginx/http.d/default.conf

# 3. Configuration Supervisor (On le met à la racine /etc pour plus de simplicité)
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# 4. Copie du code source
WORKDIR /var/www/app
COPY . .

# 5. Installation des dépendances
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 6. Droits Symfony
RUN chown -R www-data:www-data /var/www/app/var

EXPOSE 80

# 7. Un SEUL CMD qui pointe vers le bon fichier
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]