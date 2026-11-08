FROM php:8.3-cli-alpine

# Install system dependencies & PostgreSQL extensions for Laravel 11
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    bcmath \
    zip \
    intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
