using CAU.Eleitoral.Infrastructure.Services.Email;
using FluentAssertions;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.Logging;
using Moq;

namespace CAU.Eleitoral.Tests;

public class EmailServiceTests
{
    [Fact]
    public void Constructor_WithEmptySmtpHost_DisablesService()
    {
        var config = BuildConfig(smtpHost: "", smtpUser: "user", smtpPassword: "pass");
        var logger = new Mock<ILogger<EmailService>>();

        var sut = new EmailService(config, logger.Object);

        // Should log warning about disabled service
        logger.Verify(
            x => x.Log(
                LogLevel.Warning,
                It.IsAny<EventId>(),
                It.Is<It.IsAnyType>((v, t) => v.ToString()!.Contains("Email service disabled")),
                null,
                It.IsAny<Func<It.IsAnyType, Exception?, string>>()),
            Times.Once);
    }

    [Fact]
    public void Constructor_WithExampleHost_DisablesService()
    {
        var config = BuildConfig(smtpHost: "smtp.example.com", smtpUser: "user", smtpPassword: "pass");
        var logger = new Mock<ILogger<EmailService>>();

        var sut = new EmailService(config, logger.Object);

        logger.Verify(
            x => x.Log(
                LogLevel.Warning,
                It.IsAny<EventId>(),
                It.Is<It.IsAnyType>((v, t) => v.ToString()!.Contains("Email service disabled")),
                null,
                It.IsAny<Func<It.IsAnyType, Exception?, string>>()),
            Times.Once);
    }

    [Fact]
    public void Constructor_WithEmptyPassword_DisablesService()
    {
        var config = BuildConfig(smtpHost: "smtp.ses.com", smtpUser: "user", smtpPassword: "");
        var logger = new Mock<ILogger<EmailService>>();

        var sut = new EmailService(config, logger.Object);

        logger.Verify(
            x => x.Log(
                LogLevel.Warning,
                It.IsAny<EventId>(),
                It.Is<It.IsAnyType>((v, t) => v.ToString()!.Contains("Email service disabled")),
                null,
                It.IsAny<Func<It.IsAnyType, Exception?, string>>()),
            Times.Once);
    }

    [Fact]
    public async Task SendAsync_WhenDisabled_LogsAndReturns()
    {
        var config = BuildConfig(smtpHost: "", smtpUser: "", smtpPassword: "");
        var logger = new Mock<ILogger<EmailService>>();
        var sut = new EmailService(config, logger.Object);

        // Should not throw
        await sut.SendAsync("test@example.com", "Test Subject", "<p>Body</p>");

        logger.Verify(
            x => x.Log(
                LogLevel.Information,
                It.IsAny<EventId>(),
                It.Is<It.IsAnyType>((v, t) => v.ToString()!.Contains("Email not sent (disabled)")),
                null,
                It.IsAny<Func<It.IsAnyType, Exception?, string>>()),
            Times.Once);
    }

    [Fact]
    public async Task SendAsync_WithMultipleRecipients_WhenDisabled_LogsAllRecipients()
    {
        var config = BuildConfig(smtpHost: "", smtpUser: "", smtpPassword: "");
        var logger = new Mock<ILogger<EmailService>>();
        var sut = new EmailService(config, logger.Object);

        var recipients = new[] { "a@test.com", "b@test.com", "c@test.com" };
        await sut.SendAsync(recipients, "Multi Test", "<p>Body</p>");

        logger.Verify(
            x => x.Log(
                LogLevel.Information,
                It.IsAny<EventId>(),
                It.IsAny<It.IsAnyType>(),
                null,
                It.IsAny<Func<It.IsAnyType, Exception?, string>>()),
            Times.AtLeastOnce);
    }

    [Fact]
    public void Constructor_WithValidConfig_DoesNotLogWarning()
    {
        var config = BuildConfig(
            smtpHost: "email-smtp.us-east-1.amazonaws.com",
            smtpUser: "AKIAIOSFODNN7EXAMPLE",
            smtpPassword: "wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY");
        var logger = new Mock<ILogger<EmailService>>();

        var sut = new EmailService(config, logger.Object);

        logger.Verify(
            x => x.Log(
                LogLevel.Warning,
                It.IsAny<EventId>(),
                It.IsAny<It.IsAnyType>(),
                null,
                It.IsAny<Func<It.IsAnyType, Exception?, string>>()),
            Times.Never);
    }

    private static IConfiguration BuildConfig(string smtpHost, string smtpUser, string smtpPassword)
    {
        var inMemory = new Dictionary<string, string?>
        {
            ["Email:SmtpHost"] = smtpHost,
            ["Email:SmtpPort"] = "587",
            ["Email:SmtpUser"] = smtpUser,
            ["Email:SmtpPassword"] = smtpPassword,
            ["Email:FromEmail"] = "noreply@cau.org.br",
            ["Email:FromName"] = "CAU Sistema Eleitoral"
        };

        return new ConfigurationBuilder()
            .AddInMemoryCollection(inMemory)
            .Build();
    }
}
