using System.Net;
using Amazon.SimpleSystemsManagement;
using Amazon.SimpleSystemsManagement.Model;
using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.Mvc.Testing;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.DependencyInjection.Extensions;
using Moq;

namespace CAU.Eleitoral.Tests;

public sealed class ProductionMaintenanceEndpointTests(
    ProductionApiFactory factory) : IClassFixture<ProductionApiFactory>
{
    [Theory]
    [InlineData("GET", "/api/admin/diag")]
    [InlineData("POST", "/api/admin/seed")]
    public async Task Maintenance_endpoint_is_not_mapped_in_production(
        string method,
        string path)
    {
        using var client = factory.CreateClient(new WebApplicationFactoryClientOptions
        {
            AllowAutoRedirect = false,
            BaseAddress = new Uri("https://localhost")
        });
        using var request = new HttpRequestMessage(new HttpMethod(method), path);
        request.Headers.Add("X-Seed-Key", ProductionApiFactory.MaintenanceKey);

        using var response = await client.SendAsync(request);

        Assert.Equal(HttpStatusCode.NotFound, response.StatusCode);
    }
}

public sealed class ProductionApiFactory : WebApplicationFactory<global::Program>
{
    internal static readonly string MaintenanceKey = new('k', 32);

    protected override void ConfigureWebHost(IWebHostBuilder builder)
    {
        builder
            .UseEnvironment("Production")
            .UseSetting("Admin:EnableMaintenanceEndpoints", "true")
            .UseSetting("Admin:SeedKey", MaintenanceKey)
            .UseSetting("DataProtection:PersistKeysToSsm", "false")
            .UseSetting("Database:RunMigrationsOnStartup", "false")
            .UseSetting("Jwt:Key", new string('j', 64));
    }
}

public sealed class ProductionDataProtectionTests(
    SsmDataProtectionApiFactory factory) : IClassFixture<SsmDataProtectionApiFactory>
{
    [Fact]
    public async Task Production_persists_data_protection_keys_as_secure_strings()
    {
        using var client = factory.CreateClient(new WebApplicationFactoryClientOptions
        {
            AllowAutoRedirect = false,
            BaseAddress = new Uri("https://localhost")
        });

        using var response = await client.GetAsync("/api/admin/diag");

        Assert.Equal(HttpStatusCode.NotFound, response.StatusCode);
        factory.SsmClient.Verify(client => client.PutParameterAsync(
            It.Is<PutParameterRequest>(request =>
                request.Name.StartsWith(
                    "/migrai/cau-eleitoral/data-protection/",
                    StringComparison.Ordinal) &&
                request.Type == ParameterType.SecureString &&
                request.Value.Contains("<key", StringComparison.Ordinal)),
            It.IsAny<CancellationToken>()), Times.Once);
    }
}

public sealed class SsmDataProtectionApiFactory : WebApplicationFactory<global::Program>
{
    public Mock<IAmazonSimpleSystemsManagement> SsmClient { get; } = new();

    public SsmDataProtectionApiFactory()
    {
        SsmClient
            .Setup(client => client.GetParametersByPathAsync(
                It.IsAny<GetParametersByPathRequest>(),
                It.IsAny<CancellationToken>()))
            .ReturnsAsync(new GetParametersByPathResponse
            {
                Parameters = []
            });
        SsmClient
            .Setup(client => client.PutParameterAsync(
                It.IsAny<PutParameterRequest>(),
                It.IsAny<CancellationToken>()))
            .ReturnsAsync(new PutParameterResponse
            {
                HttpStatusCode = HttpStatusCode.OK
            });
    }

    protected override void ConfigureWebHost(IWebHostBuilder builder)
    {
        builder
            .UseEnvironment("Production")
            .UseSetting("Admin:EnableMaintenanceEndpoints", "true")
            .UseSetting("Admin:SeedKey", ProductionApiFactory.MaintenanceKey)
            .UseSetting("DataProtection:PersistKeysToSsm", "true")
            .UseSetting("Database:RunMigrationsOnStartup", "false")
            .UseSetting("Jwt:Key", new string('j', 64));
        builder.ConfigureServices(services =>
        {
            services.RemoveAll<IAmazonSimpleSystemsManagement>();
            services.AddSingleton(SsmClient.Object);
        });
    }
}
