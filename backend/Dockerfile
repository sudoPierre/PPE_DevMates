# Image de base PHP 8.2 avec Apache intégré
FROM php:8.2-apache

# Installation des dépendances système et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd \
    && rm -rf /var/lib/apt/lists/*

# Activation des modules Apache requis + suppression de l'avertissement ServerName
RUN a2enmod rewrite headers \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copie de la configuration du VirtualHost personnalisé
COPY apache/devmates.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Copie des sources PHP dans le conteneur
COPY src/ /var/www/html/

# Application des permissions correctes pour Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
