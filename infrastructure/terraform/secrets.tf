# =============================================================================
# CAU Sistema Eleitoral - Secrets Manager Configuration
# =============================================================================

locals {
  enable_rds_secret_rotation = var.environment == "production" && var.rds_rotation_lambda_package != ""
}

# -----------------------------------------------------------------------------
# KMS Key for Secrets
# -----------------------------------------------------------------------------

resource "aws_kms_key" "secrets" {
  description             = "KMS key for Secrets Manager - ${local.name_prefix}"
  deletion_window_in_days = 30
  enable_key_rotation     = true

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Sid    = "Enable IAM User Permissions"
        Effect = "Allow"
        Principal = {
          AWS = "arn:aws:iam::${data.aws_caller_identity.current.account_id}:root"
        }
        Action   = "kms:*"
        Resource = "*"
      },
      {
        Sid    = "Allow Secrets Manager to use the key"
        Effect = "Allow"
        Principal = {
          Service = "secretsmanager.amazonaws.com"
        }
        Action = [
          "kms:Encrypt",
          "kms:Decrypt",
          "kms:ReEncrypt*",
          "kms:GenerateDataKey*",
          "kms:DescribeKey"
        ]
        Resource = "*"
      },
      {
        Sid    = "Allow ECS Tasks to decrypt"
        Effect = "Allow"
        Principal = {
          AWS = aws_iam_role.ecs_task_execution.arn
        }
        Action = [
          "kms:Decrypt",
          "kms:DescribeKey"
        ]
        Resource = "*"
      }
    ]
  })

  tags = {
    Name = "${local.name_prefix}-secrets-key"
  }
}

resource "aws_kms_alias" "secrets" {
  name          = "alias/${local.name_prefix}-secrets"
  target_key_id = aws_kms_key.secrets.key_id
}

# -----------------------------------------------------------------------------
# RDS Credentials Secret
# -----------------------------------------------------------------------------

resource "aws_secretsmanager_secret" "rds_credentials" {
  name        = "${local.name_prefix}/rds/credentials"
  description = "RDS database credentials for ${local.name_prefix}"
  kms_key_id  = aws_kms_key.secrets.arn

  recovery_window_in_days = 30

  tags = {
    Name = "${local.name_prefix}-rds-credentials"
  }
}

resource "aws_secretsmanager_secret_version" "rds_credentials" {
  secret_id = aws_secretsmanager_secret.rds_credentials.id

  secret_string = jsonencode({
    username = var.db_username
    password = random_password.rds_password.result
    host     = aws_db_instance.main.address
    port     = aws_db_instance.main.port
    database = aws_db_instance.main.db_name
    engine   = "postgres"
    url      = "jdbc:postgresql://${aws_db_instance.main.address}:${aws_db_instance.main.port}/${aws_db_instance.main.db_name}"
  })

  lifecycle {
    ignore_changes = [secret_string]
  }
}

# -----------------------------------------------------------------------------
# .NET Connection String Secret
# -----------------------------------------------------------------------------

resource "aws_secretsmanager_secret" "connection_string" {
  name        = "${local.name_prefix}/db/connection-string"
  description = ".NET connection string for ${local.name_prefix}"
  kms_key_id  = aws_kms_key.secrets.arn

  recovery_window_in_days = 30

  tags = {
    Name = "${local.name_prefix}-connection-string"
  }
}

resource "aws_secretsmanager_secret_version" "connection_string" {
  secret_id     = aws_secretsmanager_secret.connection_string.id
  secret_string = "Host=${aws_db_instance.main.address};Port=${aws_db_instance.main.port};Database=${aws_db_instance.main.db_name};Username=${var.db_username};Password=${random_password.rds_password.result}"

  lifecycle {
    ignore_changes = [secret_string]
  }
}

# -----------------------------------------------------------------------------
# JWT Secret
# -----------------------------------------------------------------------------

resource "random_password" "jwt_secret" {
  length  = 64
  special = false
}

resource "aws_secretsmanager_secret" "jwt_secret" {
  name        = "${local.name_prefix}/jwt/secret"
  description = "JWT signing secret for ${local.name_prefix}"
  kms_key_id  = aws_kms_key.secrets.arn

  recovery_window_in_days = 30

  tags = {
    Name = "${local.name_prefix}-jwt-secret"
  }
}

resource "aws_secretsmanager_secret_version" "jwt_secret" {
  secret_id     = aws_secretsmanager_secret.jwt_secret.id
  secret_string = random_password.jwt_secret.result

  lifecycle {
    ignore_changes = [secret_string]
  }
}

# -----------------------------------------------------------------------------
# Application Secrets
# -----------------------------------------------------------------------------

resource "aws_secretsmanager_secret" "app_secrets" {
  name        = "${local.name_prefix}/app/secrets"
  description = "Application secrets for ${local.name_prefix}"
  kms_key_id  = aws_kms_key.secrets.arn

  recovery_window_in_days = 30

  tags = {
    Name = "${local.name_prefix}-app-secrets"
  }
}

