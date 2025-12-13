FROM php:8.3-cli-alpine

# Устанавливаем системные зависимости
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    oniguruma-dev \
    nodejs \
    npm \
    mysql-client

# Устанавливаем PHP расширения
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        bcmath \
        zip \
        gd \
        xml

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаем рабочую директорию
WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем PHP зависимости
RUN composer install --no-interaction --no-progress --optimize-autoloader

# Устанавливаем Node.js зависимости
RUN npm install

# Собираем фронтенд
RUN npm run build

# Устанавливаем права
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000
