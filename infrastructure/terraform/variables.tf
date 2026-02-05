# =============================================================================
# CAU Sistema Eleitoral - Input Variables
# =============================================================================

# -----------------------------------------------------------------------------
# General Configuration
# -----------------------------------------------------------------------------

variable "aws_region" {
  description = "AWS region for resources"
  type        = string
  default     = "us-east-1"
}

variable "environment" {
  description = "Environment name (development, staging, production)"
  type        = string
  default     = "production"

  validation {
    condition     = contains(["development", "staging", "production"], var.environment)
    error_message = "Environment must be one of: development, staging, production."
  }
}

variable "project_name" {
  description = "Project name for resource naming"
  type        = string
  default     = "cau-eleitoral"
}

# -----------------------------------------------------------------------------
# Domain Configuration
# -----------------------------------------------------------------------------

variable "domain_name" {
  description = "Base domain name (e.g., migrai.com.br)"
  type        = string
  default     = "migrai.com.br"
}

variable "admin_subdomain" {
  description = "Subdomain for admin app"
  type        = string
  default     = "cau-admin"
}

variable "api_subdomain" {
  description = "Subdomain for API"
  type        = string
  default     = "cau-api"
}

variable "public_subdomain" {
  description = "Subdomain for public app"
  type        = string
  default     = "cau-public"
}

variable "existing_certificate_arn" {
  description = "ARN of existing ACM certificate to use (if empty, creates new)"
  type        = string
  default     = ""
}

variable "route53_zone_id" {
  description = "Route53 hosted zone ID for the domain"
  type        = string
  default     = ""
}

variable "create_route53_zone" {
  description = "Whether to create a new Route53 hosted zone"
  type        = bool
  default     = true
}

# -----------------------------------------------------------------------------
# VPC Configuration
# -----------------------------------------------------------------------------

variable "vpc_cidr" {
  description = "CIDR block for the VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "enable_nat_gateway" {
  description = "Enable NAT Gateway for private subnets"
  type        = bool
  default     = true
}

variable "single_nat_gateway" {
  description = "Use a single NAT Gateway (cost optimization for non-prod)"
  type        = bool
  default     = false
}

# -----------------------------------------------------------------------------
# RDS Configuration
# -----------------------------------------------------------------------------

variable "db_instance_class" {
  description = "RDS instance class"
  type        = string
  default     = "db.t3.medium"
}

variable "db_allocated_storage" {
  description = "Allocated storage for RDS in GB"
  type        = number
  default     = 100
}

variable "db_max_allocated_storage" {
  description = "Maximum allocated storage for RDS autoscaling in GB"
  type        = number
  default     = 500
}

variable "db_name" {
  description = "Database name"
  type        = string
  default     = "cau_eleitoral"
}

variable "db_username" {
  description = "Database master username"
  type        = string
  default     = "cau_admin"
  sensitive   = true
}

variable "db_multi_az" {
  description = "Enable Multi-AZ deployment for RDS"
  type        = bool
  default     = true
}

variable "db_backup_retention_period" {
  description = "Number of days to retain RDS backups"
  type        = number
  default     = 30
}

variable "db_deletion_protection" {
  description = "Enable deletion protection for RDS"
  type        = bool
  default     = true
}

variable "db_skip_final_snapshot" {
  description = "Skip final snapshot when destroying RDS"
  type        = bool
  default     = false
}

# -----------------------------------------------------------------------------
# ECS Configuration
# -----------------------------------------------------------------------------

variable "ecs_api_cpu" {
  description = "CPU units for API service (1024 = 1 vCPU)"
  type        = number
  default     = 512
}

variable "ecs_api_memory" {
  description = "Memory for API service in MB"
  type        = number
  default     = 1024
}

variable "ecs_api_desired_count" {
  description = "Desired number of API tasks"
  type        = number
  default     = 2
}

variable "ecs_admin_cpu" {
  description = "CPU units for Admin service"
  type        = number
  default     = 256
}

variable "ecs_admin_memory" {
  description = "Memory for Admin service in MB"
  type        = number
  default     = 512
}

variable "ecs_admin_desired_count" {
  description = "Desired number of Admin tasks"
  type        = number
  default     = 2
}

