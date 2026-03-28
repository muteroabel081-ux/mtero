FROM php:8.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

COPY docker/entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist

COPY . .
RUN composer dump-autoload --optimize

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
