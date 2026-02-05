# CAU Sistema Eleitoral - Terraform Infrastructure

This directory contains the Terraform configuration for deploying the CAU Sistema Eleitoral application to AWS.

## Architecture Overview

```
                                    ┌─────────────────────────────────────────────────────────────────┐
                                    │                         CloudFront                               │
                                    │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐              │
                                    │  │    Admin    │  │   Public    │  │     API     │              │
                                    │  │ Distribution│  │ Distribution│  │ Distribution│              │
                                    │  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘              │
                                    └─────────┼────────────────┼────────────────┼─────────────────────┘
                                              │                │                │
                                              ▼                ▼                ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                        Application Load Balancer                                     │
│                                              (HTTPS/443)                                             │
└─────────────────────────────────────────────────────────────────────────────────────────────────────┘
                                              │
                    ┌─────────────────────────┼─────────────────────────┐
                    ▼                         ▼                         ▼
            ┌───────────────┐         ┌───────────────┐         ┌───────────────┐
            │  Admin Target │         │ Public Target │         │   API Target  │
            │     Group     │         │     Group     │         │     Group     │
            └───────┬───────┘         └───────┬───────┘         └───────┬───────┘
                    │                         │                         │
┌───────────────────┼─────────────────────────┼─────────────────────────┼──────────────────────────────┐
│                   ▼                         ▼                         ▼              ECS Cluster     │
│           ┌───────────────┐         ┌───────────────┐         ┌───────────────┐                      │
│           │ Admin Service │         │Public Service │         │  API Service  │                      │
│           │   (Fargate)   │         │   (Fargate)   │         │   (Fargate)   │                      │
│           └───────────────┘         └───────────────┘         └───────┬───────┘                      │
│                                                                       │                              │
└───────────────────────────────────────────────────────────────────────┼──────────────────────────────┘
                                                                        │
                                                                        ▼
                                                              ┌───────────────────┐
                                                              │   RDS PostgreSQL  │
                                                              │    (Multi-AZ)     │
                                                              └───────────────────┘
```

## Prerequisites

1. **AWS CLI** configured with appropriate credentials
2. **Terraform** >= 1.6.0
3. **Domain** registered and accessible
4. AWS Account ID: `801232946361`

## Quick Start

### 1. Initialize Terraform

```bash
cd infrastructure/terraform
terraform init
```

### 2. Create Variables File

```bash
cp terraform.tfvars.example terraform.tfvars
# Edit terraform.tfvars with your values
```

### 3. Plan Infrastructure

```bash
terraform plan -out=tfplan
```

### 4. Apply Infrastructure

```bash
terraform apply tfplan
```

## File Structure

```
infrastructure/terraform/
├── main.tf              # Main configuration and providers
├── variables.tf         # Input variables
├── outputs.tf           # Output values
├── vpc.tf               # VPC, subnets, security groups
├── rds.tf               # PostgreSQL RDS instance
├── ecr.tf               # ECR repositories
├── ecs.tf               # ECS cluster and services
├── alb.tf               # Application Load Balancer
├── s3.tf                # S3 buckets
├── cloudfront.tf        # CloudFront distributions
├── route53.tf           # DNS records
├── acm.tf               # SSL/TLS certificates
├── secrets.tf           # Secrets Manager
├── iam.tf               # IAM roles and policies
├── terraform.tfvars.example  # Example variables
└── README.md            # This file
```

## Resources Created

### Networking
- VPC with public, private, and database subnets
- Internet Gateway
- NAT Gateways (configurable single or multi-AZ)
- VPC Endpoints for AWS services
- Security Groups for ALB, ECS, and RDS

### Compute
- ECS Cluster with Fargate
- Three ECS Services: API, Admin, Public
- Auto Scaling for all services
- Service Discovery (Cloud Map)

### Database
- RDS PostgreSQL 16 (Multi-AZ)
- Read Replica (production only)
- Automated backups
- Performance Insights enabled

### Storage
- S3 Buckets: documents, uploads, backups
- Lifecycle policies for cost optimization
- Server-side encryption with KMS

### CDN & DNS
- CloudFront distributions for all services
- Route53 hosted zone and records
- ACM certificates with auto-renewal
- WAF protection

### Security
- Secrets Manager for credentials
- KMS keys for encryption
- IAM roles with least privilege
- VPC Flow Logs

## Environments

### Development
```hcl
environment        = "development"
single_nat_gateway = true
db_multi_az        = false
enable_spot_instances = true
```

