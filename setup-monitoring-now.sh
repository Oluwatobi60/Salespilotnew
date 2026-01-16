#!/bin/bash
echo "🔧 Setting up Monitoring Services"
echo "================================"

cd /opt/salespilot

echo "1. Cleaning up old monitoring containers..."
docker stop prometheus cadvisor node-exporter 2>/dev/null || true
docker rm prometheus cadvisor node-exporter 2>/dev/null || true

echo ""
echo "2. Creating monitoring directory..."
mkdir -p monitoring
cd monitoring

echo ""
echo "3. Creating Prometheus config..."
cat > prometheus.yml << 'PROM'
global:
  scrape_interval: 15s
  evaluation_interval: 15s

scrape_configs:
  - job_name: 'prometheus'
    static_configs:
      - targets: ['localhost:9090']

  - job_name: 'cadvisor'
    static_configs:
      - targets: ['cadvisor:8080']
    metrics_path: /metrics

  - job_name: 'node'
    static_configs:
      - targets: ['node-exporter:9100']
PROM

echo ""
echo "4. Starting Prometheus on port 9090..."
docker run -d \
  --name=prometheus \
  --network=salespilot_salespilot-network \
  -p 9090:9090 \
  -v $(pwd)/prometheus.yml:/etc/prometheus/prometheus.yml \
  prom/prometheus

echo ""
echo "5. Starting cAdvisor on port 8083..."
docker run -d \
  --name=cadvisor \
  --network=salespilot_salespilot-network \
  -p 8083:8080 \
  -v /:/rootfs:ro \
  -v /var/run:/var/run:rw \
  -v /sys:/sys:ro \
  -v /var/lib/docker/:/var/lib/docker:ro \
  gcr.io/cadvisor/cadvisor:v0.47.0

echo ""
echo "6. Starting Node Exporter on port 9100..."
docker run -d \
  --name=node-exporter \
  --network=salespilot_salespilot-network \
  -p 9100:9100 \
  -v "/:/host:ro,rslave" \
  quay.io/prometheus/node-exporter:latest \
  --path.rootfs=/host

echo ""
echo "7. Waiting for startup..."
sleep 10

echo ""
echo "✅ MONITORING SERVICES ARE READY!"
echo "================================="
echo ""
echo "🌐 Access URLs:"
echo "• Prometheus Metrics:    http://89.117.59.206:9090"
echo "• Container Metrics:     http://89.117.59.206:8083"
echo "• System Metrics:        http://89.117.59.206:9100"
echo "• Grafana Dashboard:     http://89.117.59.206:3000"
echo ""
echo "📈 To configure Grafana:"
echo "1. Go to http://89.117.59.206:3000"
echo "2. Add Prometheus data source with URL: http://prometheus:9090"
echo "3. Import dashboard ID 193 for Docker monitoring"
echo ""
echo "🐳 Current running services:"
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
