FROM php:8.2-cli

# Cài đặt các thư viện hệ thống và driver PostgreSQL (pdo_pgsql) cho Neon
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Đặt thư mục làm việc chuẩn xác
WORKDIR /var/www/html

# Copy toàn bộ source code vào trong container
COPY . .

# Cài đặt thư viện PHP (bỏ qua dev dependencies để nhẹ)
RUN composer install --no-dev --optimize-autoloader

# Phân quyền cho thư mục storage và cache của Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Mở cổng 10000 (cổng mặc định của Render)
EXPOSE 10000

# Chạy migrate database và khởi động server Laravel
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
