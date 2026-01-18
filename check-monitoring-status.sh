#!/bin/bash
echo "🔍 Monitoring Services Status Check"
echo "=================================="

echo "1. Checking running containers:"
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep -E "(prometheus|cadvisor|node-exporter|mysql)"

echo ""
echo "2. Testing each service endpoint:"

# Prometheus
echo -n "Prometheus (9090): "
curl -s -o /dev/null -w "%{http_code}" --max-time 3 http://localhost:9090 && echo " OK" || echo " FAILED"

# cAdvisor
echo -n "cAdvisor (8083): "
curl -s -o /dev/null -w "%{http_code}" --max-time 3 http://localhost:8083 && echo " OK" || echo " FAILED"

# Node Exporter
echo -n "Node Exporter (9100): "
curl -s -o /dev/null -w "%{http_code}" --max-time 3 http://localhost:9100 && echo " OK" || echo " FAILED"

echo ""
echo "3. Checking Prometheus targets (what it's actually scraping):"
curl -s http://localhost:9090/api/v1/targets | jq -r '.data.activeTargets[] | .scrapePool + " -> " + .health + " (" + .scrapeUrl + ")"'

echo ""
echo "4. Testing from Grafana container:"
docker exec monitor curl -s http://prometheus:9090/api/v1/query?query=up | jq -r '.data.result[] | .metric.job + ": " + .metric.instance'
