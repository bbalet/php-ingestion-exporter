FROM php:7.2-cli

# It's a quite old version of Debian and the pakages have moved to archive...
RUN echo "deb http://archive.debian.org/debian stretch main" > /etc/apt/sources.list

# Install prerequisites of composer
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip \
    git

# Install PHP extensions required by the project
RUN docker-php-ext-install zip pdo pdo_mysql

# Install latest version of composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html/
COPY . .
# Install the project dependancies and ingest the example files
RUN composer install

# Serve the status page with PHP built-in web server 
EXPOSE 8080
CMD php -S 0.0.0.0:8080 .htrouter.php
