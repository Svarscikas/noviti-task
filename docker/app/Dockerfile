FROM php:8.1-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl gnupg unzip git \
          libfreetype6-dev \
          libjpeg62-turbo-dev \
          libxml2-dev \
          libpng-dev \
          netcat-traditional libxml2-dev libpq-dev libzip-dev wget wkhtmltopdf \
          nginx \
          supervisor

RUN pecl install apcu

RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN docker-php-ext-configure gd \
          --with-freetype \
          --with-jpeg && \
    docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install -j$(nproc) zip opcache intl pdo_pgsql pgsql soap pcntl gd && \
    docker-php-ext-enable apcu pdo_pgsql sodium gd

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/symfony

# Set timezone
ARG TIMEZONE="Europe/Vilnius"
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"

COPY docker/app/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/app/php-fpm.conf /usr/local/etc/php-fpm.d/www.custom.conf
COPY docker/app/nginx.conf /etc/nginx/nginx.conf
COPY docker/app/site.conf /etc/nginx/conf.d/site.conf
#COPY docker/app/supervisord.conf /etc/supervisor/conf.d/app.conf

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN usermod --uid=1000 www-data && \
    groupmod --gid=1000 www-data && \
    chown -R www-data:www-data /var/www/

#CMD ["supervisord", "-n"]