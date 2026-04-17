#!/bin/bash

# =============================================================================
# Script de Despliegue APIDIAN - Cosmox-SAS
# =============================================================================
# Autor: Cosmox-SAS
# Fecha: Abril 2026
# Descripcion: Script automatizado para desplegar APIDIAN en produccion
# =============================================================================

set -e  # Exit on error

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color
BOLD='\033[1m'

# Variables configurables
PROJECT_NAME="apidian"
INSTALL_DIR="/var/www/${PROJECT_NAME}"
GITHUB_REPO="https://github.com/Cosmox-SAS/co-apidian2026.git"

# Funciones de utilidad
print_header() {
    echo -e "\n${BLUE}${BOLD}=========================================${NC}"
    echo -e "${BLUE}${BOLD}$1${NC}"
    echo -e "${BLUE}${BOLD}=========================================${NC}\n"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Verificar si se ejecuta como root
check_root() {
    if [[ $EUID -eq 0 ]]; then
       print_error "No ejecutes este script como root"
       print_info "El script usara sudo cuando sea necesario"
       exit 1
    fi
}

# Verificar requisitos del sistema
check_requirements() {
    print_header "VERIFICANDO REQUISITOS DEL SISTEMA"
    
    # Verificar Docker
    if ! command -v docker &> /dev/null; then
        print_error "Docker no esta instalado"
        print_info "Instalando Docker..."
        install_docker
    else
        DOCKER_VERSION=$(docker --version | grep -oE '[0-9]+\.[0-9]+')
        print_success "Docker instalado (v${DOCKER_VERSION})"
    fi
    
    # Verificar Docker Compose
    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose no esta instalado"
        print_info "Instalando Docker Compose..."
        install_docker_compose
    else
        COMPOSE_VERSION=$(docker-compose --version | grep -oE '[0-9]+\.[0-9]+')
        print_success "Docker Compose instalado (v${COMPOSE_VERSION})"
    fi
    
    # Verificar Git
    if ! command -v git &> /dev/null; then
        print_error "Git no esta instalado"
        print_info "Instalando Git..."
        sudo apt-get update && sudo apt-get install -y git
    else
        print_success "Git instalado"
    fi
    
    # Verificar que el usuario esta en el grupo docker
    if ! groups | grep -q 'docker'; then
        print_warning "Usuario no esta en el grupo docker"
        print_info "Agregando usuario al grupo docker..."
        sudo usermod -aG docker $USER
        print_warning "Por favor cierra sesion y vuelve a iniciar para aplicar los cambios"
        exit 1
    fi
    
    print_success "Todos los requisitos verificados"
}

# Instalar Docker
install_docker() {
    print_info "Instalando Docker..."
    sudo apt-get update
    sudo apt-get install -y apt-transport-https ca-certificates curl gnupg lsb-release
    
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
    
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
    
    sudo apt-get update
    sudo apt-get install -y docker-ce docker-ce-cli containerd.io
    
    sudo systemctl enable docker
    sudo systemctl start docker
    
    print_success "Docker instalado correctamente"
}

# Instalar Docker Compose
install_docker_compose() {
    print_info "Instalando Docker Compose..."
    sudo curl -L "https://github.com/docker/compose/releases/download/v2.23.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    sudo ln -sf /usr/local/bin/docker-compose /usr/bin/docker-compose
    print_success "Docker Compose instalado correctamente"
}

# Crear directorio de instalacion
create_directories() {
    print_header "CREANDO ESTRUCTURA DE DIRECTORIOS"
    
    if [ -d "$INSTALL_DIR" ]; then
        print_warning "El directorio $INSTALL_DIR ya existe"
        read -p "¿Deseas sobrescribirlo? (s/N): " overwrite
        if [[ $overwrite =~ ^[Ss]$ ]]; then
            print_info "Respaldando instalacion anterior..."
            sudo mv "$INSTALL_DIR" "${INSTALL_DIR}.backup.$(date +%Y%m%d_%H%M%S)"
        else
            print_info "Usando directorio existente"
            return
        fi
    fi
    
    sudo mkdir -p "$INSTALL_DIR"
    sudo chown $USER:$USER "$INSTALL_DIR"
    print_success "Directorio creado: $INSTALL_DIR"
}

# Clonar repositorio
clone_repository() {
    print_header "CLONANDO REPOSITORIO"
    
    cd "$INSTALL_DIR"
    
    if [ -d ".git" ]; then
        print_warning "El directorio ya es un repositorio git"
        read -p "¿Deseas actualizarlo? (s/N): " update
        if [[ $update =~ ^[Ss]$ ]]; then
            git pull origin master
        fi
    else
        print_info "Clonando desde $GITHUB_REPO..."
        git clone "$GITHUB_REPO" .
    fi
    
    print_success "Repositorio listo"
}

# Configurar variables de entorno
setup_environment() {
    print_header "CONFIGURANDO VARIABLES DE ENTORNO"
    
    cd "$INSTALL_DIR"
    
    if [ ! -f ".env" ]; then
        if [ -f ".env.example" ]; then
            cp .env.example .env
            print_success "Archivo .env creado desde .env.example"
        else
            print_error "No se encontro .env.example"
            exit 1
        fi
    else
        print_warning "El archivo .env ya existe"
    fi
    
    print_info "Por favor configura las siguientes variables en .env:"
    echo ""
    echo -e "${YELLOW}1. APP_URL${NC} - URL de tu dominio (ej: https://tudominio.com)"
    echo -e "${YELLOW}2. DB_PASSWORD${NC} - Contraseña segura para la base de datos"
    echo -e "${YELLOW}3. AWS_ACCESS_KEY_ID${NC} - Tu Access Key de AWS S3"
    echo -e "${YELLOW}4. AWS_SECRET_ACCESS_KEY${NC} - Tu Secret Key de AWS S3"
    echo -e "${YELLOW}5. AWS_BUCKET${NC} - Nombre de tu bucket S3"
    echo -e "${YELLOW}6. MAIL_USERNAME${NC} - Usuario SMTP (AWS SES)"
    echo -e "${YELLOW}7. MAIL_PASSWORD${NC} - Contraseña SMTP"
    echo ""
    
    read -p "¿Deseas editar el archivo .env ahora? (s/N): " edit_env
    if [[ $edit_env =~ ^[Ss]$ ]]; then
        if command -v nano &> /dev/null; then
            nano .env
        elif command -v vim &> /dev/null; then
            vim .env
        else
            print_error "No se encontro editor (nano/vim)"
            print_info "Edita manualmente: $INSTALL_DIR/.env"
        fi
    fi
    
    # Configurar DB_HOST para Docker
    if grep -q "^DB_HOST=127.0.0.1" .env 2>/dev/null; then
        sed -i 's/^DB_HOST=127.0.0.1/DB_HOST=mariadb/' .env
        print_success "DB_HOST configurado para Docker (mariadb)"
    fi
    
    # Configurar FILESYSTEM_DRIVER
    if grep -q "^FILESYSTEM_DRIVER=local" .env 2>/dev/null; then
        read -p "¿Deseas usar S3 para almacenamiento? (s/N): " use_s3
        if [[ $use_s3 =~ ^[Ss]$ ]]; then
            sed -i 's/^FILESYSTEM_DRIVER=local/FILESYSTEM_DRIVER=s3/' .env
            print_success "FILESYSTEM_DRIVER configurado a S3"
        fi
    fi
}

# Crear red Docker
create_docker_network() {
    print_header "CONFIGURANDO RED DOCKER"
    
    if ! docker network ls | grep -q "apinet"; then
        docker network create apinet
        print_success "Red Docker 'apinet' creada"
    else
        print_success "Red Docker 'apinet' ya existe"
    fi
}

# Construir y levantar contenedores
deploy_containers() {
    print_header "DESPLEGANDO CONTENEDORES"
    
    cd "$INSTALL_DIR"
    
    print_info "Construyendo imagenes Docker..."
    docker-compose build --no-cache
    
    print_info "Iniciando contenedores..."
    docker-compose up -d
    
    print_info "Esperando que los servicios esten listos..."
    sleep 10
    
    print_info "Estado de los contenedores:"
    docker-compose ps
}

# Configurar Laravel
setup_laravel() {
    print_header "CONFIGURANDO LARAVEL"
    
    cd "$INSTALL_DIR"
    
    print_info "Instalando dependencias de Composer..."
    docker exec php_api composer install --no-dev --optimize-autoloader --no-interaction
    
    # Generar APP_KEY si no existe
    if ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then
        print_info "Generando APP_KEY..."
        docker exec php_api php artisan key:generate
        print_success "APP_KEY generado"
    fi
    
    # Ejecutar migraciones
    print_info "Ejecutando migraciones..."
    docker exec php_api php artisan migrate --force
    
    # Ejecutar seeders
    read -p "¿Deseas ejecutar los seeders para datos de referencia? (s/N): " run_seeders
    if [[ $run_seeders =~ ^[Ss]$ ]]; then
        print_info "Ejecutando seeders..."
        docker exec php_api php artisan db:seed --force
    fi
    
    # Configurar permisos
    print_info "Configurando permisos..."
    docker exec php_api mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache
    docker exec php_api chmod -R 775 storage bootstrap/cache
    
    # Optimizar para produccion
    print_info "Optimizando para produccion..."
    docker exec php_api php artisan config:cache
    docker exec php_api php artisan route:cache
    docker exec php_api php artisan view:cache
    
    print_success "Laravel configurado correctamente"
}

# Configurar SSL con Let's Encrypt
setup_ssl() {
    print_header "CONFIGURACION SSL (OPCIONAL)"
    
    read -p "¿Deseas configurar SSL con Let's Encrypt? (s/N): " setup_ssl
    if [[ $setup_ssl =~ ^[Ss]$ ]]; then
        if ! command -v certbot &> /dev/null; then
            print_info "Instalando Certbot..."
            sudo apt-get update
            sudo apt-get install -y certbot
        fi
        
        read -p "Ingresa tu dominio (ej: api.tudominio.com): " domain
        
        print_info "Generando certificado para $domain..."
        sudo certbot certonly --standalone -d "$domain"
        
        sudo mkdir -p "$INSTALL_DIR/docker/ssl"
        sudo cp "/etc/letsencrypt/live/$domain/fullchain.pem" "$INSTALL_DIR/docker/ssl/cert.pem"
        sudo cp "/etc/letsencrypt/live/$domain/privkey.pem" "$INSTALL_DIR/docker/ssl/key.pem"
        sudo chown -R $USER:$USER "$INSTALL_DIR/docker/ssl"
        
        print_success "Certificados SSL configurados"
        print_info "Recuerda configurar la renovacion automatica con crontab"
    fi
}

# Configurar backup automatico
setup_backups() {
    print_header "CONFIGURACION DE BACKUPS (OPCIONAL)"
    
    read -p "¿Deseas configurar backups automaticos? (s/N): " setup_backup
    if [[ $setup_backup =~ ^[Ss]$ ]]; then
        BACKUP_SCRIPT="$INSTALL_DIR/scripts/backup.sh"
        
        mkdir -p "$INSTALL_DIR/backups"
        mkdir -p "$INSTALL_DIR/scripts"
        
        cat > "$BACKUP_SCRIPT" << 'BACKUP_EOF'
#!/bin/bash
BACKUP_DIR="/var/www/apidian/backups"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=7

mkdir -p $BACKUP_DIR

# Backup de base de datos
cd /var/www/apidian
DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2)
DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2)
DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)

