FROM hub.madelineproto.xyz/danog/madelineproto

RUN apk add --no-cache \
    docker-cli \
    docker-compose \
    curl \
    php8-cli \
    php8-openssl \
    php8-json \
    php8-phar \
    php8-zlib \
    git \
    unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

RUN composer --version
