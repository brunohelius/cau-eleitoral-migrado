using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace CAU.Eleitoral.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class AddMembroChapaSubstituicaoColumns : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "ArquivoUrl",
                table: "PlataformasEleitorais",
                type: "text",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "ConteudoCompleto",
                table: "PlataformasEleitorais",
                type: "text",
                nullable: true);

            migrationBuilder.AddColumn<DateTime>(
                name: "DataAtualizacao",
                table: "PlataformasEleitorais",
                type: "timestamp with time zone",
                nullable: true);

            migrationBuilder.AddColumn<DateTime>(
                name: "DataSubstituicao",
                table: "MembrosChapa",
                type: "timestamp with time zone",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "MotivoSubstituicao",
                table: "MembrosChapa",
                type: "text",
                nullable: true);

            migrationBuilder.AddColumn<Guid>(
                name: "AnalistaId",
                table: "DocumentosChapa",
                type: "uuid",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "ParecerAnalise",
                table: "DocumentosChapa",
                type: "text",
                nullable: true);

            migrationBuilder.CreateTable(
                name: "Configuracoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "uuid", nullable: false),
                    Chave = table.Column<string>(type: "text", nullable: false),
                    Valor = table.Column<string>(type: "text", nullable: false),
                    Descricao = table.Column<string>(type: "text", nullable: true),
                    Tipo = table.Column<string>(type: "text", nullable: false),
                    Categoria = table.Column<string>(type: "text", nullable: false),
                    Editavel = table.Column<bool>(type: "boolean", nullable: false),
                    Ativo = table.Column<bool>(type: "boolean", nullable: false),
                    ValorPadrao = table.Column<string>(type: "text", nullable: true),
                    UltimoUsuarioAtualizacaoId = table.Column<Guid>(type: "uuid", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "timestamp with time zone", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "timestamp with time zone", nullable: true),
                    CreatedBy = table.Column<string>(type: "text", nullable: true),
                    UpdatedBy = table.Column<string>(type: "text", nullable: true),
                    IsDeleted = table.Column<bool>(type: "boolean", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Configuracoes", x => x.Id);
                });
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "Configuracoes");

            migrationBuilder.DropColumn(
                name: "ArquivoUrl",
                table: "PlataformasEleitorais");

            migrationBuilder.DropColumn(
                name: "ConteudoCompleto",
                table: "PlataformasEleitorais");

            migrationBuilder.DropColumn(
                name: "DataAtualizacao",
                table: "PlataformasEleitorais");

            migrationBuilder.DropColumn(
                name: "DataSubstituicao",
                table: "MembrosChapa");

            migrationBuilder.DropColumn(
                name: "MotivoSubstituicao",
                table: "MembrosChapa");

            migrationBuilder.DropColumn(
                name: "AnalistaId",
                table: "DocumentosChapa");

            migrationBuilder.DropColumn(
                name: "ParecerAnalise",
                table: "DocumentosChapa");
        }
    }
}
