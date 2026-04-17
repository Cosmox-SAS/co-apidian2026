# Guía de Despliegue en Producción - APIDIAN API

Guía completa para desplegar el proyecto APIDIAN en un servidor de producción usando Docker Compose.

---

## 📋 Requisitos Previos

### Hardware Mínimo Recomendado

| Componente | Especificación |
|------------|----------------|
| **CPU** | 2 vCPU (4 recomendados) |
| **RAM** | 4 GB (8 GB recomendados) |
| **Disco** | 50 GB SSD |
| **Red** | Conexión estable con IP pública estática |

### Software Requerido

- **Sistema Operativo**: Ubuntu 20.04/22.04 LTS, CentOS 8, o Debian 11
- **Docker**: 20.10+ 
- **Docker Compose**: 2.0+
- **Git**: Para clonar el repositorio
- **Certbot** (opcional): Para certificados SSL gratuitos

### Instalación de Docker y Docker Compose

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Docker
sudo apt install -y apt-transport-https ca-certificates curl gnupg lsb-release

curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.23.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

sudo chmod +x /usr/local/bin/docker-compose
sudo ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose

# Verificar instalación
docker --version
docker-compose --version

# Agregar usuario al grupo docker (opcional)
sudo usermod -aG docker $USER
# Cerrar sesión y volver a iniciar para aplicar cambios
```

---

## 🚀 Pasos de Despliegue

### 1. Preparar el Servidor

```bash
# Crear directorio de la aplicación
sudo mkdir -p /var/www/apidian
sudo chown $USER:$USER /var/www/apidian

# Clonar el repositorio
cd /var/www/apidian
git clone <URL_DEL_REPOSITORIO> .
```

### 2. Configurar Variables de Entorno

Copiar el archivo de ejemplo y configurar:

```bash
cp .env.example .env
nano .env  # o vim .env
```

#### Configuración Mínima Requerida:

```env
# ============================================
# CONFIGURACIÓN DE LA APLICACIÓN
# ============================================
APP_NAME="APIDIAN"
APP_ENV=production
APP_KEY=  # Se generará automáticamente
APP_DEBUG=false
APP_PORT=80
APP_URL=https://tudominio.com  # Cambiar a tu dominio
FORCE_HTTPS=true

# ============================================
# BASE DE DATOS (MariaDB en Docker)
# ============================================
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=db_api
DB_USERNAME=user_api
DB_PASSWORD=password_segura_fuerte_aqui  # ¡CAMBIAR!
DB_PORT_EXTERNAL=3307  # Puerto externo para evitar conflictos

# ============================================
# ALMACENAMIENTO S3 (Recomendado para producción)
# ============================================
FILESYSTEM_DRIVER=s3
AWS_ACCESS_KEY_ID=TU_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY=TU_SECRET_ACCESS_KEY
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=nombre-de-tu-bucket
AWS_ENDPOINT=
AWS_USE_PATH_STYLE_ENDPOINT=false

# ============================================
# CONFIGURACIÓN DE EMAIL (AWS SES recomendado)
# ============================================
MAIL_DRIVER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com  # Para AWS SES
MAIL_PORT=587
MAIL_USERNAME=TU_SES_USERNAME
MAIL_PASSWORD=TU_SES_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@tudominio.com
MAIL_FROM_NAME="${APP_NAME}"

# ============================================
# CACHE Y SESIONES (Redis recomendado para producción)
# ============================================
CACHE_DRIVER=file  # Cambiar a redis si está disponible
SESSION_DRIVER=file  # Cambiar a redis si está disponible
SESSION_LIFETIME=120
QUEUE_CONNECTION=database  # o redis

# ============================================
# CONFIGURACIÓN DIAN
# ============================================
ALLOW_PUBLIC_DOWNLOAD=true
APPLY_SENDER_CUSTOMER_CREDENTIALS=true
GRAPHIC_REPRESENTATION_TEMPLATE=2
ALLOW_PUBLIC_REGISTER=true
VALIDATE_BEFORE_SENDING=true
SAVE_RESPONSE_DIAN_TO_DB=false
ENABLE_API_REGISTER=true

# API de certificación de la DIAN
URL_API_CERT_MODERNIZER="http://62.146.176.127:8091"
PDFTOTEXT_PATH="/usr/bin/pdftotext"
```

### 3. Crear Red Docker

```bash
# Crear red externa para los contenedores
docker network create apinet
```

### 4. Configurar Docker Compose para Producción

Crear `docker-compose.yml`:

```yaml
version: '3.8'

