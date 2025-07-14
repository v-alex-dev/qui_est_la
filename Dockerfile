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
  nodejs \
  npm \
  sqlite3 \
  libsqlite3-dev \
  && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy existing application directory contents with proper ownership
COPY --chown=www-data:www-data . /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
RUN npm install && npm run build && rm -rf node_modules

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html/storage \
  && chmod -R 755 /var/www/html/bootstrap/cache \
  && chmod -R 755 /var/www/html/database \
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
