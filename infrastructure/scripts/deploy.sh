#!/bin/bash
# =============================================================================
# CAU Sistema Eleitoral - Deployment Script
# =============================================================================

set -e

# Configuration
AWS_REGION="us-east-1"
AWS_ACCOUNT_ID="801232946361"
ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
PROJECT_NAME="cau-eleitoral"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}CAU Sistema Eleitoral - Deploy Script${NC}"
echo -e "${GREEN}========================================${NC}"

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"

# Check AWS credentials
echo -e "\n${YELLOW}[1/7] Checking AWS credentials...${NC}"
aws sts get-caller-identity > /dev/null 2>&1 || {
    echo -e "${RED}Error: AWS credentials not configured${NC}"
    exit 1
}
echo -e "${GREEN}AWS credentials OK${NC}"

# Login to ECR
echo -e "\n${YELLOW}[2/7] Logging in to ECR...${NC}"
aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_REGISTRY}
echo -e "${GREEN}ECR login successful${NC}"

# Build and push API image
echo -e "\n${YELLOW}[3/7] Building API image...${NC}"
cd "${PROJECT_ROOT}/apps/api"
docker build -f "${PROJECT_ROOT}/infrastructure/docker/Dockerfile.api" -t ${ECR_REGISTRY}/${PROJECT_NAME}-api:latest .
docker push ${ECR_REGISTRY}/${PROJECT_NAME}-api:latest
echo -e "${GREEN}API image pushed${NC}"

# Build and push Admin image
echo -e "\n${YELLOW}[4/7] Building Admin image...${NC}"
cd "${PROJECT_ROOT}/apps/admin"

# Create production .env for build
cat > .env.production << EOF
VITE_API_URL=https://cau-api.migrai.com.br/api
VITE_APP_NAME=CAU Sistema Eleitoral Admin
VITE_APP_ENV=production
EOF

docker build -f "${PROJECT_ROOT}/infrastructure/docker/Dockerfile.admin" -t ${ECR_REGISTRY}/${PROJECT_NAME}-admin:latest .
docker push ${ECR_REGISTRY}/${PROJECT_NAME}-admin:latest
echo -e "${GREEN}Admin image pushed${NC}"

# Build and push Public image
echo -e "\n${YELLOW}[5/7] Building Public image...${NC}"
cd "${PROJECT_ROOT}/apps/public"

# Create production .env for build
cat > .env.production << EOF
VITE_API_URL=https://cau-api.migrai.com.br/api
VITE_APP_NAME=CAU Sistema Eleitoral
VITE_APP_ENV=production
EOF

docker build -f "${PROJECT_ROOT}/infrastructure/docker/Dockerfile.public" -t ${ECR_REGISTRY}/${PROJECT_NAME}-public:latest .
docker push ${ECR_REGISTRY}/${PROJECT_NAME}-public:latest
echo -e "${GREEN}Public image pushed${NC}"

# Update ECS services
echo -e "\n${YELLOW}[6/7] Updating ECS services...${NC}"
CLUSTER_NAME="${PROJECT_NAME}-cluster"

# Update API service
aws ecs update-service --cluster ${CLUSTER_NAME} --service ${PROJECT_NAME}-api --force-new-deployment --region ${AWS_REGION} > /dev/null
echo "API service update initiated"

# Update Admin service
aws ecs update-service --cluster ${CLUSTER_NAME} --service ${PROJECT_NAME}-admin --force-new-deployment --region ${AWS_REGION} > /dev/null
echo "Admin service update initiated"

# Update Public service
aws ecs update-service --cluster ${CLUSTER_NAME} --service ${PROJECT_NAME}-public --force-new-deployment --region ${AWS_REGION} > /dev/null
echo "Public service update initiated"

echo -e "${GREEN}All services update initiated${NC}"

# Wait for deployments
echo -e "\n${YELLOW}[7/7] Waiting for deployments to stabilize...${NC}"
echo "This may take a few minutes..."

aws ecs wait services-stable --cluster ${CLUSTER_NAME} --services ${PROJECT_NAME}-api ${PROJECT_NAME}-admin ${PROJECT_NAME}-public --region ${AWS_REGION}

echo -e "\n${GREEN}========================================${NC}"
echo -e "${GREEN}Deployment Complete!${NC}"
echo -e "${GREEN}========================================${NC}"
echo -e "\nApplication URLs:"
echo -e "  Admin:  https://cau-admin.migrai.com.br"
echo -e "  Public: https://cau-public.migrai.com.br"
echo -e "  API:    https://cau-api.migrai.com.br"
echo -e "\nHealth Check:"
echo -e "  curl https://cau-api.migrai.com.br/health"
