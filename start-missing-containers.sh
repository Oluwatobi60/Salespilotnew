#!/bin/bash
echo "🔧 Starting Missing Containers"
echo "=============================="

cd /opt/salespilot

echo "1. Current status:"
docker-compose ps

echo ""
echo "2. Starting nginx and app containers..."
docker-compose up -d nginx app

echo ""
echo "3. Waiting..."
sleep 15

echo ""
echo "4. Checking..."
docker-compose ps

echo ""
echo "5. Testing application..."
if curl -s -f http://localhost:8787 > /dev/null; then
    echo "✅ Application is now running!"
else
    echo "❌ Still not working"
    echo "Logs:"
    docker-compose logs --tail=20
fi
