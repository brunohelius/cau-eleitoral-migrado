# =============================================================================
# CAU Sistema Eleitoral - Terraform Outputs
# =============================================================================

# -----------------------------------------------------------------------------
# VPC Outputs
# -----------------------------------------------------------------------------

output "vpc_id" {
  description = "ID of the VPC"
  value       = aws_vpc.main.id
}

output "vpc_cidr" {
  description = "CIDR block of the VPC"
  value       = aws_vpc.main.cidr_block
}

output "public_subnet_ids" {
  description = "IDs of public subnets"
  value       = aws_subnet.public[*].id
}

output "private_subnet_ids" {
  description = "IDs of private subnets"
  value       = aws_subnet.private[*].id
}

output "database_subnet_ids" {
  description = "IDs of database subnets"
  value       = aws_subnet.database[*].id
}

output "nat_gateway_ips" {
  description = "Public IPs of NAT Gateways"
  value       = aws_nat_gateway.main[*].public_ip
}

# -----------------------------------------------------------------------------
# RDS Outputs
# -----------------------------------------------------------------------------

output "rds_endpoint" {
  description = "RDS instance endpoint"
  value       = aws_db_instance.main.endpoint
}

output "rds_address" {
  description = "RDS instance address (hostname)"
  value       = aws_db_instance.main.address
}

output "rds_port" {
  description = "RDS instance port"
  value       = aws_db_instance.main.port
}

output "rds_database_name" {
  description = "RDS database name"
  value       = aws_db_instance.main.db_name
}

output "rds_username" {
  description = "RDS master username"
  value       = aws_db_instance.main.username
  sensitive   = true
}

output "rds_secret_arn" {
  description = "ARN of the RDS credentials secret"
  value       = aws_secretsmanager_secret.rds_credentials.arn
}

# -----------------------------------------------------------------------------
# ECR Outputs
# -----------------------------------------------------------------------------

output "ecr_api_repository_url" {
  description = "URL of the API ECR repository"
  value       = aws_ecr_repository.api.repository_url
}

output "ecr_admin_repository_url" {
  description = "URL of the Admin ECR repository"
  value       = aws_ecr_repository.admin.repository_url
}

output "ecr_public_repository_url" {
  description = "URL of the Public ECR repository"
  value       = aws_ecr_repository.public.repository_url
}

output "ecr_repository_arns" {
  description = "ARNs of all ECR repositories"
  value = {
    api    = aws_ecr_repository.api.arn
    admin  = aws_ecr_repository.admin.arn
    public = aws_ecr_repository.public.arn
  }
}

# -----------------------------------------------------------------------------
# ECS Outputs
# -----------------------------------------------------------------------------

output "ecs_cluster_id" {
  description = "ID of the ECS cluster"
  value       = aws_ecs_cluster.main.id
}

output "ecs_cluster_name" {
  description = "Name of the ECS cluster"
  value       = aws_ecs_cluster.main.name
}

output "ecs_cluster_arn" {
  description = "ARN of the ECS cluster"
  value       = aws_ecs_cluster.main.arn
}

output "ecs_service_api_name" {
  description = "Name of the API ECS service"
  value       = aws_ecs_service.api.name
}

output "ecs_service_admin_name" {
  description = "Name of the Admin ECS service"
  value       = aws_ecs_service.admin.name
}

output "ecs_service_public_name" {
  description = "Name of the Public ECS service"
  value       = aws_ecs_service.public.name
}

# -----------------------------------------------------------------------------
# ALB Outputs
# -----------------------------------------------------------------------------

output "alb_dns_name" {
  description = "DNS name of the Application Load Balancer"
  value       = aws_lb.main.dns_name
}

output "alb_zone_id" {
  description = "Zone ID of the Application Load Balancer"
  value       = aws_lb.main.zone_id
}

output "alb_arn" {
  description = "ARN of the Application Load Balancer"
  value       = aws_lb.main.arn
}

output "alb_api_target_group_arn" {
  description = "ARN of the API target group"
  value       = aws_lb_target_group.api.arn
}

output "alb_admin_target_group_arn" {
  description = "ARN of the Admin target group"
  value       = aws_lb_target_group.admin.arn
}

output "alb_public_target_group_arn" {
  description = "ARN of the Public target group"
  value       = aws_lb_target_group.public.arn
}

# -----------------------------------------------------------------------------
# S3 Outputs
# -----------------------------------------------------------------------------

output "s3_documents_bucket" {
  description = "Name of the documents S3 bucket"
  value       = aws_s3_bucket.documents.id
}

output "s3_documents_bucket_arn" {
  description = "ARN of the documents S3 bucket"
  value       = aws_s3_bucket.documents.arn
}

output "s3_uploads_bucket" {
  description = "Name of the uploads S3 bucket"
  value       = aws_s3_bucket.uploads.id
}

