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
var jwtKey = builder.Configuration["Jwt:Key"] ?? "DefaultSecretKeyForDevelopment123456789012345678901234567890";
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

// Seed endpoint (protected by secret key)
app.MapPost("/api/admin/seed", async (HttpContext context, DatabaseSeeder seeder) =>
{
    var seedKey = context.Request.Headers["X-Seed-Key"].FirstOrDefault();
    var expectedKey = app.Configuration["Admin:SeedKey"] ?? "CAU-SEED-2026-SECRET";

    if (seedKey != expectedKey)
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

// Force create admin endpoint (protected by secret key)
app.MapPost("/api/admin/setup-admin", async (HttpContext context, AppDbContext db) =>
{
    var seedKey = context.Request.Headers["X-Seed-Key"].FirstOrDefault();
    var expectedKey = app.Configuration["Admin:SeedKey"] ?? "CAU-SEED-2026-SECRET";

    if (seedKey != expectedKey)
    {
        return Results.Unauthorized();
    }

    try
    {
        var email = "admin@cau.org.br";
        var existingUser = await db.Usuarios.FirstOrDefaultAsync(u => u.Email == email);

        // Generate password hash
        var saltBytes = System.Security.Cryptography.RandomNumberGenerator.GetBytes(16);
        var salt = Convert.ToBase64String(saltBytes);
        using var pbkdf2 = new System.Security.Cryptography.Rfc2898DeriveBytes(
            "Admin@123", saltBytes, 100000, System.Security.Cryptography.HashAlgorithmName.SHA256);
        var hash = Convert.ToBase64String(pbkdf2.GetBytes(32));

        if (existingUser != null)
        {
            // Update existing user
            existingUser.PasswordHash = hash;
            existingUser.PasswordSalt = salt;
            existingUser.EmailConfirmado = true;
            existingUser.Status = CAU.Eleitoral.Domain.Enums.StatusUsuario.Ativo;
            await db.SaveChangesAsync();
            return Results.Ok(new { message = "Admin user updated successfully", email });
        }
        else
        {
            // Create new admin
            var adminRole = await db.Roles.FirstOrDefaultAsync(r => r.Nome == "Administrador");
            var admin = new CAU.Eleitoral.Domain.Entities.Usuarios.Usuario
            {
                Nome = "Admin Sistema",
                Email = email,
                Cpf = "11111111111",
                PasswordHash = hash,
                PasswordSalt = salt,
                Status = CAU.Eleitoral.Domain.Enums.StatusUsuario.Ativo,
                EmailConfirmado = true,
                Tipo = CAU.Eleitoral.Domain.Enums.TipoUsuario.Administrador
            };
            await db.Usuarios.AddAsync(admin);
            await db.SaveChangesAsync();

            if (adminRole != null)
            {
                await db.UsuarioRoles.AddAsync(new CAU.Eleitoral.Domain.Entities.Usuarios.UsuarioRole
                {
                    UsuarioId = admin.Id,
                    RoleId = adminRole.Id
                });
                await db.SaveChangesAsync();
            }
            return Results.Ok(new { message = "Admin user created successfully", email });
        }
    }
    catch (Exception ex)
    {
        Log.Error(ex, "Error setting up admin");
        return Results.Problem($"Error setting up admin: {ex.Message}");
    }
});

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

app.Run();
