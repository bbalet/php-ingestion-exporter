FROM php:7.2-cli
# Install Zip (it is a prerequisite of composer)
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip
# Install latest version of composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer self-update
WORKDIR /var/www/html/
COPY . .
# Install the project dependancies and ingest the example files
RUN composer install
RUN php ingest-files.php

# Serve the status page with PHP built-in web server 
CMD php -S localhost:8100
