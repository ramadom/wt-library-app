#!/bin/bash


echo "Installing PHP dependencies..."

apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && apt-get install -y \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Install Composer if not present
if ! command -v composer &> /dev/null
then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "Insall dependencies..."
composer install --no-interaction

# Run Doctrine database creation and migration commands
echo "Creating the database..."
php bin/console doctrine:database:create --no-interaction

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Finally, run the Symfony built-in server
echo "Starting the Symfony development server..."
php -S 0.0.0.0:7789 -t public

