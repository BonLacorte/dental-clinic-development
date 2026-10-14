# Local development runtime, not a production container.
FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
      git unzip libjpeg62-turbo-dev libpng-dev libfreetype6-dev \
      libzip-dev libonig-dev libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring gd zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN printf '%s\n' \
      'date.timezone=Asia/Manila' \
      'session.save_path=/workspace/storage/sessions' \
      'display_errors=Off' \
      'log_errors=On' \
      'error_log=/proc/self/fd/2' \
      'upload_max_filesize=20M' \
      'post_max_size=24M' \
      'memory_limit=256M' \
      > /usr/local/etc/php/conf.d/montero-demo.ini

WORKDIR /workspace/public