docker exec mariadb_api mariadb-dump -u ${DB_USERNAME} -p"${DB_PASSWORD}" ${DB_DATABASE} 2>/dev/null | gzip > "$BACKUP_DIR/db_backup_$DATE.sql.gz"

# Backup de archivos locales
tar -czf "$BACKUP_DIR/files_backup_$DATE.tar.gz" -C /var/www/apidian storage/app/local 2>/dev/null || true

# Eliminar backups antiguos
find $BACKUP_DIR -name "*.gz" -mtime +$RETENTION_DAYS -delete

echo "Backup completado: $DATE"
BACKUP_EOF
        
        chmod +x "$BACKUP_SCRIPT"
        
        read -p "¿A que hora ejecutar el backup diario? (0-23, default: 2): " backup_hour
        backup_hour=${backup_hour:-2}
        
        (crontab -l 2>/dev/null; echo "0 $backup_hour * * * $BACKUP_SCRIPT >> /var/log/apidian_backup.log 2>&1") | crontab -
        
        print_success "Backup configurado para ejecutarse diariamente a las ${backup_hour}:00"
    fi
}

# Verificar estado final
verify_deployment() {
    print_header "VERIFICANDO DESPLIEGUE"
    
    cd "$INSTALL_DIR"
    
    print_info "Estado de contenedores:"
    docker-compose ps
    
    APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2)
    if [ -n "$APP_URL" ]; then
        print_info "Verificando aplicacion..."
        sleep 5
        
        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL" 2>/dev/null || echo "000")
        if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
            print_success "Aplicacion respondiendo correctamente (HTTP $HTTP_CODE)"
        else
            print_warning "La aplicacion no responde HTTP 200/302 (codigo: $HTTP_CODE)"
            print_info "Verifica los logs: docker-compose logs -f"
        fi
    fi
    
    print_success "Despliegue completado"
}

