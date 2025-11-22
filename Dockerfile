FROM php:8.2-apache

# Встановлюємо утиліти та PHP-розширення
RUN apt-get update && apt-get install -y git unzip curl libzip-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && a2enmod rewrite

# Встановлюємо Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo "file_uploads=On\nupload_max_filesize=10M\npost_max_size=10M" > /usr/local/etc/php/conf.d/uploads.ini


# Копіюємо код
WORKDIR /var/www/html
COPY . .

# Встановлюємо PHP-залежності
RUN composer install --no-dev --optimize-autoloader

# Змінюємо порт Apache на 8000 для Koyeb
RUN sed -i 's/80/8000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
EXPOSE 8000

CMD ["apache2-foreground"]