resource "aws_secretsmanager_secret_version" "app_secrets" {
  secret_id = aws_secretsmanager_secret.app_secrets.id

  secret_string = jsonencode({
    # Application-specific secrets
    encryption_key       = random_password.app_encryption_key.result
    api_key              = random_password.api_key.result
    webhook_secret       = random_password.webhook_secret.result
    cloudfront_header    = random_password.cloudfront_header.result

    # AWS Configuration
    aws_region           = var.aws_region
    s3_documents_bucket  = aws_s3_bucket.documents.id
    s3_uploads_bucket    = aws_s3_bucket.uploads.id
    s3_backups_bucket    = aws_s3_bucket.backups.id

    # Domain Configuration
    domain               = var.domain_name
    api_url              = "https://${local.api_domain}"
    admin_url            = "https://${local.admin_domain}"
    public_url           = "https://${local.public_domain}"
  })

  lifecycle {
    ignore_changes = [secret_string]
  }
}

resource "random_password" "app_encryption_key" {
  length  = 32
  special = false
}

resource "random_password" "api_key" {
  length  = 48
  special = false
}

resource "random_password" "webhook_secret" {
  length  = 32
  special = false
}

# -----------------------------------------------------------------------------
# SMTP Credentials (if needed)
# -----------------------------------------------------------------------------

resource "aws_secretsmanager_secret" "smtp_credentials" {
  name        = "${local.name_prefix}/smtp/credentials"
  description = "SMTP credentials for ${local.name_prefix}"
  kms_key_id  = aws_kms_key.secrets.arn

  recovery_window_in_days = 30

  tags = {
    Name = "${local.name_prefix}-smtp-credentials"
  }
}

resource "aws_secretsmanager_secret_version" "smtp_credentials" {
  secret_id = aws_secretsmanager_secret.smtp_credentials.id

  secret_string = jsonencode({
    host     = "email-smtp.${var.aws_region}.amazonaws.com"
    port     = 587
    username = "" # To be filled manually with SES SMTP credentials
    password = "" # To be filled manually with SES SMTP credentials
    from     = "noreply@${var.domain_name}"
  })

  lifecycle {
    ignore_changes = [secret_string]
  }
}

# -----------------------------------------------------------------------------
# Secrets Rotation (Optional)
# -----------------------------------------------------------------------------

# Lambda function for RDS password rotation
resource "aws_lambda_function" "rds_rotation" {
  count         = local.enable_rds_secret_rotation ? 1 : 0
  function_name = "${local.name_prefix}-rds-secret-rotation"
  description   = "Rotates RDS password in Secrets Manager"

  # Use AWS-provided rotation function
  runtime       = "python3.11"
  handler       = "lambda_function.lambda_handler"
  timeout       = 30
  memory_size   = 128

  role = aws_iam_role.secrets_rotation[0].arn

  # Deploy only when a real lambda package is provided
  filename         = var.rds_rotation_lambda_package
  source_code_hash = filebase64sha256(var.rds_rotation_lambda_package)

  vpc_config {
    subnet_ids         = aws_subnet.private[*].id
    security_group_ids = [aws_security_group.secrets_rotation[0].id]
  }

  environment {
    variables = {
      SECRETS_MANAGER_ENDPOINT = "https://secretsmanager.${var.aws_region}.amazonaws.com"
    }
  }

  tags = {
    Name = "${local.name_prefix}-rds-rotation"
  }

  depends_on = [aws_iam_role_policy.secrets_rotation]
}

resource "aws_security_group" "secrets_rotation" {
  count       = local.enable_rds_secret_rotation ? 1 : 0
  name        = "${local.name_prefix}-secrets-rotation-sg"
  description = "Security group for secrets rotation lambda"
  vpc_id      = aws_vpc.main.id

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "${local.name_prefix}-secrets-rotation-sg"
  }
}

resource "aws_iam_role" "secrets_rotation" {
  count = local.enable_rds_secret_rotation ? 1 : 0
  name  = "${local.name_prefix}-secrets-rotation-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "lambda.amazonaws.com"
        }
      }
    ]
  })

  tags = {
    Name = "${local.name_prefix}-secrets-rotation-role"
  }
}

resource "aws_iam_role_policy" "secrets_rotation" {
  count = local.enable_rds_secret_rotation ? 1 : 0
  name  = "${local.name_prefix}-secrets-rotation-policy"
  role  = aws_iam_role.secrets_rotation[0].id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "secretsmanager:GetSecretValue",
          "secretsmanager:DescribeSecret",
          "secretsmanager:PutSecretValue",
          "secretsmanager:UpdateSecretVersionStage"
        ]
        Resource = aws_secretsmanager_secret.rds_credentials.arn
      },
      {
        Effect = "Allow"
        Action = [
          "kms:Decrypt",
          "kms:GenerateDataKey"
        ]
        Resource = aws_kms_key.secrets.arn
      },
      {
        Effect = "Allow"
        Action = [
          "logs:CreateLogGroup",
          "logs:CreateLogStream",
          "logs:PutLogEvents"
        ]
        Resource = "arn:aws:logs:*:*:*"
      },
      {
        Effect = "Allow"
        Action = [
          "ec2:CreateNetworkInterface",
          "ec2:DeleteNetworkInterface",
          "ec2:DescribeNetworkInterfaces"
        ]
        Resource = "*"
      }
    ]
  })
}

# Secrets rotation schedule (disabled by default)
# resource "aws_secretsmanager_secret_rotation" "rds_credentials" {
#   count               = local.enable_rds_secret_rotation ? 1 : 0
#   secret_id           = aws_secretsmanager_secret.rds_credentials.id
#   rotation_lambda_arn = aws_lambda_function.rds_rotation[0].arn

#   rotation_rules {
#     automatically_after_days = 30
#   }
# }
