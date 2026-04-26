FROM php:8.3-fpm-alpine AS app

WORKDIR /var/www

RUN apk add --no-cache \
    bash \
    curl \
    git \
    freetype-dev \
    icu-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    libpng-dev \
    libpq-dev \
    oniguruma-dev \
    postgresql-client \
    unzip \
    zip \
    $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring intl zip gd \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]
