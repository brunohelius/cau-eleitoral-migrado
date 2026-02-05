# =============================================================================
# CAU Sistema Eleitoral - Main Terraform Configuration
# =============================================================================
# AWS Account: 801232946361
# Region: us-east-1
# Domain: cau-eleitoral.migrai.com.br
# =============================================================================

terraform {
  required_version = ">= 1.5.0"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
    random = {
      source  = "hashicorp/random"
      version = "~> 3.6"
    }
  }

  # Backend configuration for state management
  # Uncomment and configure for production use
  # backend "s3" {
  #   bucket         = "cau-eleitoral-terraform-state"
  #   key            = "infrastructure/terraform.tfstate"
  #   region         = "us-east-1"
  #   encrypt        = true
  #   dynamodb_table = "cau-eleitoral-terraform-locks"
  # }
}

# =============================================================================
# AWS Provider Configuration
# =============================================================================

provider "aws" {
  region = var.aws_region

  default_tags {
    tags = {
      Project     = "CAU-Sistema-Eleitoral"
      Environment = var.environment
      ManagedBy   = "Terraform"
      Owner       = "CAU"
      CostCenter  = "Eleitoral"
    }
  }
}

# Provider for ACM certificates (must be in us-east-1 for CloudFront)
provider "aws" {
  alias  = "us_east_1"
  region = "us-east-1"

  default_tags {
    tags = {
      Project     = "CAU-Sistema-Eleitoral"
      Environment = var.environment
      ManagedBy   = "Terraform"
      Owner       = "CAU"
      CostCenter  = "Eleitoral"
    }
  }
}

# =============================================================================
# Data Sources
# =============================================================================

data "aws_caller_identity" "current" {}

data "aws_region" "current" {}

data "aws_availability_zones" "available" {
  state = "available"
}

# =============================================================================
# Local Values
# =============================================================================

locals {
  name_prefix = "cau-eleitoral"

  common_tags = {
    Project     = "CAU-Sistema-Eleitoral"
    Environment = var.environment
    ManagedBy   = "Terraform"
  }

  # Availability zones for the region
  azs = slice(data.aws_availability_zones.available.names, 0, 3)

  # Domain configurations
  domain_name     = var.domain_name
  api_domain      = "${var.api_subdomain}.${var.domain_name}"
  admin_domain    = "${var.admin_subdomain}.${var.domain_name}"
  public_domain   = "${var.public_subdomain}.${var.domain_name}"

  # Container ports
  api_port    = 8080
  admin_port  = 80
  public_port = 80
}
