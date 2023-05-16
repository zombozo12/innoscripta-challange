FROM php:8.0-fpm

ENV USER=service
ENV UID=1000
ENV PWD=/var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    nginx \
    postgresql \
    wget \
    lsb-release \
    gnupg \
    libpq-dev

RUN curl -fsSL https://deb.nodesource.com/setup_14.x | bash -

RUN apt-get update && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer --version

# Configure nginx
COPY docker/config/nginx.conf /etc/nginx/nginx.conf

# Copy php.ini config
COPY docker/config/www.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Copy uploads.ini config
COPY docker/config/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Use local configuration
COPY docker/config/supervisor.conf.tpl /etc/supervisor/conf.d/supervisor.conf.tpl

# Copy scripts
COPY docker/config/init.sh /usr/local/bin/init.sh

## Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $UID -d /home/$USER $USER
RUN mkdir -p /home/$USER/.composer && \
    chown -R $USER:$USER /home/$USER

# Set working directory
WORKDIR $PWD

# Copy app files
COPY . .

RUN npm install -g npm@latest

RUN composer install --no-dev

RUN cp .env.example .env

RUN php artisan key:generate

# Chown app files
# RUN chown -v -R $USER:www-data .
RUN chown -R www-data:www-data .

RUN npm install cross-env --unsafe-perm=true --allow-root

RUN npm install --unsafe-perm=true --allow-root

RUN npm audit fix --force; exit 0

RUN npm run dev

RUN chown -R www-data:www-data .

# Expose the port nginx is reachable on
EXPOSE 80 9001

# Run supervisor
ENTRYPOINT ["/bin/sh", "/usr/local/bin/init.sh"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:80/fpm-ping
