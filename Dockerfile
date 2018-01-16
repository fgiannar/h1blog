FROM php:7
RUN apt-get update -y && apt-get install -y openssl zip unzip git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql mbstring
WORKDIR /var/www/html
COPY . /var/www/html
CMD bash -c "composer install && chmod -R 777 storage && php -S 0.0.0.0:8181 -t ./public"
EXPOSE 8181