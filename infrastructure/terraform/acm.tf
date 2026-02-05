# =============================================================================
# CAU Sistema Eleitoral - ACM Certificate Configuration
# =============================================================================

# -----------------------------------------------------------------------------
# Use existing ACM Certificate (if provided) or create new one
# -----------------------------------------------------------------------------

locals {
  # Full domain names
  admin_fqdn  = "${var.admin_subdomain}.${var.domain_name}"
  api_fqdn    = "${var.api_subdomain}.${var.domain_name}"
  public_fqdn = "${var.public_subdomain}.${var.domain_name}"

  # Use existing certificate if provided, otherwise use created one
  certificate_arn = var.existing_certificate_arn != "" ? var.existing_certificate_arn : aws_acm_certificate.main[0].arn
}

# Only create certificate if no existing one is provided
resource "aws_acm_certificate" "main" {
  count             = var.existing_certificate_arn == "" ? 1 : 0
  provider          = aws.us_east_1
  domain_name       = var.domain_name
  validation_method = "DNS"

  subject_alternative_names = [
    "*.${var.domain_name}"
  ]

  lifecycle {
    create_before_destroy = true
  }

  tags = {
    Name = "${local.name_prefix}-cloudfront-cert"
  }
}

# -----------------------------------------------------------------------------
# Certificate Validation Records (only if creating new certificate)
# -----------------------------------------------------------------------------

resource "aws_route53_record" "cert_validation" {
  for_each = var.existing_certificate_arn == "" ? {
    for dvo in aws_acm_certificate.main[0].domain_validation_options : dvo.domain_name => {
      name   = dvo.resource_record_name
      record = dvo.resource_record_value
      type   = dvo.resource_record_type
    }
  } : {}

  allow_overwrite = true
  name            = each.value.name
  records         = [each.value.record]
  ttl             = 60
  type            = each.value.type
  zone_id         = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
}

# -----------------------------------------------------------------------------
# Certificate Validation (only if creating new certificate)
# -----------------------------------------------------------------------------

resource "aws_acm_certificate_validation" "main" {
  count                   = var.existing_certificate_arn == "" ? 1 : 0
  provider                = aws.us_east_1
  certificate_arn         = aws_acm_certificate.main[0].arn
  validation_record_fqdns = [for record in aws_route53_record.cert_validation : record.fqdn]
}

# -----------------------------------------------------------------------------
# Certificate Expiration Alarm
# -----------------------------------------------------------------------------

resource "aws_cloudwatch_metric_alarm" "certificate_expiration" {
  provider            = aws.us_east_1
  alarm_name          = "${local.name_prefix}-certificate-expiration"
  comparison_operator = "LessThanThreshold"
  evaluation_periods  = 1
  metric_name         = "DaysToExpiry"
  namespace           = "AWS/CertificateManager"
  period              = 86400 # 1 day
  statistic           = "Minimum"
  threshold           = 30 # Alert 30 days before expiration
  alarm_description   = "SSL certificate will expire in less than 30 days"
  alarm_actions       = var.alarm_email != "" ? [aws_sns_topic.alarms_us_east_1[0].arn] : []
  ok_actions          = var.alarm_email != "" ? [aws_sns_topic.alarms_us_east_1[0].arn] : []

  dimensions = {
    CertificateArn = local.certificate_arn
  }

  tags = {
    Name = "${local.name_prefix}-cert-expiration-alarm"
  }
}

# SNS Topic in us-east-1 for CloudFront-related alarms
resource "aws_sns_topic" "alarms_us_east_1" {
  provider = aws.us_east_1
  count    = var.alarm_email != "" ? 1 : 0
  name     = "${local.name_prefix}-alarms-us-east-1"

  tags = {
    Name = "${local.name_prefix}-alarms-us-east-1"
  }
}

resource "aws_sns_topic_subscription" "alarms_email_us_east_1" {
  provider  = aws.us_east_1
  count     = var.alarm_email != "" ? 1 : 0
  topic_arn = aws_sns_topic.alarms_us_east_1[0].arn
  protocol  = "email"
  endpoint  = var.alarm_email
}
