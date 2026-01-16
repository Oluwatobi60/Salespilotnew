#!/bin/bash
echo "🚀 Starting deployment script..."
echo "================================"
echo "Date: $(date)"
echo "User: $(whoami)"
echo "Directory: $(pwd)"

# Navigate to application directory
cd /opt/salespilot

# Pull latest code
echo "📥 Pulling latest code..."
git pull origin master

# Stop and restart containers
echo "🛑 Stopping containers..."
docker-compose down

echo "🚀 Starting containers..."
docker-compose up -d --build

# Wait for containers to start
echo "⏳ Waiting for containers to start..."
sleep 30

# Check status
echo "📊 Checking deployment status..."
echo ""
echo "🐳 Docker containers:"
docker ps
echo ""
echo "📈 Container logs (last 20 lines):"
docker-compose logs --tail=20

echo "✅ Deployment script completed!"
