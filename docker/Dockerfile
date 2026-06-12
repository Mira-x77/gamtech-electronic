FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libssl-dev \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions required by WordPress / WooCommerce
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo \
        pdo_mysql \
        zip \
        intl \
        xml \
        mbstring \
        curl \
        opcache \
        exif \
        bcmath

# Enable Apache mod_rewrite (needed for WordPress pretty permalinks)
RUN a2enmod rewrite

# Set recommended PHP settings for WordPress
RUN { \
    echo 'upload_max_filesize = 64M'; \
    echo 'post_max_size = 64M'; \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 120'; \
    echo 'max_input_vars = 3000'; \
} > /usr/local/etc/php/conf.d/wordpress.ini

# Allow .htaccess overrides in Apache
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
