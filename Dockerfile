FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    locales \
    --no-install-recommends

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo_mysql \
    intl \
    opcache \
    bcmath \
    soap \
    zip \
    exif \
    pcntl \
    mysqli \
    pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN sed -i 's/# ru_RU.UTF-8 UTF-8/ru_RU.UTF-8 UTF-8/' /etc/locale.gen && \
    locale-gen

ENV LANG ru_RU.UTF-8
ENV LANGUAGE ru_RU:en
ENV LC_ALL ru_RU.UTF-8

COPY . /var/www/symfony

WORKDIR /var/www/symfony
RUN chmod +x entrypoint.sh
RUN cp .example.env .env

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-scripts --no-progress --prefer-dist

EXPOSE 9000

CMD ["php-fpm"]