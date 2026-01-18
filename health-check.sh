#!/bin/bash
echo "🔍 Salespilot Health Check"
echo "=========================="

cd /opt/salespilot

# Check containers
echo "1. Container Status:"
docker-compose ps

echo ""
echo "2. Application URLs:"
echo "   Main App:    http://localhost:8787"
echo "   MySQL Admin: http://localhost:33069"

echo ""
echo "3. Testing endpoints..."
if curl -s -f --max-time 5 http://localhost:8787 > /dev/null; then
    echo "✅ Main application is responding"
else
    echo "❌ Main application is down"
fi

echo ""
echo "4. Laravel-specific checks:"
# Check Laravel caches
if docker-compose exec app php artisan config:cache --help > /dev/null 2>&1; then
    echo "✅ Laravel artisan is working"
else
    echo "❌ Laravel artisan is not working"
fi

echo ""
echo "5. Database connection:"
docker-compose exec app php artisan tinker --execute="
try {
    \$count = \\DB::table('migrations')->count();
    echo '✅ Database connected, migrations count: ' . \$count;
} catch (\\Exception \$e) {
    echo '❌ Database error: ' . \$e->getMessage();
}
" 2>/dev/null || echo "⚠️ Cannot test database"

echo ""
echo "6. Disk space:"
df -h /opt
