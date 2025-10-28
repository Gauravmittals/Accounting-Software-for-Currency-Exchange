# PHP + Apache (Laravel-friendly)
FROM php:8.2-apache

# System libs + MongoDB PHP extension
RUN apt-get update && apt-get install -y \
  git unzip libssl-dev pkg-config \
  && pecl install mongodb \
  && docker-php-ext-enable mongodb \
  && a2enmod rewrite \
  && rm -rf /var/lib/apt/lists/*

# Serve /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
  && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html
COPY . .

# Install Composer inside image
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP deps for prod (no dev)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

EXPOSE 80
CMD ["apache2-foreground"]
