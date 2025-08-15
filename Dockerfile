# Dockerfile

# Start from the official PHP 8.2 FPM image.
# FPM (FastCGI Process Manager) is an alternative PHP FastCGI implementation with some additional features useful for sites of any size.
FROM php:8.2-fpm

# Set the working directory for all subsequent instructions.
WORKDIR /var/www/html

# Install essential system dependencies.
# - git, curl, zip, unzip: Common tools for package management.
# - lib...-dev: Libraries required for compiling PHP extensions.
# - libicu-dev: Required for the 'intl' PHP extension.
# - libzip-dev: Required for the 'zip' PHP extension.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip

# Install the PHP extensions required by Laravel and Filament.
# - pdo_mysql: For database connectivity.
# - intl: For internationalization features used by Filament.
# - zip: For handling zip archives, required by some Filament dependencies (e.g., for exports).
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Install Composer, the PHP dependency manager.
# We copy it from the official Composer image to ensure we get a stable version.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application source code into the container.
COPY . /var/www/html

# Change the ownership of all application files to the 'www-data' user.
# This prevents permission issues when the application tries to write to logs or cache.
COPY --chown=www-data:www-data . /var/www/html

# Switch to a non-root user for enhanced security.
# Running the application as 'www-data' is a standard security practice.
USER www-data

# Expose port 9000 to allow other containers (like Nginx) to connect to PHP-FPM.
EXPOSE 9000

# The main command to run when the container starts.
# This starts the PHP-FPM server.
CMD ["php-fpm"]