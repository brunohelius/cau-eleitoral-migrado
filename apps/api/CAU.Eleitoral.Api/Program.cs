using System.Text;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using Microsoft.OpenApi.Models;
using Serilog;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Application.Services;
using CAU.Eleitoral.Domain.Interfaces.Repositories;
using CAU.Eleitoral.Infrastructure.Data;
using CAU.Eleitoral.Infrastructure.Repositories;

var builder = WebApplication.CreateBuilder(args);

// Configure Serilog
Log.Logger = new LoggerConfiguration()
    .ReadFrom.Configuration(builder.Configuration)
    .Enrich.FromLogContext()
    .WriteTo.Console()
    .CreateLogger();

builder.Host.UseSerilog();

// Add services to the container
builder.Services.AddControllers();

// Configure Swagger/OpenAPI
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen(c =>
{
    c.SwaggerDoc("v1", new OpenApiInfo
    {
        Title = "CAU Sistema Eleitoral API",
        Version = "v1",
        Description = "API do Sistema Eleitoral do CAU",
        Contact = new OpenApiContact
        {
            Name = "CAU",
            Email = "suporte@cau.org.br"
        }
    });

    c.AddSecurityDefinition("Bearer", new OpenApiSecurityScheme
    {
        Description = "JWT Authorization header using the Bearer scheme. Example: \"Bearer {token}\"",
        Name = "Authorization",
        In = ParameterLocation.Header,
        Type = SecuritySchemeType.ApiKey,
        Scheme = "Bearer"
    });

    c.AddSecurityRequirement(new OpenApiSecurityRequirement
    {
        {
            new OpenApiSecurityScheme
            {
                Reference = new OpenApiReference
                {
                    Type = ReferenceType.SecurityScheme,
                    Id = "Bearer"
                }
            },
            Array.Empty<string>()
        }
    });
});

// Configure Database
builder.Services.AddDbContext<AppDbContext>(options =>
{
    options.UseNpgsql(
        builder.Configuration.GetConnectionString("DefaultConnection"),
        b => b.MigrationsAssembly("CAU.Eleitoral.Infrastructure"));
});

// Configure JWT Authentication
var jwtKey = builder.Configuration["Jwt:Key"];
if (string.IsNullOrWhiteSpace(jwtKey))
{
    throw new InvalidOperationException("Configuration 'Jwt:Key' is required.");
}
if (!builder.Environment.IsDevelopment() && jwtKey.Contains("YourSuperSecretKeyHere", StringComparison.Ordinal))
{
    throw new InvalidOperationException("Configuration 'Jwt:Key' must be overridden outside development.");
}
var jwtIssuer = builder.Configuration["Jwt:Issuer"] ?? "CAU.Eleitoral";
var jwtAudience = builder.Configuration["Jwt:Audience"] ?? "CAU.Eleitoral.Client";

builder.Services.AddAuthentication(JwtBearerDefaults.AuthenticationScheme)
    .AddJwtBearer(options =>
    {
        options.TokenValidationParameters = new TokenValidationParameters
        {
            ValidateIssuer = true,
            ValidateAudience = true,
            ValidateLifetime = true,
            ValidateIssuerSigningKey = true,
            ValidIssuer = jwtIssuer,
            ValidAudience = jwtAudience,
            IssuerSigningKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(jwtKey))
        };
    });

builder.Services.AddAuthorization();

// Configure CORS
builder.Services.AddCors(options =>
{
    options.AddPolicy("AllowAll", policy =>
    {
        policy.AllowAnyOrigin()
              .AllowAnyMethod()
              .AllowAnyHeader();
    });

    options.AddPolicy("Production", policy =>
    {
        policy.WithOrigins(
            "https://cau-admin.migrai.com.br",
            "https://cau-public.migrai.com.br",
            "https://cau-api.migrai.com.br"
        )
        .AllowAnyMethod()
        .AllowAnyHeader()
        .AllowCredentials();
    });
});

