FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev curl

# Install PHP extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Enable Apache rewrite
RUN a2enmod rewrite

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Install Node.js dependencies and build frontend
RUN npm install
RUN npm run build

# Fix Apache DocumentRoot to public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -i "s|/var/www/html|/var/www/html/public|g" /etc/apache2/sites-available/000-default.conf
RUN sed -i "s|/var/www/html|/var/www/html/public|g" /etc/apache2/apache2.conf

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache public

# Make entrypoint script executable
RUN chmod +x /var/www/html/docker-entrypoint.sh

# Expose port
EXPOSE 80

# Use entrypoint
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