services:
  # ==========================================
  # NGINX - Servidor Web
  # ==========================================
  nginx_api:
    image: nginx:alpine
    container_name: nginx_api
    restart: unless-stopped
    working_dir: /var/www/html
    ports:
      - "${APP_PORT}:80"
      - "443:443"  # Puerto SSL
    volumes:
      - ./:/var/www/html:ro
      - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf:ro
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
      - ./docker/ssl:/etc/nginx/ssl:ro  # Certificados SSL
      - nginx_logs:/var/log/nginx
    networks:
      - apinet
    depends_on:
      - php_api
    healthcheck:
      test: ["CMD", "wget", "--quiet", "--tries=1", "--spider", "http://localhost/health"]
      interval: 30s
      timeout: 10s
      retries: 3

  # ==========================================
  # PHP-FPM - Procesamiento PHP
  # ==========================================
  php_api:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: php_api
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
      - php_logs:/var/log/php
    networks:
      - apinet
    depends_on:
      - mariadb
      - redis  # Opcional
    environment:
      - APP_ENV=production
    healthcheck:
      test: ["CMD", "php", "-v"]
      interval: 30s
      timeout: 10s
      retries: 3

  # ==========================================
  # MariaDB - Base de Datos
  # ==========================================
  mariadb:
    image: mariadb:11.5.2
    container_name: mariadb_api
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    ports:
      - "${DB_PORT_EXTERNAL}:3306"
    volumes:
      - mariadb_data:/var/lib/mysql
      - ./docker/init.sql:/docker-entrypoint-initdb.d/init.sql:ro
      - ./backups:/backups  # Para backups
    networks:
      - apinet
    healthcheck:
      test: ["CMD", "healthcheck.sh", "--connect", "--innodb_initialized"]
      interval: 30s
      timeout: 10s
      retries: 3

  # ==========================================
  # Redis - Cache y Sesiones (Opcional pero recomendado)
  # ==========================================
  redis:
    image: redis:7-alpine
    container_name: redis_api
    restart: unless-stopped
    volumes:
      - redis_data:/data
    networks:
      - apinet
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 30s
      timeout: 10s
      retries: 3

  # ==========================================
  # Cron - Tareas Programadas de Laravel
  # ==========================================
  cron:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: cron_api
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
    networks:
      - apinet
    depends_on:
      - mariadb
    command: crontab /var/www/html/docker/cron/laravel-scheduler

  # ==========================================
  # Queue Worker - Procesamiento de Colas
  # ==========================================
  queue:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: queue_api
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
    networks:
      - apinet
    depends_on:
      - mariadb
      - redis
    command: php artisan queue:work --sleep=3 --tries=3 --timeout=90
    deploy:
      replicas: 2  # Escalar según carga

networks:
  apinet:
    external: true

volumes:
  mariadb_data:
    driver: local
  redis_data:
    driver: local
  nginx_logs:
    driver: local
  php_logs:
    driver: local