// Register Repositories
builder.Services.AddScoped(typeof(IRepository<>), typeof(Repository<>));
builder.Services.AddScoped<IUnitOfWork, UnitOfWork>();

// Register Services
builder.Services.AddScoped<IEleicaoService, EleicaoService>();
builder.Services.AddScoped<IChapaService, ChapaService>();
builder.Services.AddScoped<IUsuarioService, UsuarioService>();
builder.Services.AddScoped<IAuthService, AuthService>();
builder.Services.AddScoped<IDenunciaService, DenunciaService>();
builder.Services.AddScoped<IImpugnacaoService, ImpugnacaoService>();
builder.Services.AddScoped<IJulgamentoService, JulgamentoService>();
builder.Services.AddScoped<IDocumentoService, DocumentoService>();
builder.Services.AddScoped<ICalendarioService, CalendarioService>();
builder.Services.AddScoped<IVotacaoService, VotacaoService>();
builder.Services.AddScoped<IApuracaoService, ApuracaoService>();
builder.Services.AddScoped<IRelatorioService, RelatorioService>();
builder.Services.AddScoped<INotificacaoService, NotificacaoService>();
builder.Services.AddScoped<IAuditoriaService, AuditoriaService>();
builder.Services.AddScoped<IConfiguracaoService, ConfiguracaoService>();
builder.Services.AddScoped<CAU.Eleitoral.Api.Controllers.IDashboardService, CAU.Eleitoral.Api.Services.DashboardService>();
builder.Services.AddScoped<CAU.Eleitoral.Api.Controllers.IAuditoriaService, CAU.Eleitoral.Api.Services.AuditoriaApiService>();
builder.Services.AddScoped<CAU.Eleitoral.Api.Controllers.IFilialService, CAU.Eleitoral.Api.Services.FilialApiService>();
builder.Services.AddScoped<CAU.Eleitoral.Api.Controllers.INotificacaoService, CAU.Eleitoral.Api.Services.NotificacaoApiService>();
builder.Services.AddScoped<CAU.Eleitoral.Api.Controllers.IDocumentoService, CAU.Eleitoral.Api.Services.DocumentoApiService>();
builder.Services.AddScoped<CAU.Eleitoral.Api.Controllers.IJulgamentoService, CAU.Eleitoral.Api.Services.JulgamentoApiService>();
builder.Services.AddScoped<CAU.Eleitoral.Api.Controllers.IRelatorioService, CAU.Eleitoral.Api.Services.RelatorioApiService>();
builder.Services.AddScoped<CAU.Eleitoral.Api.Controllers.IMembroChapaService, CAU.Eleitoral.Api.Services.MembroChapaApiService>();
builder.Services.AddScoped<CAU.Eleitoral.Api.Controllers.ICalendarioService, CAU.Eleitoral.Api.Services.CalendarioApiService>();
builder.Services.AddScoped<CAU.Eleitoral.Api.Controllers.IConselheiroService, CAU.Eleitoral.Api.Services.ConselheiroApiService>();

// Add Health Checks
builder.Services.AddHealthChecks()
    .AddNpgSql(builder.Configuration.GetConnectionString("DefaultConnection") ?? "");

// Register Database Seeder
builder.Services.AddScoped<DatabaseSeeder>();

var app = builder.Build();

// Configure the HTTP request pipeline
if (app.Environment.IsDevelopment())
{
    app.UseSwagger();
    app.UseSwaggerUI(c =>
    {
        c.SwaggerEndpoint("/swagger/v1/swagger.json", "CAU Sistema Eleitoral API v1");
        c.RoutePrefix = string.Empty;
    });
    app.UseCors("AllowAll");
}
else
{
    app.UseCors("Production");
    app.UseHsts();
}

app.UseHttpsRedirection();
app.UseSerilogRequestLogging();

app.UseAuthentication();
app.UseAuthorization();

