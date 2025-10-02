FROM php:8.4-fpm-alpine

#RUN apk --no-cache add shadow sudo

RUN apk add --update linux-headers

RUN apk update && apk add --no-cache \
    $PHPIZE_DEPS \
    bash \
    git \
    libmcrypt-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    nodejs \
    npm \
    openssl \
    unzip \
    vim \
    wget \
    zip \
    icu-dev

RUN docker-php-ext-install \
    bcmath \
    pdo \
    mysqli \
    pdo_mysql \
    zip \
    intl

RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    && docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/ \
    --with-webp=/usr/include/ \
    && docker-php-ext-install gd

RUN apk add --no-cache \
    supervisor

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

# fix this issue https://ask.fedoraproject.org/t/sudo-setrlimit-rlimit-core-operation-not-permitted/4223
RUN echo "Set disable_coredump false" >> /etc/sudo.conf


RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp_linux -o /usr/local/bin/youtube-dl
RUN chmod a+rx /usr/local/bin/youtube-dl

# Clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/*
RUN deluser www-data 2>/dev/null || true && \
    delgroup www-data 2>/dev/null || true && \
    addgroup -g 1000 www-data && \
    adduser -u 1000 -D -S -G www-data www-data
RUN chown -R www-data:www-data /var/www/html

USER www-data
