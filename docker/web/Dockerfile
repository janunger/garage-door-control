FROM php:7.0-apache-jessie

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY php.ini /usr/local/etc/php/conf.d/zz_custom.ini

RUN apt-get update
RUN apt-get install -y unzip

RUN docker-php-ext-install -j$(nproc) pdo_mysql bcmath

RUN { \
    cd /root; \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    php composer-setup.php; \
    php -r "unlink('composer-setup.php');"; \
    chmod +x composer.phar; \
    mv composer.phar /usr/local/bin/composer; \
}