app.MapControllers();
app.MapHealthChecks("/health");

var adminMaintenanceRequested =
    app.Environment.IsDevelopment() ||
    builder.Configuration.GetValue<bool>("Admin:EnableMaintenanceEndpoints");
var adminSeedKey = app.Configuration["Admin:SeedKey"];

if (adminMaintenanceRequested && !string.IsNullOrWhiteSpace(adminSeedKey))
{
    // Seed endpoint (protected by secret key)
    app.MapPost("/api/admin/seed", async (HttpContext context, DatabaseSeeder seeder) =>
    {
        if (!HasValidAdminSeedKey(context, adminSeedKey!))
        {
            return Results.Unauthorized();
        }

        try
        {
            Log.Information("Starting database seeding via API...");
            await seeder.SeedAsync();
            Log.Information("Database seeding completed via API.");
            return Results.Ok(new { message = "Database seeded successfully" });
        }
        catch (Exception ex)
        {
            Log.Error(ex, "Error seeding database");
            return Results.Problem($"Error seeding database: {ex.Message}");
        }
    });

    // Diagnostic endpoint - returns table counts for debugging
    app.MapGet("/api/admin/diag", async (HttpContext context, AppDbContext db) =>
    {
        if (!HasValidAdminSeedKey(context, adminSeedKey!))
        {
            return Results.Unauthorized();
        }

        var calFiltered = await db.Calendarios.CountAsync();
        var calTotal = await db.Calendarios.IgnoreQueryFilters().CountAsync();
        var calDeleted = await db.Calendarios.IgnoreQueryFilters().Where(c => c.IsDeleted).CountAsync();
        var docFiltered = await db.Documentos.CountAsync();
        var docTotal = await db.Documentos.IgnoreQueryFilters().CountAsync();
        var docDeleted = await db.Documentos.IgnoreQueryFilters().Where(d => d.IsDeleted).CountAsync();
        var editalCount = await db.Editais.CountAsync();
        var eleicaoCount = await db.Eleicoes.CountAsync();
        var chapaCount = await db.Chapas.CountAsync();
        var membroCount = await db.MembrosChapa.CountAsync();

        return Results.Ok(new
        {
            calendarios = new { filtered = calFiltered, total = calTotal, deleted = calDeleted },
            documentos = new { filtered = docFiltered, total = docTotal, deleted = docDeleted },
            editais = editalCount,
            eleicoes = eleicaoCount,
            chapas = chapaCount,
            membrosChapa = membroCount
        });
    });

    Log.Information("Admin maintenance endpoints enabled.");
}
else if (adminMaintenanceRequested)
{
    Log.Warning("Admin maintenance endpoints requested but 'Admin:SeedKey' is not configured. Endpoints remain disabled.");
}
else
{
    Log.Information("Admin maintenance endpoints disabled.");
}

// Apply migrations on startup (controlled by environment variable)
var runMigrations = builder.Configuration.GetValue<bool>("Database:RunMigrationsOnStartup", app.Environment.IsDevelopment());
if (runMigrations)
{
    Log.Information("Running database migrations...");
    using var scope = app.Services.CreateScope();
    var db = scope.ServiceProvider.GetRequiredService<AppDbContext>();
    await db.Database.MigrateAsync();
    Log.Information("Database migrations completed.");

    // Only seed in development
    if (app.Environment.IsDevelopment())
    {
        var seeder = scope.ServiceProvider.GetRequiredService<DatabaseSeeder>();
        await seeder.SeedAsync();
    }
}

static bool HasValidAdminSeedKey(HttpContext context, string expectedKey)
{
    var providedSeedKey = context.Request.Headers["X-Seed-Key"].FirstOrDefault();
    return !string.IsNullOrWhiteSpace(providedSeedKey) &&
           string.Equals(providedSeedKey, expectedKey, StringComparison.Ordinal);
}

app.Run();
