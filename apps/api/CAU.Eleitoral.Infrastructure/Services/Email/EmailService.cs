using MailKit.Net.Smtp;
using MailKit.Security;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.Logging;
using MimeKit;

namespace CAU.Eleitoral.Infrastructure.Services.Email;

public interface IEmailService
{
    Task SendAsync(string to, string subject, string htmlBody, CancellationToken ct = default);
    Task SendAsync(IEnumerable<string> to, string subject, string htmlBody, CancellationToken ct = default);
}

public class EmailService : IEmailService
{
    private readonly IConfiguration _config;
    private readonly ILogger<EmailService> _logger;
    private readonly string _smtpHost;
    private readonly int _smtpPort;
    private readonly string _smtpUser;
    private readonly string _smtpPassword;
    private readonly string _fromEmail;
    private readonly string _fromName;
    private readonly bool _enabled;

    public EmailService(IConfiguration config, ILogger<EmailService> logger)
    {
        _config = config;
        _logger = logger;
        _smtpHost = config["Email:SmtpHost"] ?? "";
        _smtpPort = int.TryParse(config["Email:SmtpPort"], out var port) ? port : 587;
        _smtpUser = config["Email:SmtpUser"] ?? "";
        _smtpPassword = config["Email:SmtpPassword"] ?? "";
        _fromEmail = config["Email:FromEmail"] ?? "noreply@cau.org.br";
        _fromName = config["Email:FromName"] ?? "CAU Sistema Eleitoral";

        _enabled = !string.IsNullOrEmpty(_smtpHost)
            && _smtpHost != "smtp.example.com"
            && !string.IsNullOrEmpty(_smtpUser)
            && !string.IsNullOrEmpty(_smtpPassword);

        if (!_enabled)
        {
            _logger.LogWarning("Email service disabled: SMTP credentials not configured. " +
                "Set Email:SmtpHost, Email:SmtpUser, and Email:SmtpPassword to enable.");
        }
    }

    public async Task SendAsync(string to, string subject, string htmlBody, CancellationToken ct = default)
    {
        await SendAsync(new[] { to }, subject, htmlBody, ct);
    }

    public async Task SendAsync(IEnumerable<string> to, string subject, string htmlBody, CancellationToken ct = default)
    {
        if (!_enabled)
        {
            _logger.LogInformation("Email not sent (disabled): Subject='{Subject}', To={To}", subject, string.Join(", ", to));
            return;
        }

        var message = new MimeMessage();
        message.From.Add(new MailboxAddress(_fromName, _fromEmail));
        foreach (var addr in to)
            message.To.Add(MailboxAddress.Parse(addr));
        message.Subject = subject;

        var bodyBuilder = new BodyBuilder { HtmlBody = htmlBody };
        message.Body = bodyBuilder.ToMessageBody();

        try
        {
            using var client = new SmtpClient();
            await client.ConnectAsync(_smtpHost, _smtpPort, SecureSocketOptions.StartTls, ct);
            await client.AuthenticateAsync(_smtpUser, _smtpPassword, ct);
            await client.SendAsync(message, ct);
            await client.DisconnectAsync(true, ct);

            _logger.LogInformation("Email sent: Subject='{Subject}', To={To}", subject, string.Join(", ", to));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to send email: Subject='{Subject}', To={To}", subject, string.Join(", ", to));
        }
    }
}
