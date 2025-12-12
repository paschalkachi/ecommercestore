FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies (vendor)
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Fix Apache DocumentRoot to public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -i "s|/var/www/html|/var/www/html/public|g" /etc/apache2/sites-available/000-default.conf
RUN sed -i "s|/var/www/html|/var/www/html/public|g" /etc/apache2/apache2.conf

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# DO NOT run artisan cache commands here
# They need .env (which Render provides only at runtime)

EXPOSE 80

CMD ["apache2-foreground"]
