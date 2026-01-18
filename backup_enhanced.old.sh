#!/bin/bash
echo "📦 Starting enhanced backup process..."
echo "================================"

# Configuration
BACKUP_DIR="/backup"
MYSQL_ROOT_PASSWORD="OluwaTobi60"
RETENTION_DAYS=7
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Ensure backup directory exists
mkdir -p $BACKUP_DIR

# Function to log messages
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
log_info() { echo -e "📝 $1"; }

# 1. Backup MySQL databases
log_info "🗄️  Backing up MySQL databases..."
MYSQL_BACKUP_FILE="$BACKUP_DIR/mysql_backup_$TIMESTAMP.sql"
docker exec salespilot-mysql mysqldump -u root -p$MYSQL_ROOT_PASSWORD --all-databases --single-transaction > $MYSQL_BACKUP_FILE 2>/tmp/mysql_backup_error.log

if [ $? -eq 0 ]; then
    log_success "MySQL backup created: $MYSQL_BACKUP_FILE"
    
    # Compress the backup
    gzip $MYSQL_BACKUP_FILE
    log_success "Backup compressed: ${MYSQL_BACKUP_FILE}.gz"
    
    # Calculate backup size
    BACKUP_SIZE=$(du -h "${MYSQL_BACKUP_FILE}.gz" | cut -f1)
    log_info "Backup size: $BACKUP_SIZE"
else
    log_error "MySQL backup failed!"
    cat /tmp/mysql_backup_error.log
    exit 1
fi

# 2. Backup important configuration files
log_info "📄 Backing up configuration files..."
CONFIG_BACKUP_DIR="$BACKUP_DIR/config_backup_$TIMESTAMP"
mkdir -p $CONFIG_BACKUP_DIR

# Backup docker-compose.yml and .env files
cp /opt/salespilot/docker-compose.yml $CONFIG_BACKUP_DIR/
cp /opt/salespilot/.env $CONFIG_BACKUP_DIR/ 2>/dev/null || true

# Backup nginx config if exists
if [ -d "/opt/salespilot/nginx" ]; then
    cp -r /opt/salespilot/nginx $CONFIG_BACKUP_DIR/
fi

# Create tar archive of configs
tar -czf "$CONFIG_BACKUP_DIR.tar.gz" -C $BACKUP_DIR "config_backup_$TIMESTAMP"
rm -rf $CONFIG_BACKUP_DIR
log_success "Configuration backup created: $CONFIG_BACKUP_DIR.tar.gz"

# 3. Cleanup old backups
log_info "🧹 Cleaning up old backups (older than $RETENTION_DAYS days)..."
find $BACKUP_DIR -name "mysql_backup_*.sql.gz" -mtime +$RETENTION_DAYS -delete
find $BACKUP_DIR -name "config_backup_*.tar.gz" -mtime +$RETENTION_DAYS -delete
log_success "Old backups cleaned up"

# 4. List current backups
log_info "📋 Current backups:"
echo "MySQL backups:"
ls -lh $BACKUP_DIR/mysql_backup_*.sql.gz 2>/dev/null || echo "No MySQL backups found"
echo ""
echo "Config backups:"
ls -lh $BACKUP_DIR/config_backup_*.tar.gz 2>/dev/null || echo "No config backups found"

# 5. Calculate total backup size
TOTAL_SIZE=$(du -sh $BACKUP_DIR | cut -f1)
log_info "Total backup directory size: $TOTAL_SIZE"

echo ""
log_success "✅ Backup process completed successfully!"
echo "Backup location: $BACKUP_DIR"