### Staging
```hcl
environment        = "staging"
single_nat_gateway = true
db_multi_az        = false
```

### Production
```hcl
environment        = "production"
single_nat_gateway = false
db_multi_az        = true
db_deletion_protection = true
```

## Deployment Process

### Initial Deployment

1. **Certificate Validation**
   - After first apply, DNS validation records are created
   - Wait for certificate validation (can take up to 30 minutes)

2. **Database Setup**
   - RDS is created with initial credentials in Secrets Manager
   - Run database migrations after deployment

3. **Push Docker Images**
   ```bash
   # Login to ECR
   aws ecr get-login-password --region us-east-1 | \
     docker login --username AWS --password-stdin 801232946361.dkr.ecr.us-east-1.amazonaws.com

   # Build and push images
   docker build -t cau-eleitoral/api ./api
   docker tag cau-eleitoral/api:latest 801232946361.dkr.ecr.us-east-1.amazonaws.com/cau-eleitoral/api:latest
   docker push 801232946361.dkr.ecr.us-east-1.amazonaws.com/cau-eleitoral/api:latest
   ```

4. **Update ECS Services**
   ```bash
   aws ecs update-service --cluster cau-eleitoral-cluster --service cau-eleitoral-api --force-new-deployment
   ```

### Updating Infrastructure

```bash
# Pull latest changes
git pull

# Plan changes
terraform plan -out=tfplan

# Review and apply
terraform apply tfplan
```

## Accessing Resources

### ECS Exec (Container Shell)

```bash
aws ecs execute-command \
  --cluster cau-eleitoral-cluster \
  --task <task-id> \
  --container api \
  --interactive \
  --command "/bin/sh"
```

### Database Connection

```bash
# Get credentials from Secrets Manager
aws secretsmanager get-secret-value \
  --secret-id cau-eleitoral/rds/credentials \
  --query SecretString --output text | jq

# Connect via bastion or ECS Exec
psql -h <rds-endpoint> -U cau_admin -d cau_eleitoral
```

### CloudWatch Logs

```bash
# API logs
aws logs tail /aws/ecs/cau-eleitoral/api --follow

# All logs
aws logs tail /aws/ecs/cau-eleitoral --follow
```

## Monitoring

### CloudWatch Dashboards
- ECS Service metrics
- RDS Performance Insights
- ALB request metrics
- CloudFront cache statistics

### Alarms
- RDS CPU, Memory, Storage
- ECS Service health
- ALB 5XX errors
- Certificate expiration

## Cost Optimization

### Development Environment
- Use single NAT Gateway
- Disable Multi-AZ for RDS
- Enable Fargate Spot
- Reduce ECS task counts

### Production Environment
- Reserve RDS instances (1 or 3 year)
- Use CloudFront for caching
- Implement S3 lifecycle policies
- Monitor with Cost Explorer

## Troubleshooting

### Certificate Validation Stuck
```bash
# Check validation status
aws acm describe-certificate --certificate-arn <arn> --query 'Certificate.DomainValidationOptions'
```

### ECS Service Not Starting
```bash
# Check service events
aws ecs describe-services --cluster cau-eleitoral-cluster --services cau-eleitoral-api

# Check task failures
aws ecs describe-tasks --cluster cau-eleitoral-cluster --tasks <task-arn>
```

### Database Connection Issues
```bash
# Check security groups
aws ec2 describe-security-groups --group-ids <sg-id>

# Test connectivity from ECS task
aws ecs execute-command --cluster cau-eleitoral-cluster --task <task-id> --container api --command "nc -zv <rds-endpoint> 5432"
```

## Backup and Recovery

### Manual Database Snapshot
```bash
aws rds create-db-snapshot \
  --db-instance-identifier cau-eleitoral-db \
  --db-snapshot-identifier manual-backup-$(date +%Y%m%d)
```

### Restore from Snapshot
```bash
aws rds restore-db-instance-from-db-snapshot \
  --db-instance-identifier cau-eleitoral-db-restored \
  --db-snapshot-identifier <snapshot-id>
```

## Security Considerations

1. **Never commit `terraform.tfvars`** to version control
2. **Enable MFA** for AWS console access
3. **Rotate secrets** regularly via Secrets Manager
4. **Review IAM policies** for least privilege
5. **Monitor CloudTrail** for API activity

## Support

For issues or questions:
- Check CloudWatch Logs
- Review ECS service events
- Contact DevOps team

## License

Proprietary - CAU (Conselho de Arquitetura e Urbanismo)
