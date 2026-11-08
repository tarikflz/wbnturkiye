# Yerel test imajı: PHP 8.3 + Apache (.htaccess kuralları çalışır, php -S gibi taklit gerekmez)
FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers expires \
    && sed -ri 's#/var/www/html#/var/www/public_html#g' /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/public_html>\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-available/wbn.conf \
    && a2enconf wbn \
    && printf 'upload_max_filesize=10M\npost_max_size=12M\n' > /usr/local/etc/php/conf.d/wbn.ini

WORKDIR /var/www
