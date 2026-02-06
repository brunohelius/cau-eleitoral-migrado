using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace CAU.Eleitoral.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class AddAuditoriaLogsAndNotificacoes : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "AuditoriaLogs",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "uuid", nullable: false),
                    UsuarioId = table.Column<Guid>(type: "uuid", nullable: true),
                    UsuarioNome = table.Column<string>(type: "text", nullable: true),
                    UsuarioEmail = table.Column<string>(type: "text", nullable: true),
                    Acao = table.Column<string>(type: "text", nullable: false),
                    EntidadeTipo = table.Column<string>(type: "text", nullable: false),
                    EntidadeId = table.Column<Guid>(type: "uuid", nullable: true),
                    EntidadeNome = table.Column<string>(type: "text", nullable: true),
                    Detalhes = table.Column<string>(type: "text", nullable: true),
                    ValorAnterior = table.Column<string>(type: "text", nullable: true),
                    ValorNovo = table.Column<string>(type: "text", nullable: true),
                    IpAddress = table.Column<string>(type: "text", nullable: true),
                    UserAgent = table.Column<string>(type: "text", nullable: true),
                    Recurso = table.Column<string>(type: "text", nullable: true),
                    Metodo = table.Column<string>(type: "text", nullable: true),
                    StatusCode = table.Column<int>(type: "integer", nullable: true),
                    Sucesso = table.Column<bool>(type: "boolean", nullable: false),
                    Mensagem = table.Column<string>(type: "text", nullable: true),
                    Nivel = table.Column<string>(type: "text", nullable: false),
                    DataAcao = table.Column<DateTime>(type: "timestamp with time zone", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "timestamp with time zone", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "timestamp with time zone", nullable: true),
                    CreatedBy = table.Column<string>(type: "text", nullable: true),
                    UpdatedBy = table.Column<string>(type: "text", nullable: true),
                    IsDeleted = table.Column<bool>(type: "boolean", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AuditoriaLogs", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "Notificacoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "uuid", nullable: false),
                    UsuarioId = table.Column<Guid>(type: "uuid", nullable: false),
                    Titulo = table.Column<string>(type: "text", nullable: false),
                    Mensagem = table.Column<string>(type: "text", nullable: false),
                    Tipo = table.Column<string>(type: "text", nullable: false),
                    Canal = table.Column<string>(type: "text", nullable: false),
                    Status = table.Column<string>(type: "text", nullable: false),
                    Lida = table.Column<bool>(type: "boolean", nullable: false),
                    DataLeitura = table.Column<DateTime>(type: "timestamp with time zone", nullable: true),
                    DataEnvio = table.Column<DateTime>(type: "timestamp with time zone", nullable: false),
                    Link = table.Column<string>(type: "text", nullable: true),
                    Dados = table.Column<string>(type: "text", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "timestamp with time zone", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "timestamp with time zone", nullable: true),
                    CreatedBy = table.Column<string>(type: "text", nullable: true),
                    UpdatedBy = table.Column<string>(type: "text", nullable: true),
                    IsDeleted = table.Column<bool>(type: "boolean", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Notificacoes", x => x.Id);
                });
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "AuditoriaLogs");

            migrationBuilder.DropTable(
                name: "Notificacoes");
        }
    }
}
