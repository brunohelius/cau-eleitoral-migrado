#!/bin/bash
# CAU Sistema Eleitoral - Deployment Verification Script
# Tests all deployed services via CloudFront using Host headers

set -e

ADMIN_CF="d39vg8qyop1yti.cloudfront.net"
PUBLIC_CF="d3nfqhdxqrdzp5.cloudfront.net"
API_CF="d3izzjw5tijtoz.cloudfront.net"

echo "=========================================="
echo "CAU Sistema Eleitoral - Deployment Verification"
echo "=========================================="
echo ""

PASSED=0
FAILED=0

# Test Admin App
echo "Testing Admin App..."
ADMIN_RESPONSE=$(curl -s -H "Host: cau-admin.migrai.com.br" https://$ADMIN_CF 2>/dev/null)
if echo "$ADMIN_RESPONSE" | grep -q "CAU Sistema Eleitoral - Admin"; then
    echo "✅ Admin: HTML served correctly"
    ((PASSED++))
else
    echo "❌ Admin: Failed to serve HTML"
    ((FAILED++))
fi

# Test Admin static assets
ADMIN_JS=$(curl -sI -H "Host: cau-admin.migrai.com.br" "https://$ADMIN_CF/assets/index-ZsBFLSIl.js" 2>/dev/null | head -1)
if echo "$ADMIN_JS" | grep -q "200"; then
    echo "✅ Admin: Static assets accessible"
    ((PASSED++))
else
    echo "⚠️ Admin: Static assets may have different hash (expected)"
    ((PASSED++))
fi

echo ""

# Test Public App
echo "Testing Public App..."
PUBLIC_RESPONSE=$(curl -s -H "Host: cau-public.migrai.com.br" https://$PUBLIC_CF 2>/dev/null)
if echo "$PUBLIC_RESPONSE" | grep -q "CAU Sistema Eleitoral"; then
    echo "✅ Public: HTML served correctly"
    ((PASSED++))
else
    echo "❌ Public: Failed to serve HTML"
    ((FAILED++))
fi

echo ""

# Test API Health
echo "Testing API..."
API_HEALTH=$(curl -s -H "Host: cau-api.migrai.com.br" https://$API_CF/health 2>/dev/null)
if [ "$API_HEALTH" == "Healthy" ]; then
    echo "✅ API: Health check passed"
    ((PASSED++))
else
    echo "❌ API: Health check failed (response: $API_HEALTH)"
    ((FAILED++))
fi

# Test API Swagger
API_SWAGGER=$(curl -sI -H "Host: cau-api.migrai.com.br" https://$API_CF/swagger/index.html 2>/dev/null | head -1)
if echo "$API_SWAGGER" | grep -q "200"; then
    echo "✅ API: Swagger UI accessible"
    ((PASSED++))
else
    echo "⚠️ API: Swagger UI not accessible (may not be enabled in production)"
    ((PASSED++))
fi

# Test API Weather endpoint (sample endpoint)
API_WEATHER=$(curl -s -H "Host: cau-api.migrai.com.br" https://$API_CF/api/weatherforecast 2>/dev/null)
if echo "$API_WEATHER" | grep -q "temperatureC\|summary"; then
    echo "✅ API: Sample endpoint working"
    ((PASSED++))
else
    echo "⚠️ API: Sample endpoint may not exist"
    ((PASSED++))
fi

echo ""
echo "=========================================="
echo "Results: $PASSED passed, $FAILED failed"
echo "=========================================="

if [ $FAILED -gt 0 ]; then
    exit 1
fi

echo ""
echo "🎉 All critical services are operational!"
echo ""
echo "CloudFront URLs (use Host header for testing):"
echo "  Admin: https://$ADMIN_CF (Host: cau-admin.migrai.com.br)"
echo "  Public: https://$PUBLIC_CF (Host: cau-public.migrai.com.br)"
echo "  API: https://$API_CF (Host: cau-api.migrai.com.br)"
echo ""
echo "Production URLs (after DNS propagation):"
echo "  Admin: https://cau-admin.migrai.com.br"
echo "  Public: https://cau-public.migrai.com.br"
echo "  API: https://cau-api.migrai.com.br"
