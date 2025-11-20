# 1. Використовуємо офіційний образ PHP 8.2 з Apache
FROM php:8.2-apache

# 2. Встановлюємо mysqli та включаємо rewrite
RUN docker-php-ext-install mysqli && a2enmod rewrite

# 3. Встановлюємо Composer (якщо знадобиться оновлення залежностей)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 4. Копіюємо весь проект у контейнер
COPY . /var/www/html/

# 5. Встановлюємо робочу директорію
WORKDIR /var/www/html

# 6. Якщо хочеш перевірити/оновити залежності
# RUN composer install --no-dev --optimize-autoloader

# 7. Відкриваємо порт 80
EXPOSE 80

# 8. Старт Apache
CMD ["apache2-foreground"]
