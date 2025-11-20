FROM php:8.2-fpm

# Встановлюємо розширення
RUN docker-php-ext-install mysqli

# Встановлюємо nginx + supervisor
RUN apt-get update && apt-get install -y nginx supervisor && apt-get clean

# Копіюємо код
COPY . /var/www
WORKDIR /var/www

# Копіюємо конфіг nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Копіюємо конфіг supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Права
RUN chown -R www-data:www-data /var/www

# Стандартний порт Render
EXPOSE 10000

# Запускаємо supervisor (який запускає PHP-FPM + nginx)
CMD ["/usr/bin/supervisord"]
