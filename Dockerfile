FROM php:8.3-apache

# Laravelでよく使うPHP拡張
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Composerをコピー
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apacheの公開ディレクトリを Laravel の public に変更
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# アプリをコピー
COPY . /var/www/html

# 依存関係インストール
RUN composer install --no-dev --optimize-autoloader --no-interaction

# storage と bootstrap/cache の権限
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Render の Web Service は 10000 番ポートを期待
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
EXPOSE 10000

CMD ["apache2-foreground"]