variable "ecs_public_cpu" {
  description = "CPU units for Public service"
  type        = number
  default     = 256
}

variable "ecs_public_memory" {
  description = "Memory for Public service in MB"
  type        = number
  default     = 512
}

variable "ecs_public_desired_count" {
  description = "Desired number of Public tasks"
  type        = number
  default     = 2
}

variable "enable_ecs_exec" {
  description = "Enable ECS Exec for debugging"
  type        = bool
  default     = true
}

# -----------------------------------------------------------------------------
# Auto Scaling Configuration
# -----------------------------------------------------------------------------

variable "autoscaling_min_capacity" {
  description = "Minimum number of tasks for auto scaling"
  type        = number
  default     = 2
}

variable "autoscaling_max_capacity" {
  description = "Maximum number of tasks for auto scaling"
  type        = number
  default     = 10
}

variable "autoscaling_cpu_target" {
  description = "Target CPU utilization for auto scaling"
  type        = number
  default     = 70
}

variable "autoscaling_memory_target" {
  description = "Target memory utilization for auto scaling"
  type        = number
  default     = 80
}

# -----------------------------------------------------------------------------
# S3 Configuration
# -----------------------------------------------------------------------------

variable "s3_versioning_enabled" {
  description = "Enable versioning for S3 buckets"
  type        = bool
  default     = true
}

variable "s3_lifecycle_glacier_days" {
  description = "Days before transitioning to Glacier"
  type        = number
  default     = 90
}

variable "s3_lifecycle_expiration_days" {
  description = "Days before object expiration (0 = never)"
  type        = number
  default     = 0
}

# -----------------------------------------------------------------------------
# CloudFront Configuration
# -----------------------------------------------------------------------------

variable "cloudfront_price_class" {
  description = "CloudFront price class"
  type        = string
  default     = "PriceClass_100"

  validation {
    condition     = contains(["PriceClass_100", "PriceClass_200", "PriceClass_All"], var.cloudfront_price_class)
    error_message = "Price class must be one of: PriceClass_100, PriceClass_200, PriceClass_All."
  }
}

variable "cloudfront_min_ttl" {
  description = "Minimum TTL for CloudFront caching"
  type        = number
  default     = 0
}

variable "cloudfront_default_ttl" {
  description = "Default TTL for CloudFront caching"
  type        = number
  default     = 3600
}

variable "cloudfront_max_ttl" {
  description = "Maximum TTL for CloudFront caching"
  type        = number
  default     = 86400
}

# -----------------------------------------------------------------------------
# Monitoring Configuration
# -----------------------------------------------------------------------------

variable "enable_container_insights" {
  description = "Enable CloudWatch Container Insights for ECS"
  type        = bool
  default     = true
}

variable "log_retention_days" {
  description = "CloudWatch Logs retention in days"
  type        = number
  default     = 30
}

variable "alarm_email" {
  description = "Email address for CloudWatch alarms"
  type        = string
  default     = ""
}

# -----------------------------------------------------------------------------
# Security Configuration
# -----------------------------------------------------------------------------

variable "allowed_cidr_blocks" {
  description = "CIDR blocks allowed to access the application"
  type        = list(string)
  default     = ["0.0.0.0/0"]
}

variable "enable_waf" {
  description = "Enable AWS WAF for ALB"
  type        = bool
  default     = true
}

# -----------------------------------------------------------------------------
# Backup Configuration
# -----------------------------------------------------------------------------

variable "enable_aws_backup" {
  description = "Enable AWS Backup for automated backups"
  type        = bool
  default     = true
}

variable "backup_schedule" {
  description = "Cron expression for backup schedule"
  type        = string
  default     = "cron(0 5 ? * * *)" # Daily at 5 AM UTC
}

variable "backup_retention_days" {
  description = "Number of days to retain backups"
  type        = number
  default     = 35
}

# -----------------------------------------------------------------------------
# Cost Optimization
# -----------------------------------------------------------------------------

variable "enable_spot_instances" {
  description = "Enable Fargate Spot for cost savings (non-critical workloads)"
  type        = bool
  default     = false
}

variable "spot_weight" {
  description = "Weight for Fargate Spot capacity (0-100)"
  type        = number
  default     = 50
}
