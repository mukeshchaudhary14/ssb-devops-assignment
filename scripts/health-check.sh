#!/bin/bash
# ==========================================
# SSB Digital - Stack Health Check Script
# ==========================================

EXIT_CODE=0

echo "🔍 Running Health Checks..."

# 1. Check Apache
if systemctl is-active --quiet apache2; then
    echo "✅ Apache Web Server: Running (Port 80)"
else
    echo "❌ Apache Web Server: DOWN"
    EXIT_CODE=1
fi

# 2. Check MySQL
if systemctl is-active --quiet mysql; then
    echo "✅ MySQL Database Server: Running (Port 3306)"
else
    echo "❌ MySQL Database Server: DOWN"
    EXIT_CODE=1
fi

# 3. Check Web HTTP Endpoint
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/ssb-app/index.php || echo "000")
if [ "$HTTP_STATUS" == "200" ]; then
    echo "✅ Web Application (/ssb-app): Responding HTTP 200 OK"
else
    echo "❌ Web Application (/ssb-app): Unreachable (HTTP $HTTP_STATUS)"
    EXIT_CODE=1
fi

if [ $EXIT_CODE -eq 0 ]; then
    echo "🎉 All services healthy!"
else
    echo "⚠️ One or more services failed health check!"
fi

exit $EXIT_CODE