```

### 5. Crear Archivos de Configuración Docker

#### `docker/nginx/nginx.conf`:

```nginx
user nginx;
worker_processes auto;
error_log /var/log/nginx/error.log warn;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';

    access_log /var/log/nginx/access.log main;

    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;

    # Compresión
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/rss+xml application/atom+xml image/svg+xml;

    include /etc/nginx/conf.d/*.conf;
}
```

#### `docker/nginx/default.conf`:

```nginx
server {
    listen 80;
    server_name _;
    return 301 https://$host$request_uri;  # Redirigir HTTPS
}

server {
    listen 443 ssl http2;
    server_name tudominio.com www.tudominio.com;  # Cambiar

    root /var/www/html/public;
    index index.php index.html;

    # SSL
    ssl_certificate /etc/nginx/ssl/cert.pem;
    ssl_certificate_key /etc/nginx/ssl/key.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Seguridad
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Logs
    access_log /var/log/nginx/apidian_access.log;
    error_log /var/log/nginx/apidian_error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass php_api:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Optimizaciones
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_read_timeout 300;
    }

    # Health check
    location /health {
        access_log off;
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }

    # Bloquear archivos ocultos
    location ~ /\. {
        deny all;
    }

    # Caché para archivos estáticos
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg)$ {
        expires 1M;
        add_header Cache-Control "public, immutable";
    }
}
```

#### `docker/cron/laravel-scheduler`:

```cron
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔒 Configuración SSL/TLS

### Opción A: Certbot (Let's Encrypt - Gratuito)

```bash
# Instalar Certbot
sudo apt install -y certbot

# Generar certificados (modo standalone)
sudo certbot certonly --standalone -d tudominio.com -d www.tudominio.com

# Copiar certificados al directorio del proyecto
sudo cp /etc/letsencrypt/live/tudominio.com/fullchain.pem /var/www/apidian/docker/ssl/cert.pem
sudo cp /etc/letsencrypt/live/tudominio.com/privkey.pem /var/www/apidian/docker/ssl/key.pem
sudo chown $USER:$USER /var/www/apidian/docker/ssl/*.pem

# Configurar renovación automática
sudo crontab -e
# Agregar:
0 12 * * * /usr/bin/certbot renew --quiet && cp /etc/letsencrypt/live/tudominio.com/fullchain.pem /var/www/apidian/docker/ssl/cert.pem && cp /etc/letsencrypt/live/tudominio.com/privkey.pem /var/www/apidian/docker/ssl/key.pem && cd /var/www/apidian && docker-compose restart nginx_api
```

### Opción B: Certificado Comprado

1. Colocar los archivos en `docker/ssl/`:
   - `cert.pem` - Certificado + intermedios
   - `key.pem` - Llave privada

---

## 🚀 Despliegue Final

### 1. Iniciar Servicios

```bash
cd /var/www/apidian

# Construir imágenes
docker-compose build --no-cache

# Iniciar en segundo plano
docker-compose up -d

# Verificar estado
docker-compose ps
docker-compose logs -f
```

### 2. Instalar Dependencias y Configurar Laravel

```bash
# Instalar dependencias
docker exec php_api composer install --no-dev --optimize-autoloader --no-interaction

# Generar APP_KEY
docker exec php_api php artisan key:generate

# Ejecutar migraciones
docker exec php_api php artisan migrate --force

# Ejecutar seeders (solo primera vez)
docker exec php_api php artisan db:seed --force

# Optimizar para producción
docker exec php_api php artisan config:cache
docker exec php_api php artisan route:cache
docker exec php_api php artisan view:cache
docker exec php_api php artisan event:cache

# Permisos de storage
docker exec php_api chmod -R 775 storage bootstrap/cache
docker exec php_api chown -R www-data:www-data storage bootstrap/cache
```

### 3. Verificar Funcionamiento

```bash
# Ver logs
docker-compose logs -f nginx_api
docker-compose logs -f php_api

# Verificar salud
curl -I https://tudominio.com/health
curl -I https://tudominio.com/api/documentation
```

---

## 💾 Backup Automático

### Script de Backup Diario (`scripts/backup.sh`):

```bash
#!/bin/bash
# scripts/backup.sh

BACKUP_DIR="/var/www/apidian/backups"
DATE=$(date +%Y%m%d_%H%M%S)
S3_BUCKET="s3://tu-bucket-backups/apidian"
RETENTION_DAYS=7

# Crear directorio
mkdir -p $BACKUP_DIR

# Backup de base de datos
docker exec mariadb_api mariadb-dump -u user_api -p'password_segura' db_api | gzip > "$BACKUP_DIR/db_backup_$DATE.sql.gz"

# Backup de archivos (solo storage local, no S3)
tar -czf "$BACKUP_DIR/files_backup_$DATE.tar.gz" -C /var/www/apidian storage/app/local

# Subir a S3 (opcional)
aws s3 cp "$BACKUP_DIR/db_backup_$DATE.sql.gz" $S3_BUCKET/database/
aws s3 cp "$BACKUP_DIR/files_backup_$DATE.tar.gz" $S3_BUCKET/files/

# Eliminar backups antiguos (local)
find $BACKUP_DIR -name "*.gz" -mtime +$RETENTION_DAYS -delete

echo "Backup completado: $DATE"
```

### Configurar Cron para Backups:

```bash
chmod +x /var/www/apidian/scripts/backup.sh

# Agregar al crontab del servidor
sudo crontab -e
# Backup diario a las 2 AM
0 2 * * * /var/www/apidian/scripts/backup.sh >> /var/log/apidian_backup.log 2>&1
```

---

## 📊 Monitoreo

### Opción 1: Uptime Kuma (Self-hosted)

```yaml
# Agregar a docker-compose.yml
  uptime-kuma:
    image: louislam/uptime-kuma:latest
    container_name: uptime_kuma
    restart: unless-stopped
    ports:
      - "3001:3001"
    volumes:
      - uptime_data:/app/data
    networks:
      - apinet
```

### Opción 2: Health Checks con Alertas

```bash
# Script de monitoreo (scripts/monitor.sh)
#!/bin/bash
URL="https://tudominio.com/health"
EMAIL="admin@tudominio.com"

if ! curl -f -s "$URL" > /dev/null; then
    echo "APIDIAN está caído: $(date)" | mail -s "ALERTA: APIDIAN DOWN" $EMAIL
fi
```

---

## 🔧 Mantenimiento y Actualizaciones

### Actualizar la Aplicación:

```bash
cd /var/www/apidian

# Backup antes de actualizar
./scripts/backup.sh

# Descargar cambios
git pull origin main

# Actualizar dependencias
docker exec php_api composer install --no-dev --optimize-autoloader

# Ejecutar migraciones
docker exec php_api php artisan migrate --force

# Limpiar y regenerar caché
docker exec php_api php artisan optimize:clear
docker exec php_api php artisan optimize

# Reiniciar contenedores
docker-compose restart
```

### Ver Uso de Recursos:

```bash
# Estadísticas de Docker
docker stats

# Uso de disco
docker system df

# Limpiar recursos no utilizados
docker system prune -a --volumes
```

---

## 🐛 Troubleshooting

### Problema: Error 500

```bash
# Ver logs detallados
docker exec php_api cat storage/logs/laravel-$(date +%Y-%m-%d).log | tail -50

# Verificar permisos
docker exec php_api ls -la storage/
docker exec php_api chmod -R 775 storage
```

### Problema: Base de datos no conecta

```bash
# Verificar contenedor MariaDB
docker-compose ps mariadb
docker-compose logs mariadb

# Probar conexión desde PHP
docker exec php_api php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';"
```

### Problema: Permisos denegados en storage

```bash
docker exec php_api chown -R www-data:www-data storage bootstrap/cache
docker exec php_api chmod -R 775 storage bootstrap/cache
```

### Problema: SSL no funciona

```bash
# Verificar certificados
openssl x509 -in docker/ssl/cert.pem -text -noout | head -10

# Verificar puertos
sudo netstat -tlnp | grep 443
```

### Reiniciar todo:

```bash
cd /var/www/apidian
docker-compose down
docker-compose up -d --build
```

---

## 📞 Comandos Útiles Rápidos

```bash
# Entrar al contenedor PHP
docker exec -it php_api bash

# Ejecutar comandos Artisan
docker exec php_api php artisan [comando]

# Ver logs en tiempo real
docker-compose logs -f [servicio]

# Escalar workers de cola
docker-compose up -d --scale queue=4

# Backup manual de BD
docker exec mariadb_api mariadb-dump -u user_api -p'password' db_api > backup_$(date +%F).sql

# Restaurar BD
docker exec -i mariadb_api mysql -u user_api -p'password' db_api < backup.sql
```

---

## 🛡️ Checklist de Seguridad

- [ ] APP_DEBUG=false en producción
- [ ] APP_KEY generado y único
- [ ] Contraseñas de base de datos fuertes
- [ ] SSL/TLS configurado con certificados válidos
- [ ] Firewall activo (puertos 80, 443, 22 solo)
- [ ] Backups automatizados configurados
- [ ] Logs rotados (logrotate)
- [ ] Actualizaciones de seguridad automáticas
- [ ] Acceso SSH solo con clave (sin password)
- [ ] Fail2ban instalado

---

## 📚 Recursos Adicionales

- [Docker Documentation](https://docs.docker.com/)
- [Laravel Deployment](https://laravel.com/docs/10.x/deployment)
- [AWS S3 Best Practices](https://docs.aws.amazon.com/s3/latest/userguide/security-best-practices.html)
- [Certbot Documentation](https://eff-certbot.readthedocs.io/)

---

**Última actualización:** Abril 2026  
**Versión:** 1.0  
**Autor:** Equipo APIDIAN
