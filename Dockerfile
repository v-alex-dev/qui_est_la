# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip \
  sqlite3 \
  libsqlite3-dev \
  && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy existing application directory contents with proper ownership
COPY --chown=www-data:www-data . /var/www/html

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/storage/logs \
  && mkdir -p /var/www/html/storage/framework/cache \
  && mkdir -p /var/www/html/storage/framework/sessions \
  && mkdir -p /var/www/html/storage/framework/views \
  && mkdir -p /var/www/html/bootstrap/cache \
  && chown -R www-data:www-data /var/www/html/storage \
  && chown -R www-data:www-data /var/www/html/bootstrap/cache \
  && chmod -R 775 /var/www/html/storage \
  && chmod -R 775 /var/www/html/bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set final permissions
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html/public \
  && touch /var/www/html/database/database.sqlite \
  && chown www-data:www-data /var/www/html/database/database.sqlite \
  && chmod 664 /var/www/html/database/database.sqlite

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy the startup script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Create Apache virtual host configuration
RUN echo '<VirtualHost *:80>\n\
  DocumentRoot /var/www/html/public\n\
  <Directory /var/www/html/public>\n\
  AllowOverride All\n\
  Require all granted\n\
  </Directory>\n\
  ErrorLog ${APACHE_LOG_DIR}/error.log\n\
  CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
  </VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start with our custom script
CMD ["/usr/local/bin/start.sh"]
