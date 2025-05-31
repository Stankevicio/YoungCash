# Usar uma imagem oficial do PHP com Apache
FROM php:8.1-apache

# Instalar dependências do sistema necessárias para as extensões PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    zlib1g-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias
RUN docker-php-ext-install -j$(nproc) pdo pdo_pgsql sockets gd mbstring zip opcache && \
    a2enmod rewrite

RUN docker-php-ext-install mysqli pdo_mysql && docker-php-ext-enable mysqli pdo_mysql

# Copiar os arquivos do projeto para o diretório padrão do Apache
COPY . /var/www/html/