#!/bin/bash
echo "📊 Application Status Check"
echo "================================"

cd /opt/salespilot

echo "🐳 Docker Containers:"
docker-compose ps

echo ""
echo "📈 Container Logs (last 5 lines):"
docker-compose logs --tail=5

echo ""
echo "💾 Disk Usage:"
df -h / /backup

echo ""
echo "📦 Latest Backups:"
ls -lh /backup/mysql_backup_*.sql.gz 2>/dev/null | head -5 || echo "No backups found"

echo ""
echo "🌐 Application URL: http://$(curl -s ifconfig.me):8787"
