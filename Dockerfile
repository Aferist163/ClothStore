FROM php:8.2-apache

# Копіюємо код без vendor
COPY . /var/www/html/

# Встановлюємо mysqli та rewrite
RUN docker-php-ext-install mysqli
RUN a2enmod rewrite

# Встановлюємо Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Встановлюємо залежності
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Права
RUN chown -R www-data:www-data /var/www/html
