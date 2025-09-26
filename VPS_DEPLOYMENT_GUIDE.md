# 🚀 GoRide VPS Deployment Guide

## 📋 Требования к серверу

### Минимальные требования:
- **RAM**: 2GB (рекомендуется 4GB+)
- **CPU**: 2 cores (рекомендуется 4+ cores)
- **Storage**: 20GB SSD (рекомендуется 50GB+)
- **OS**: Ubuntu 20.04/22.04 LTS или CentOS 8+

### Рекомендуемые VPS провайдеры:
- DigitalOcean ($10-20/месяц)
- Linode ($10-20/месяц)
- Vultr ($10-20/месяц)
- AWS EC2 (t3.small - $15-25/месяц)
- Hetzner ($5-15/месяц)

## 🛠️ Установка на Ubuntu 22.04

### 1. Подключение к серверу
```bash
ssh root@your-server-ip
```

### 2. Обновление системы
```bash
apt update && apt upgrade -y
apt install -y curl wget git unzip software-properties-common
```

### 3. Установка PHP 8.1+
```bash
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
    php8.2-curl php8.2-zip php8.2-gd php8.2-json php8.2-bcmath \
    php8.2-tokenizer php8.2-fileinfo php8.2-intl
```

### 4. Установка Composer
```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

### 5. Установка MySQL 8.0
```bash
apt install -y mysql-server
mysql_secure_installation
```

Создание базы данных:
```bash
mysql -u root -p
```
```sql
CREATE DATABASE goride_production;
CREATE USER 'goride'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON goride_production.* TO 'goride'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 6. Установка Nginx
```bash
apt install -y nginx
systemctl start nginx
systemctl enable nginx
```

### 7. Установка Node.js (для real-time features)
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs
```

### 8. Установка Redis (для кэширование и сессии)
```bash
apt install -y redis-server
systemctl start redis-server
systemctl enable redis-server
```

## 📁 Деплой Laravel приложения

### 1. Клонирование проекта
```bash
cd /var/www
git clone your-repo-url goride
cd goride/laravel-backend
```

### 2. Установка зависимостей
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Настройка окружения
```bash
cp .env.example .env
nano .env
```

Конфигурация `.env` для продакшена:
```env
APP_NAME="GoRide API"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://api.yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=goride_production
DB_USERNAME=goride
DB_PASSWORD=your_strong_password

BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Pusher для real-time
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# Payment Gateways
STRIPE_KEY=pk_live_your_stripe_key
STRIPE_SECRET=sk_live_your_stripe_secret

RAZORPAY_KEY=rzp_live_your_key
RAZORPAY_SECRET=your_razorpay_secret

# Google Maps
GOOGLE_MAPS_API_KEY=your_google_maps_key

# Firebase (только для push notifications)
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYOUR_KEY\n-----END PRIVATE KEY-----\n"
FIREBASE_CLIENT_EMAIL=your_service_account_email
```

### 4. Генерация ключа и настройка
```bash
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Миграция базы данных
```bash
php artisan migrate --force
```

### 6. Настройка прав доступа
```bash
chown -R www-data:www-data /var/www/goride
chmod -R 755 /var/www/goride
chmod -R 775 /var/www/goride/laravel-backend/storage
chmod -R 775 /var/www/goride/laravel-backend/bootstrap/cache
```

## ⚙️ Конфигурация Nginx

### 1. Создание конфигурации сайта
```bash
nano /etc/nginx/sites-available/goride-api
```

```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    root /var/www/goride/laravel-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Security headers
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    
    # CORS headers for API
    add_header Access-Control-Allow-Origin "*";
    add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS";
    add_header Access-Control-Allow-Headers "Origin, Content-Type, Accept, Authorization, X-Request-With";
    
    if ($request_method = 'OPTIONS') {
        add_header Access-Control-Allow-Origin "*";
        add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS";
        add_header Access-Control-Allow-Headers "Origin, Content-Type, Accept, Authorization, X-Request-With";
        add_header Content-Length 0;
        add_header Content-Type text/plain;
        return 200;
    }
}
```

### 2. Активация сайта
```bash
ln -s /etc/nginx/sites-available/goride-api /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

## 🔒 SSL Certificate (Let's Encrypt)

### 1. Установка Certbot
```bash
apt install -y certbot python3-certbot-nginx
```

### 2. Получение SSL сертификата
```bash
certbot --nginx -d api.yourdomain.com
```

### 3. Автообновление сертификата
```bash
crontab -e
```
Добавить:
```
0 12 * * * /usr/bin/certbot renew --quiet
```

## 🔄 Queue Worker (для фоновых задач)

### 1. Создание systemd сервиса
```bash
nano /etc/systemd/system/goride-worker.service
```

```ini
[Unit]
Description=GoRide Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/goride/laravel-backend
ExecStart=/usr/bin/php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