# Mostrar informacion final
show_final_info() {
    print_header "DESPLIEGUE COMPLETADO"
    
    echo -e "${GREEN}${BOLD}✅ APIDIAN ha sido desplegado exitosamente${NC}\n"
    
    echo -e "${BOLD}📍 Ubicacion:${NC} $INSTALL_DIR"
    APP_URL=$(grep "^APP_URL=" "$INSTALL_DIR/.env" 2>/dev/null | cut -d '=' -f2)
    echo -e "${BOLD}🌐 URL:${NC} ${APP_URL:-'No configurada'}"
    echo -e "${BOLD}📧 Admin:${NC} Revisa usuario_admin.txt para credenciales"
    echo ""
    
    echo -e "${BOLD}🛠️  Comandos utiles:${NC}"
    echo "  cd $INSTALL_DIR"
    echo "  docker-compose ps           # Ver estado"
    echo "  docker-compose logs -f      # Ver logs"
    echo "  docker exec php_api bash    # Entrar al contenedor"
    echo ""
    
    echo -e "${BOLD}📁 Archivos importantes:${NC}"
    echo "  .env                        # Configuracion"
    echo "  docker-compose.yml          # Contenedores"
    echo "  cosmox-sas-docs/            # Documentacion"
    echo ""
    
    print_info "Para actualizar desde facturalatam, revisa cosmox-sas-docs/WORKFLOW.md"
}

# Funcion principal
main() {
    print_header "DESPLIEGUE DE APIDIAN - COSMOX-SAS"
    print_info "Este script desplegara APIDIAN en produccion usando Docker Compose"
    echo ""
    
    read -p "¿Continuar con el despliegue? (s/N): " confirm
    if [[ ! $confirm =~ ^[Ss]$ ]]; then
        print_info "Despliegue cancelado"
        exit 0
    fi
    
    check_root
    check_requirements
    create_directories
    clone_repository
    setup_environment
    create_docker_network
    deploy_containers
    setup_laravel
    setup_ssl
    setup_backups
    verify_deployment
    show_final_info
}

# Ejecutar
main
