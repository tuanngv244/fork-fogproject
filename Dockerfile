FROM php:7.4-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    mariadb-client \
    curl \
    wget \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    rsync \
    lftp \
    vsftpd \
    xinetd \
    tftpd-hpa \
    apache2-utils \
    bc \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    mbstring \
    xml \
    curl \
    json \
    bcmath \
    gettext \
    ftp \
    posix \
    sockets

# Enable Apache modules
RUN a2enmod rewrite ssl headers

# Create FOG directories
RUN mkdir -p /var/www/html/fog \
    && mkdir -p /images \
    && mkdir -p /images/dev \
    && chmod -R 777 /images

# Copy FOG source
COPY packages/web/ /var/www/html/fog/

# Set permissions
RUN chown -R www-data:www-data /var/www/html/fog \
    && chmod -R 755 /var/www/html/fog

# Apache configuration
RUN echo '<VirtualHost *:80>\n\
    ServerName localhost\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    <Directory /var/www/html/fog>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    Alias /fog /var/www/html/fog\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/fog.conf

RUN a2dissite 000-default.conf \
    && a2ensite fog.conf

# PHP configuration
RUN echo 'max_execution_time = 300\n\
max_input_time = 600\n\
memory_limit = 256M\n\
post_max_size = 100M\n\
upload_max_filesize = 100M' > /usr/local/etc/php/conf.d/fog.ini

# Expose ports
EXPOSE 80 21 69 9000-9010

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s \
    CMD curl -f http://localhost/fog/ || exit 1

CMD ["apache2-foreground"]