### 2. Запуск сервиса
```bash
systemctl daemon-reload
systemctl enable goride-worker
systemctl start goride-worker
```

## 📊 Мониторинг и логи

### 1. Настройка логирования
```bash
mkdir -p /var/log/goride
chown www-data:www-data /var/log/goride
```

### 2. Мониторинг логов
```bash
# Laravel логи
tail -f /var/www/goride/laravel-backend/storage/logs/laravel.log

# Nginx логи
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# Queue worker логи
journalctl -u goride-worker -f
```

## 🚀 Оптимизация производительности

### 1. PHP-FPM оптимизация
```bash
nano /etc/php/8.2/fpm/pool.d/www.conf
```

Изменить:
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

### 2. MySQL оптимизация
```bash
nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Добавить:
```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 200
query_cache_size = 128M
```

### 3. Redis оптимизация
```bash
nano /etc/redis/redis.conf
```

Изменить:
```
maxmemory 512mb
maxmemory-policy allkeys-lru
```

## 📱 Обновление Flutter приложений

### 1. Изменить API URL в Flutter
В файле `lib/services/api_service.dart`:
```dart
static const String baseUrl = 'https://api.yourdomain.com/api/v1';
```

### 2. Пересборка приложений
```bash
cd Applications/GoRide-5.2/customer
flutter build apk --release
flutter build appbundle --release

# Для iOS
flutter build ios --release
```

## 🔧 Автоматизация деплоя

### 1. Создание скрипта деплоя
```bash
nano /var/www/goride/deploy.sh
```

```bash
#!/bin/bash
cd /var/www/goride/laravel-backend

# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Restart services
systemctl restart php8.2-fpm
systemctl restart goride-worker
systemctl reload nginx

echo "Deployment completed successfully!"
```

```bash
chmod +x /var/www/goride/deploy.sh
```

## 🛡️ Безопасность

### 1. Firewall настройка
```bash
ufw enable
ufw allow ssh
ufw allow 'Nginx Full'
ufw allow 3306  # MySQL (только для локальных подключений)
```

### 2. Fail2Ban для защиты от брутфорса
```bash
apt install -y fail2ban
systemctl enable fail2ban
systemctl start fail2ban
```

### 3. Регулярные бэкапы
```bash
nano /root/backup.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"

# Database backup
mysqldump -u goride -p'your_password' goride_production > $BACKUP_DIR/db_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/goride

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

```bash
chmod +x /root/backup.sh
crontab -e
```
Добавить:
```
0 2 * * * /root/backup.sh
```

## 🧪 Проверка работоспособности

### 1. Тест API endpoints
```bash
curl -X GET https://api.yourdomain.com/api/v1/services
curl -X POST https://api.yourdomain.com/api/v1/customer/login \
  -H "Content-Type: application/json" \
  -d '{"firebase_uid":"test","email":"test@test.com","full_name":"Test User","login_type":"email"}'
```

### 2. Проверка сервисов
```bash
systemctl status nginx
systemctl status php8.2-fpm
systemctl status mysql
systemctl status redis-server
systemctl status goride-worker
```

## 📈 Масштабирование

### Для высоких нагрузок:
1. **Load Balancer**: Nginx + несколько серверов приложений
2. **Database**: MySQL Master-Slave репликация
3. **Cache**: Redis Cluster
4. **CDN**: CloudFlare для статических файлов
5. **Monitoring**: Grafana + Prometheus

## 💰 Примерная стоимость VPS

| Конфигурация | Провайдер | Цена/месяц | Подходит для |
|-------------|-----------|------------|--------------|
| 2GB RAM, 2 CPU | DigitalOcean | $12 | Тестирование |
| 4GB RAM, 2 CPU | DigitalOcean | $24 | Небольшая нагрузка |
| 8GB RAM, 4 CPU | DigitalOcean | $48 | Средняя нагрузка |
| 16GB RAM, 8 CPU | DigitalOcean | $96 | Высокая нагрузка |

---

## ✅ Готово к деплою!

Проект полностью готов для установки на VPS. Следуйте этой инструкции пошагово, и у вас будет работающий продакшен сервер с GoRide API! 🚀

