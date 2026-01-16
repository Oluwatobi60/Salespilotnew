#!/bin/bash
echo "🧪 Testing All Monitoring Services"
echo "================================="

echo "1. Checking container status:"
for service in prometheus cadvisor node-exporter; do
    if docker ps | grep -q $service; then
        STATUS="✅ RUNNING"
    else
        STATUS="❌ STOPPED"
    fi
    echo "   $service: $STATUS"
done

echo ""
echo "2. Testing service accessibility:"

# Test Prometheus
if curl -s --max-time 5 http://localhost:9090 > /dev/null; then
    echo "✅ Prometheus (9090): Accessible"
else
    echo "❌ Prometheus (9090): Not accessible"
fi

# Test cAdvisor
if curl -s --max-time 5 http://localhost:8083 > /dev/null; then
    echo "✅ cAdvisor (8083): Accessible"
else
    echo "❌ cAdvisor (8083): Not accessible"
fi

# Test Node Exporter
if curl -s --max-time 5 http://localhost:9100 > /dev/null; then
    echo "✅ Node Exporter (9100): Accessible"
else
    echo "❌ Node Exporter (9100): Not accessible"
fi

echo ""
echo "3. Testing from Grafana container:"
if docker exec monitor curl -s http://prometheus:9090/-/healthy > /dev/null 2>&1; then
    echo "✅ Grafana can reach Prometheus"
else
    echo "❌ Grafana cannot reach Prometheus"
fi

echo ""
echo "4. Checking Apache2 ports (for reference):"
echo "Apache2 is using: 80, 8080, 8081"
echo "These ports are occupied by Apache, not Docker"