output "s3_uploads_bucket_arn" {
  description = "ARN of the uploads S3 bucket"
  value       = aws_s3_bucket.uploads.arn
}

output "s3_backups_bucket" {
  description = "Name of the backups S3 bucket"
  value       = aws_s3_bucket.backups.id
}

output "s3_backups_bucket_arn" {
  description = "ARN of the backups S3 bucket"
  value       = aws_s3_bucket.backups.arn
}

# -----------------------------------------------------------------------------
# CloudFront Outputs
# -----------------------------------------------------------------------------

output "cloudfront_admin_distribution_id" {
  description = "ID of the Admin CloudFront distribution"
  value       = aws_cloudfront_distribution.admin.id
}

output "cloudfront_admin_domain_name" {
  description = "Domain name of the Admin CloudFront distribution"
  value       = aws_cloudfront_distribution.admin.domain_name
}

output "cloudfront_public_distribution_id" {
  description = "ID of the Public CloudFront distribution"
  value       = aws_cloudfront_distribution.public.id
}

output "cloudfront_public_domain_name" {
  description = "Domain name of the Public CloudFront distribution"
  value       = aws_cloudfront_distribution.public.domain_name
}

output "cloudfront_api_distribution_id" {
  description = "ID of the API CloudFront distribution"
  value       = aws_cloudfront_distribution.api.id
}

output "cloudfront_api_domain_name" {
  description = "Domain name of the API CloudFront distribution"
  value       = aws_cloudfront_distribution.api.domain_name
}

# -----------------------------------------------------------------------------
# Route53 Outputs
# -----------------------------------------------------------------------------

output "route53_zone_id" {
  description = "ID of the Route53 hosted zone"
  value       = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
}

output "route53_name_servers" {
  description = "Name servers for the Route53 hosted zone"
  value       = var.create_route53_zone ? aws_route53_zone.main[0].name_servers : []
}

# -----------------------------------------------------------------------------
# ACM Outputs
# -----------------------------------------------------------------------------

output "acm_certificate_arn" {
  description = "ARN of the ACM certificate (us-east-1 for CloudFront)"
  value       = local.certificate_arn
}

output "acm_certificate_domain" {
  description = "Domain name of the ACM certificate"
  value       = var.domain_name
}

# -----------------------------------------------------------------------------
# Secrets Manager Outputs
# -----------------------------------------------------------------------------

output "secrets_rds_arn" {
  description = "ARN of the RDS credentials secret"
  value       = aws_secretsmanager_secret.rds_credentials.arn
}

output "secrets_app_arn" {
  description = "ARN of the application secrets"
  value       = aws_secretsmanager_secret.app_secrets.arn
}

output "secrets_jwt_arn" {
  description = "ARN of the JWT secret"
  value       = aws_secretsmanager_secret.jwt_secret.arn
}

# -----------------------------------------------------------------------------
# IAM Outputs
# -----------------------------------------------------------------------------

output "ecs_task_execution_role_arn" {
  description = "ARN of the ECS task execution role"
  value       = aws_iam_role.ecs_task_execution.arn
}

output "ecs_task_role_arn" {
  description = "ARN of the ECS task role"
  value       = aws_iam_role.ecs_task.arn
}

# -----------------------------------------------------------------------------
# Application URLs
# -----------------------------------------------------------------------------

output "application_urls" {
  description = "Application URLs"
  value = {
    admin_url  = "https://${local.admin_domain}"
    public_url = "https://${local.public_domain}"
    api_url    = "https://${local.api_domain}"
  }
}

# -----------------------------------------------------------------------------
# Connection Strings (for reference)
# -----------------------------------------------------------------------------

output "database_connection_info" {
  description = "Database connection information (retrieve password from Secrets Manager)"
  value = {
    host     = aws_db_instance.main.address
    port     = aws_db_instance.main.port
    database = aws_db_instance.main.db_name
    username = aws_db_instance.main.username
    secret   = aws_secretsmanager_secret.rds_credentials.name
  }
  sensitive = true
}

# -----------------------------------------------------------------------------
# Deployment Information
# -----------------------------------------------------------------------------

output "deployment_info" {
  description = "Deployment information for CI/CD"
  value = {
    aws_region     = var.aws_region
    aws_account_id = data.aws_caller_identity.current.account_id
    ecs_cluster    = aws_ecs_cluster.main.name
    ecr_repositories = {
      api    = aws_ecr_repository.api.repository_url
      admin  = aws_ecr_repository.admin.repository_url
      public = aws_ecr_repository.public.repository_url
    }
    services = {
      api    = aws_ecs_service.api.name
      admin  = aws_ecs_service.admin.name
      public = aws_ecs_service.public.name
    }
  }
}
