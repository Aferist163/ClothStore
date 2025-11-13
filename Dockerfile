# 1. Використовуємо ОФІЦІЙНИЙ образ PHP 8.2 з сервером Apache
FROM php:8.2-apache

# 2. Копіюємо весь наш код (index.php, api/...) у робочу папку Apache
COPY . /var/www/html/

# 3. (Опціонально, але гарна практика) Вмикаємо mod_rewrite для майбутнього
RUN a2enmod rewrite