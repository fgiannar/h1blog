FROM php:7

# Install dependencies
RUN apt-get update -y && apt-get install -y openssl zip unzip git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql mbstring

# Set workdir
WORKDIR /var/www/html

# Copy files to workdir
COPY . /var/www/html

# Change storage permissions
CMD chmod -R 777 storage

# Start php dev server
CMD php -S 0.0.0.0:8181 -t ./public

# Expose port 8181
EXPOSE 8181