#!/bin/bash
echo "🔄 Starting restore process..."
echo "================================"

# Configuration
BACKUP_DIR="/backup"
MYSQL_ROOT_PASSWORD="OluwaTobi60"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }

# List available backups
echo "📋 Available backups:"
BACKUP_FILES=($(ls -t $BACKUP_DIR/mysql_backup_*.sql.gz 2>/dev/null))

if [ ${#BACKUP_FILES[@]} -eq 0 ]; then
    log_error "No backup files found in $BACKUP_DIR"
    exit 1
fi

for i in "${!BACKUP_FILES[@]}"; do
    echo "$((i+1)). ${BACKUP_FILES[$i]}"
done

# Ask user to select backup
echo ""
read -p "Select backup number to restore (1-${#BACKUP_FILES[@]}): " SELECTION

if ! [[ "$SELECTION" =~ ^[0-9]+$ ]] || [ "$SELECTION" -lt 1 ] || [ "$SELECTION" -gt ${#BACKUP_FILES[@]} ]; then
    log_error "Invalid selection"
    exit 1
fi

SELECTED_FILE="${BACKUP_FILES[$((SELECTION-1))]}"
echo "Selected: $SELECTED_FILE"

# Confirm restore
read -p "Are you sure you want to restore this backup? This will overwrite current data. (yes/no): " CONFIRM
if [ "$CONFIRM" != "yes" ]; then
    echo "Restore cancelled"
    exit 0
fi

# Stop application containers
echo "🛑 Stopping application containers..."
cd /opt/salespilot
docker-compose stop app nginx

# Restore database
echo "🗄️  Restoring database..."
gunzip -c "$SELECTED_FILE" | docker exec -i salespilot-mysql mysql -u root -p$MYSQL_ROOT_PASSWORD

if [ $? -eq 0 ]; then
    log_success "Database restored successfully"
else
    log_error "Database restore failed"
    exit 1
fi

# Start containers
echo "🚀 Starting containers..."
docker-compose start

echo ""
log_success "✅ Restore completed successfully!"
echo "You may need to restart the application containers: docker-compose restart"
