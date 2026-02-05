#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
AWS_ACCOUNT_ID="801232946361"
AWS_REGION="us-east-1"
S3_BUCKET="cau-eleitoral-backups-801232946361"
CODEBUILD_PROJECT="cau-eleitoral-build"

echo -e "${GREEN}🚀 CAU Sistema Eleitoral - Deploy via CodeBuild${NC}"
echo "================================================"
echo "AWS Account: ${AWS_ACCOUNT_ID}"
echo "Region: ${AWS_REGION}"
echo ""

# Get project root
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "${PROJECT_ROOT}"

# Step 1: Create source zip
echo -e "${YELLOW}📦 Creating source archive...${NC}"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
ZIP_FILE="/tmp/cau-eleitoral-source-${TIMESTAMP}.zip"

# Create zip excluding unnecessary files
zip -r "${ZIP_FILE}" . \
    -x "*.git/*" \
    -x "node_modules/*" \
    -x "*/node_modules/*" \
    -x "*.terraform/*" \
    -x "*/bin/*" \
    -x "*/obj/*" \
    -x "test-results/*" \
    -x "playwright-report/*" \
    -x ".playwright-mcp/*" \
    -x "*.zip" \
    > /dev/null

echo -e "${GREEN}✅ Source archive created: ${ZIP_FILE}${NC}"

# Step 2: Upload to S3
echo -e "${YELLOW}⬆️  Uploading to S3...${NC}"
aws s3 cp "${ZIP_FILE}" "s3://${S3_BUCKET}/codebuild/source.zip" --region ${AWS_REGION}
echo -e "${GREEN}✅ Source uploaded to s3://${S3_BUCKET}/codebuild/source.zip${NC}"

# Step 3: Start CodeBuild
echo -e "${YELLOW}🔨 Starting CodeBuild...${NC}"
BUILD_ID=$(aws codebuild start-build \
    --project-name ${CODEBUILD_PROJECT} \
    --region ${AWS_REGION} \
    --query 'build.id' \
    --output text)

echo -e "${GREEN}✅ Build started: ${BUILD_ID}${NC}"
echo ""

# Step 4: Monitor build
echo -e "${YELLOW}📊 Monitoring build progress...${NC}"
while true; do
    BUILD_STATUS=$(aws codebuild batch-get-builds \
        --ids "${BUILD_ID}" \
        --region ${AWS_REGION} \
        --query 'builds[0].buildStatus' \
        --output text)

    BUILD_PHASE=$(aws codebuild batch-get-builds \
        --ids "${BUILD_ID}" \
        --region ${AWS_REGION} \
        --query 'builds[0].currentPhase' \
        --output text)

    echo -e "  Status: ${BUILD_STATUS} | Phase: ${BUILD_PHASE}"

    if [ "${BUILD_STATUS}" = "SUCCEEDED" ]; then
        echo ""
        echo -e "${GREEN}🎉 Build completed successfully!${NC}"
        break
    elif [ "${BUILD_STATUS}" = "FAILED" ] || [ "${BUILD_STATUS}" = "FAULT" ] || [ "${BUILD_STATUS}" = "STOPPED" ]; then
        echo ""
        echo -e "${RED}❌ Build failed with status: ${BUILD_STATUS}${NC}"
        echo "Check logs: aws logs tail /aws/codebuild/cau-eleitoral --follow"
        exit 1
    fi

    sleep 15
done

# Cleanup
rm -f "${ZIP_FILE}"

echo ""
echo "Application URLs:"
echo "  Admin:  https://cau-admin.migrai.com.br"
echo "  Public: https://cau-public.migrai.com.br"
echo "  API:    https://cau-api.migrai.com.br"
echo ""
echo "To check ECS services status:"
echo "  aws ecs describe-services --cluster cau-eleitoral-cluster --services cau-eleitoral-api cau-eleitoral-admin cau-eleitoral-public --query 'services[*].{Name:serviceName,Running:runningCount,Desired:desiredCount}' --output table"
