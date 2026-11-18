FROM php:8.3-apache

# Ekstensi yang dibutuhkan CodeIgniter 4: intl (wajib), mysqli (database), gd (image)
RUN apt-get update \
 && apt-get install -y --no-install-recommends libicu-dev libpng-dev libjpeg-dev libwebp-dev libzip-dev unzip \
 && docker-php-ext-configure gd --with-jpeg --with-webp \
 && docker-php-ext-install intl mysqli gd zip \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

# Document root CI4 ada di folder public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
 && printf 'upload_max_filesize=4M\npost_max_size=8M\n' > /usr/local/etc/php/conf.d/upload.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
