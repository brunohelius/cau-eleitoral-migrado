# =============================================================================
# CAU Sistema Eleitoral - Route53 Configuration
# =============================================================================

# -----------------------------------------------------------------------------
# Route53 Hosted Zone
# -----------------------------------------------------------------------------

resource "aws_route53_zone" "main" {
  count = var.create_route53_zone ? 1 : 0
  name  = var.domain_name

  tags = {
    Name = "${local.name_prefix}-zone"
  }
}

# -----------------------------------------------------------------------------
# DNS Records - Admin (Main Domain)
# -----------------------------------------------------------------------------

resource "aws_route53_record" "admin" {
  zone_id = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
  name    = local.admin_domain
  type    = "A"

  alias {
    name                   = aws_cloudfront_distribution.admin.domain_name
    zone_id                = aws_cloudfront_distribution.admin.hosted_zone_id
    evaluate_target_health = false
  }
}

resource "aws_route53_record" "admin_ipv6" {
  zone_id = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
  name    = local.admin_domain
  type    = "AAAA"

  alias {
    name                   = aws_cloudfront_distribution.admin.domain_name
    zone_id                = aws_cloudfront_distribution.admin.hosted_zone_id
    evaluate_target_health = false
  }
}

# -----------------------------------------------------------------------------
# DNS Records - Public
# -----------------------------------------------------------------------------

resource "aws_route53_record" "public" {
  zone_id = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
  name    = local.public_domain
  type    = "A"

  alias {
    name                   = aws_cloudfront_distribution.public.domain_name
    zone_id                = aws_cloudfront_distribution.public.hosted_zone_id
    evaluate_target_health = false
  }
}

resource "aws_route53_record" "public_ipv6" {
  zone_id = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
  name    = local.public_domain
  type    = "AAAA"

  alias {
    name                   = aws_cloudfront_distribution.public.domain_name
    zone_id                = aws_cloudfront_distribution.public.hosted_zone_id
    evaluate_target_health = false
  }
}

# -----------------------------------------------------------------------------
# DNS Records - API
# -----------------------------------------------------------------------------

resource "aws_route53_record" "api" {
  zone_id = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
  name    = local.api_domain
  type    = "A"

  alias {
    name                   = aws_cloudfront_distribution.api.domain_name
    zone_id                = aws_cloudfront_distribution.api.hosted_zone_id
    evaluate_target_health = false
  }
}

resource "aws_route53_record" "api_ipv6" {
  zone_id = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
  name    = local.api_domain
  type    = "AAAA"

  alias {
    name                   = aws_cloudfront_distribution.api.domain_name
    zone_id                = aws_cloudfront_distribution.api.hosted_zone_id
    evaluate_target_health = false
  }
}

# -----------------------------------------------------------------------------
# DNS Records - Direct ALB Access (for internal/testing)
# -----------------------------------------------------------------------------

resource "aws_route53_record" "alb_direct" {
  zone_id = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
  name    = "alb.${var.domain_name}"
  type    = "A"

  alias {
    name                   = aws_lb.main.dns_name
    zone_id                = aws_lb.main.zone_id
    evaluate_target_health = true
  }
}

# -----------------------------------------------------------------------------
# Health Checks
# -----------------------------------------------------------------------------

resource "aws_route53_health_check" "api" {
  fqdn              = local.api_domain
  port              = 443
  type              = "HTTPS"
  resource_path     = "/actuator/health"
  failure_threshold = 3
  request_interval  = 30
  measure_latency   = true

  tags = {
    Name = "${local.name_prefix}-api-health-check"
  }
}

resource "aws_route53_health_check" "admin" {
  fqdn              = local.admin_domain
  port              = 443
  type              = "HTTPS"
  resource_path     = "/health"
  failure_threshold = 3
  request_interval  = 30
  measure_latency   = true

  tags = {
    Name = "${local.name_prefix}-admin-health-check"
  }
}

resource "aws_route53_health_check" "public" {
  fqdn              = local.public_domain
  port              = 443
  type              = "HTTPS"
  resource_path     = "/health"
  failure_threshold = 3
  request_interval  = 30
  measure_latency   = true

  tags = {
    Name = "${local.name_prefix}-public-health-check"
  }
}

# -----------------------------------------------------------------------------
# CloudWatch Alarms for Health Checks
# -----------------------------------------------------------------------------

resource "aws_cloudwatch_metric_alarm" "health_check_api" {
  alarm_name          = "${local.name_prefix}-api-health-check"
  comparison_operator = "LessThanThreshold"
  evaluation_periods  = 2
  metric_name         = "HealthCheckStatus"
  namespace           = "AWS/Route53"
  period              = 60
  statistic           = "Minimum"
  threshold           = 1
  alarm_description   = "API health check is failing"
  alarm_actions       = var.alarm_email != "" ? [aws_sns_topic.alarms[0].arn] : []
  ok_actions          = var.alarm_email != "" ? [aws_sns_topic.alarms[0].arn] : []

  dimensions = {
    HealthCheckId = aws_route53_health_check.api.id
  }

  tags = {
    Name = "${local.name_prefix}-api-health-alarm"
  }
}

resource "aws_cloudwatch_metric_alarm" "health_check_admin" {
  alarm_name          = "${local.name_prefix}-admin-health-check"
  comparison_operator = "LessThanThreshold"
  evaluation_periods  = 2
  metric_name         = "HealthCheckStatus"
  namespace           = "AWS/Route53"
  period              = 60
  statistic           = "Minimum"
  threshold           = 1
  alarm_description   = "Admin health check is failing"
  alarm_actions       = var.alarm_email != "" ? [aws_sns_topic.alarms[0].arn] : []
  ok_actions          = var.alarm_email != "" ? [aws_sns_topic.alarms[0].arn] : []

  dimensions = {
    HealthCheckId = aws_route53_health_check.admin.id
  }

  tags = {
    Name = "${local.name_prefix}-admin-health-alarm"
  }
}

# -----------------------------------------------------------------------------
# MX Records (if email is needed)
# -----------------------------------------------------------------------------

# Uncomment if you need email routing
# resource "aws_route53_record" "mx" {
#   zone_id = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
#   name    = var.domain_name
#   type    = "MX"
#   ttl     = 3600
#   records = [
#     "10 mail.${var.domain_name}"
#   ]
# }

# -----------------------------------------------------------------------------
# SPF, DKIM, DMARC Records (for email security)
# -----------------------------------------------------------------------------

# Uncomment if you need email authentication
# resource "aws_route53_record" "spf" {
#   zone_id = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
#   name    = var.domain_name
#   type    = "TXT"
#   ttl     = 3600
#   records = ["v=spf1 include:amazonses.com ~all"]
# }

# resource "aws_route53_record" "dmarc" {
#   zone_id = var.create_route53_zone ? aws_route53_zone.main[0].zone_id : var.route53_zone_id
#   name    = "_dmarc.${var.domain_name}"
#   type    = "TXT"
#   ttl     = 3600
#   records = ["v=DMARC1; p=quarantine; rua=mailto:dmarc@${var.domain_name}"]
# }
