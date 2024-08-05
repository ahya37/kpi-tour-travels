# Use an official PHP image with Apache
FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    gnupg

# Install Node.js version 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Check Node.js and npm versions
RUN node -v
RUN npm -v

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Set Apache configurations
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set the working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Buka port 8000
EXPOSE 8082

# Command untuk menjalankan server
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8082"]
