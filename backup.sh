#!/bin/bash
echo "📦 Starting backup process..."
echo "================================"

# Create timestamp
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup"

# Ensure backup directory exists
mkdir -p $BACKUP_DIR

# Database backup
echo "🗄️  Backing up MySQL databases..."
docker exec salespilot-mysql mysqldump -u root -pOluwaTobi60 --all-databases > $BACKUP_DIR/mysql_backup_$TIMESTAMP.sql

# Check if backup was successful
if [ $? -eq 0 ]; then
    echo "✅ MySQL backup created: $BACKUP_DIR/mysql_backup_$TIMESTAMP.sql"
    # Compress the backup
    gzip $BACKUP_DIR/mysql_backup_$TIMESTAMP.sql
    echo "✅ Backup compressed: $BACKUP_DIR/mysql_backup_$TIMESTAMP.sql.gz"
    
    # Keep only last 7 backups
    echo "🧹 Cleaning old backups (keeping last 7)..."
    ls -t $BACKUP_DIR/mysql_backup_*.sql.gz | tail -n +8 | xargs rm -f
else
    echo "❌ MySQL backup failed!"
    exit 1
fi

echo "✅ Backup completed successfully!"
echo "Backup file: $BACKUP_DIR/mysql_backup_$TIMESTAMP.sql.gz"
