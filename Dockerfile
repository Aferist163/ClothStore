# 1. PHP 8.2 + Apache
FROM php:8.2-apache

# 2. Копіюємо код
COPY . /var/www/html/

# 3. Встановлюємо розширення MySQLi
RUN docker-php-ext-install mysqli

# 4. Вмикаємо rewrite
RUN a2enmod rewrite

# 5. Встановлюємо Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 6. Встановлюємо залежності через Composer
RUN composer install --working-dir=/var/www/html