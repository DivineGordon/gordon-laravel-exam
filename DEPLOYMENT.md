# Deployment Guide

## Overview

This guide covers deploying the Page Customizer application to production environments. The application consists of a Laravel backend API and a Vue.js frontend that can be deployed separately or together.

## Prerequisites

### Server Requirements

- **PHP 8.2+** with required extensions:
  - BCMath PHP Extension
  - Ctype PHP Extension
  - cURL PHP Extension
  - DOM PHP Extension
  - Fileinfo PHP Extension
  - JSON PHP Extension
  - Mbstring PHP Extension
  - OpenSSL PHP Extension
  - PCRE PHP Extension
  - PDO PHP Extension
  - Tokenizer PHP Extension
  - XML PHP Extension
- **Node.js 20.19.0+** and npm
- **Web Server** (Nginx/Apache)
- **Database** (PostgreSQL/MySQL recommended for production)
- **SSL Certificate** (Let's Encrypt recommended)

### Production Database

For production, migrate from SQLite to a robust database:

#### PostgreSQL (Recommended)
```bash
# Install PostgreSQL
sudo apt update
sudo apt install postgresql postgresql-contrib

# Create database and user
sudo -u postgres psql
CREATE DATABASE page_customizer;
CREATE USER page_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE page_customizer TO page_user;
\q
```

#### MySQL Alternative
```bash
# Install MySQL
sudo apt update
sudo apt install mysql-server

# Create database and user
sudo mysql
CREATE DATABASE page_customizer;
CREATE USER 'page_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON page_customizer.* TO 'page_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Deployment Options

### Option 1: Traditional Server Deployment

#### 1. Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install nginx php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd nodejs npm git unzip

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 2. Application Deployment

```bash
# Clone repository
cd /var/www
sudo git clone <repository-url> page-customizer
sudo chown -R www-data:www-data page-customizer
cd page-customizer

# Install dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm install
sudo -u www-data npm run build

# Frontend build
cd frontend/page-customizer-frontend
sudo -u www-data npm install
sudo -u www-data npm run build
cd ../..
```

#### 3. Environment Configuration

```bash
# Copy environment file
sudo -u www-data cp .env.example .env

# Generate application key
sudo -u www-data php artisan key:generate

# Configure database
sudo -u www-data nano .env
```

Production `.env` configuration:
```env
APP_NAME="Page Customizer"
APP_ENV=production
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=page_customizer
DB_USERNAME=page_user
DB_PASSWORD=secure_password

# File storage
FILESYSTEM_DISK=public

# Cache and sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### 4. Database Setup

```bash
# Run migrations
sudo -u www-data php artisan migrate --force

# Seed themes
sudo -u www-data php artisan db:seed --class=PageThemeSeeder

# Create storage link
sudo -u www-data php artisan storage:link
```

#### 5. Nginx Configuration

Create `/etc/nginx/sites-available/page-customizer`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/page-customizer/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Handle Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Handle API routes
    location /api {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Handle public pages
    location /pages {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Serve static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security headers
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/page-customizer /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### 6. SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Test auto-renewal
sudo certbot renew --dry-run
```

### Option 2: Docker Deployment

#### 1. Create Dockerfile

```dockerfile
# Backend Dockerfile
FROM php:8.2-fpm

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
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
```

#### 2. Docker Compose Configuration

```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: page-customizer-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - page-customizer

  nginx:
    image: nginx:alpine
    container_name: page-customizer-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx/conf.d/:/etc/nginx/conf.d/
    depends_on:
      - app
    networks:
      - page-customizer

  db:
    image: postgres:15
    container_name: page-customizer-db
    restart: unless-stopped
    environment:
      POSTGRES_DB: page_customizer
      POSTGRES_USER: page_user
      POSTGRES_PASSWORD: secure_password
    volumes:
      - postgres_data:/var/lib/postgresql/data
    networks:
      - page-customizer

  redis:
    image: redis:7-alpine
    container_name: page-customizer-redis
    restart: unless-stopped
    networks:
      - page-customizer

volumes:
  postgres_data:

networks:
  page-customizer:
    driver: bridge
```

#### 3. Deploy with Docker

```bash
# Build and start containers
docker-compose up -d --build

# Run migrations
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --class=PageThemeSeeder
docker-compose exec app php artisan storage:link
```

### Option 3: Cloud Platform Deployment

#### Heroku Deployment

1. **Create Heroku App**
```bash
heroku create your-app-name
heroku addons:create heroku-postgresql:hobby-dev
heroku addons:create heroku-redis:hobby-dev
```

2. **Configure Environment**
```bash
heroku config:set APP_KEY=$(php artisan key:generate --show)
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
```

3. **Deploy**
```bash
git push heroku main
heroku run php artisan migrate --force
heroku run php artisan db:seed --class=PageThemeSeeder
```

#### DigitalOcean App Platform

1. Create `app.yaml`:
```yaml
name: page-customizer
services:
- name: web
  source_dir: /
  github:
    repo: your-username/page-customizer
    branch: main
  run_command: php artisan serve --host=0.0.0.0 --port=8080
  environment_slug: php
  instance_count: 1
  instance_size_slug: basic-xxs
  envs:
  - key: APP_ENV
    value: production
  - key: APP_DEBUG
    value: false
databases:
- name: page-customizer-db
  engine: PG
  version: "13"
```

## Performance Optimization

### 1. Enable Caching

```bash
# Install Redis
sudo apt install redis-server

# Configure Laravel caching
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### 2. Database Optimization

```sql
-- Add indexes for better performance
CREATE INDEX idx_page_analytics_date ON page_analytics(visited_at);
CREATE INDEX idx_page_analytics_page_date ON page_analytics(client_page_id, visited_at);
```

### 3. File Storage Optimization

```bash
# Use cloud storage for production
composer require league/flysystem-aws-s3-v3

# Configure S3 in .env
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
```

### 4. Frontend Optimization

```bash
# Build optimized frontend
cd frontend/page-customizer-frontend
npm run build

# Serve static files from CDN
# Configure Vite for CDN assets
```

## Monitoring and Logging

### 1. Application Monitoring

```bash
# Install monitoring tools
composer require spatie/laravel-health

# Configure health checks
php artisan health:install
```

### 2. Log Management

```bash
# Configure log rotation
sudo nano /etc/logrotate.d/page-customizer

# Add configuration:
/var/www/page-customizer/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
```

### 3. Error Tracking

Consider integrating error tracking services:
- Sentry
- Bugsnag
- Rollbar

## Security Hardening

### 1. Server Security

```bash
# Configure firewall
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'

# Disable root login
sudo nano /etc/ssh/sshd_config
# Set PermitRootLogin no
sudo systemctl restart ssh
```

### 2. Application Security

```bash
# Set secure file permissions
sudo chmod -R 755 /var/www/page-customizer
sudo chmod -R 775 /var/www/page-customizer/storage
sudo chmod -R 775 /var/www/page-customizer/bootstrap/cache

# Configure PHP security
sudo nano /etc/php/8.2/fpm/php.ini
# Set:
# expose_php = Off
# allow_url_fopen = Off
# allow_url_include = Off
```

### 3. Database Security

```sql
-- Create read-only user for analytics
CREATE USER analytics_readonly WITH PASSWORD 'secure_password';
GRANT SELECT ON page_analytics TO analytics_readonly;
GRANT SELECT ON client_pages TO analytics_readonly;
```

## Backup Strategy

### 1. Database Backup

```bash
# Create backup script
sudo nano /usr/local/bin/backup-page-customizer.sh

#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/page-customizer"
mkdir -p $BACKUP_DIR

# Database backup
pg_dump page_customizer > $BACKUP_DIR/db_backup_$DATE.sql

# File backup
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/page-customizer/storage/app/public

# Cleanup old backups (keep 30 days)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

### 2. Automated Backups

```bash
# Add to crontab
sudo crontab -e

# Add line:
0 2 * * * /usr/local/bin/backup-page-customizer.sh
```

## Maintenance Tasks

### 1. Regular Updates

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Update Composer dependencies
sudo -u www-data composer update

# Update npm dependencies
sudo -u www-data npm update
```

### 2. Performance Monitoring

```bash
# Monitor server resources
htop
iotop
df -h

# Monitor application logs
tail -f /var/www/page-customizer/storage/logs/laravel.log
```

### 3. Database Maintenance

```sql
-- Analyze table statistics
ANALYZE page_analytics;
ANALYZE client_pages;

-- Clean up old analytics data (optional)
DELETE FROM page_analytics WHERE visited_at < NOW() - INTERVAL '1 year';
```

## Troubleshooting

### Common Issues

1. **Permission Errors**
```bash
sudo chown -R www-data:www-data /var/www/page-customizer
sudo chmod -R 755 /var/www/page-customizer
```

2. **Database Connection Issues**
```bash
# Check database status
sudo systemctl status postgresql
# Check connection
php artisan tinker
>>> DB::connection()->getPdo();
```

3. **File Upload Issues**
```bash
# Check storage permissions
sudo chmod -R 775 /var/www/page-customizer/storage
# Check PHP upload limits
php -i | grep upload_max_filesize
```

4. **Frontend Build Issues**
```bash
# Clear npm cache
npm cache clean --force
# Reinstall dependencies
rm -rf node_modules package-lock.json
npm install
```

### Log Analysis

```bash
# Check Nginx logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Check Laravel logs
tail -f storage/logs/laravel.log
```

## Scaling Considerations

### Horizontal Scaling

1. **Load Balancer Configuration**
2. **Database Replication**
3. **Session Storage** (Redis)
4. **File Storage** (S3/CDN)

### Vertical Scaling

1. **Server Resources** (CPU/RAM)
2. **Database Optimization**
3. **Caching Strategy**
4. **CDN Integration**

This deployment guide provides comprehensive instructions for deploying the Page Customizer application in various environments. Choose the deployment method that best fits your infrastructure and requirements.
