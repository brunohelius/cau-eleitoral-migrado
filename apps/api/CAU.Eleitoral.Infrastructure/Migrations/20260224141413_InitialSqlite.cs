using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace CAU.Eleitoral.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class InitialSqlite : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "ArquivosJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    NomeOriginal = table.Column<string>(type: "TEXT", nullable: false),
                    NomeArmazenado = table.Column<string>(type: "TEXT", nullable: false),
                    Extensao = table.Column<string>(type: "TEXT", nullable: true),
                    MimeType = table.Column<string>(type: "TEXT", nullable: true),
                    Tamanho = table.Column<long>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: false),
                    ArquivoCaminhoFisico = table.Column<string>(type: "TEXT", nullable: true),
                    HashMD5 = table.Column<string>(type: "TEXT", nullable: true),
                    HashSHA256 = table.Column<string>(type: "TEXT", nullable: true),
                    DataUpload = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UploadPor = table.Column<string>(type: "TEXT", nullable: true),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    Publico = table.Column<bool>(type: "INTEGER", nullable: false),
                    Assinado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    AssinadoPor = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ArquivosJulgamento", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "AuditoriaLogs",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: true),
                    UsuarioNome = table.Column<string>(type: "TEXT", nullable: true),
                    UsuarioEmail = table.Column<string>(type: "TEXT", nullable: true),
                    Acao = table.Column<string>(type: "TEXT", nullable: false),
                    EntidadeTipo = table.Column<string>(type: "TEXT", nullable: false),
                    EntidadeId = table.Column<Guid>(type: "TEXT", nullable: true),
                    EntidadeNome = table.Column<string>(type: "TEXT", nullable: true),
                    Detalhes = table.Column<string>(type: "TEXT", nullable: true),
                    ValorAnterior = table.Column<string>(type: "TEXT", nullable: true),
                    ValorNovo = table.Column<string>(type: "TEXT", nullable: true),
                    IpAddress = table.Column<string>(type: "TEXT", nullable: true),
                    UserAgent = table.Column<string>(type: "TEXT", nullable: true),
                    Recurso = table.Column<string>(type: "TEXT", nullable: true),
                    Metodo = table.Column<string>(type: "TEXT", nullable: true),
                    StatusCode = table.Column<int>(type: "INTEGER", nullable: true),
                    Sucesso = table.Column<bool>(type: "INTEGER", nullable: false),
                    Mensagem = table.Column<string>(type: "TEXT", nullable: true),
                    Nivel = table.Column<string>(type: "TEXT", nullable: false),
                    DataAcao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AuditoriaLogs", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "CarimbosTempo",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    NumeroSerie = table.Column<string>(type: "TEXT", nullable: false),
                    HashOriginal = table.Column<string>(type: "TEXT", nullable: true),
                    HashComCarimbo = table.Column<string>(type: "TEXT", nullable: true),
                    DataCarimbo = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataValidadeCarimbo = table.Column<DateTime>(type: "TEXT", nullable: true),
                    AutoridadeCarimbo = table.Column<string>(type: "TEXT", nullable: true),
                    PolicyId = table.Column<string>(type: "TEXT", nullable: true),
                    Nonce = table.Column<string>(type: "TEXT", nullable: true),
                    CarimboBase64 = table.Column<string>(type: "TEXT", nullable: true),
                    TokenTSA = table.Column<string>(type: "TEXT", nullable: true),
                    Verificado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataVerificacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ResultadoVerificacao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_CarimbosTempo", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "CategoriasDocumento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Codigo = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    CategoriaPaiId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Padrao = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_CategoriasDocumento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_CategoriasDocumento_CategoriasDocumento_CategoriaPaiId",
                        column: x => x.CategoriaPaiId,
                        principalTable: "CategoriasDocumento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "CertidoesJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Requerente = table.Column<string>(type: "TEXT", nullable: true),
                    Finalidade = table.Column<string>(type: "TEXT", nullable: true),
                    DataEmissao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataValidade = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    EmitidoPor = table.Column<string>(type: "TEXT", nullable: true),
                    CargoEmissor = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CodigoVerificacao = table.Column<string>(type: "TEXT", nullable: true),
                    QRCodeUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Assinada = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    AssinadoPor = table.Column<string>(type: "TEXT", nullable: true),
                    CertificadoDigitalId = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_CertidoesJulgamento", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "Configuracoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Chave = table.Column<string>(type: "TEXT", nullable: false),
                    Valor = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<string>(type: "TEXT", nullable: false),
                    Categoria = table.Column<string>(type: "TEXT", nullable: false),
                    Editavel = table.Column<bool>(type: "INTEGER", nullable: false),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    ValorPadrao = table.Column<string>(type: "TEXT", nullable: true),
                    UltimoUsuarioAtualizacaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Configuracoes", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "DecisoesJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Resultado = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Dispositivo = table.Column<string>(type: "TEXT", nullable: true),
                    Efeitos = table.Column<string>(type: "TEXT", nullable: true),
                    DataDecisao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Publicada = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_DecisoesJulgamento", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "FasesEleicaoConfig",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Fase = table.Column<int>(type: "INTEGER", nullable: false),
                    Codigo = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Obrigatoria = table.Column<bool>(type: "INTEGER", nullable: false),
                    DuracaoMinimaDias = table.Column<int>(type: "INTEGER", nullable: true),
                    DuracaoMaximaDias = table.Column<int>(type: "INTEGER", nullable: true),
                    FaseAnterior = table.Column<int>(type: "INTEGER", nullable: true),
                    FasePosterior = table.Column<int>(type: "INTEGER", nullable: true),
                    PermiteRetrocesso = table.Column<bool>(type: "INTEGER", nullable: false),
                    RequerAprovacao = table.Column<bool>(type: "INTEGER", nullable: false),
                    NotificarInicio = table.Column<bool>(type: "INTEGER", nullable: false),
                    NotificarFim = table.Column<bool>(type: "INTEGER", nullable: false),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_FasesEleicaoConfig", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "IntimacoesJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Forma = table.Column<int>(type: "INTEGER", nullable: false),
                    Intimado = table.Column<string>(type: "TEXT", nullable: false),
                    QualificacaoIntimado = table.Column<string>(type: "TEXT", nullable: true),
                    EmailIntimado = table.Column<string>(type: "TEXT", nullable: true),
                    EnderecoIntimado = table.Column<string>(type: "TEXT", nullable: true),
                    Finalidade = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    FundamentoLegal = table.Column<string>(type: "TEXT", nullable: true),
                    DataExpedicao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataCiencia = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPrazo = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoDias = table.Column<int>(type: "INTEGER", nullable: false),
                    PrazoUtil = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataInicioContagem = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataFimContagem = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ComprovanteUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Cumprida = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataCumprimento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ObservacaoCumprimento = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_IntimacoesJulgamento", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "ModelosDocumento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Codigo = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    ConteudoHtml = table.Column<string>(type: "TEXT", nullable: true),
                    EstilosCSS = table.Column<string>(type: "TEXT", nullable: true),
                    Cabecalho = table.Column<string>(type: "TEXT", nullable: true),
                    Rodape = table.Column<string>(type: "TEXT", nullable: true),
                    VariaveisDisponiveis = table.Column<string>(type: "TEXT", nullable: true),
                    ExemploPreenchido = table.Column<string>(type: "TEXT", nullable: true),
                    Versao = table.Column<int>(type: "INTEGER", nullable: true),
                    Padrao = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ModelosDocumento", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "Notificacoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Mensagem = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<string>(type: "TEXT", nullable: false),
                    Canal = table.Column<string>(type: "TEXT", nullable: false),
                    Status = table.Column<string>(type: "TEXT", nullable: false),
                    Lida = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataLeitura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataEnvio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Link = table.Column<string>(type: "TEXT", nullable: true),
                    Dados = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Notificacoes", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "NotificacoesJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Forma = table.Column<int>(type: "INTEGER", nullable: false),
                    Destinatario = table.Column<string>(type: "TEXT", nullable: false),
                    EmailDestinatario = table.Column<string>(type: "TEXT", nullable: true),
                    EnderecoDestinatario = table.Column<string>(type: "TEXT", nullable: true),
                    TelefoneDestinatario = table.Column<string>(type: "TEXT", nullable: true),
                    Assunto = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataEmissao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataEnvio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataRecebimento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPrazo = table.Column<DateTime>(type: "TEXT", nullable: true),
                    NumeroAR = table.Column<string>(type: "TEXT", nullable: true),
                    ComprovanteRecebimento = table.Column<string>(type: "TEXT", nullable: true),
                    MotivoNaoRecebimento = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ComprovanteUrl = table.Column<string>(type: "TEXT", nullable: true),
                    TentativasEnvio = table.Column<int>(type: "INTEGER", nullable: false),
                    UltimaTentativa = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_NotificacoesJulgamento", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "ParametrosEleicao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Chave = table.Column<string>(type: "TEXT", nullable: false),
                    Valor = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<string>(type: "TEXT", nullable: true),
                    Grupo = table.Column<string>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    Editavel = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ParametrosEleicao", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "Permissoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Codigo = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Modulo = table.Column<string>(type: "TEXT", nullable: true),
                    Recurso = table.Column<string>(type: "TEXT", nullable: true),
                    Acao = table.Column<string>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Permissoes", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "PublicacoesJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    DataAgendada = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    LinkPublicacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PublicacoesJulgamento", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "RegionaisCAU",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Sigla = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Cnpj = table.Column<string>(type: "TEXT", nullable: true),
                    Endereco = table.Column<string>(type: "TEXT", nullable: true),
                    Cidade = table.Column<string>(type: "TEXT", nullable: true),
                    UF = table.Column<string>(type: "TEXT", nullable: false),
                    Cep = table.Column<string>(type: "TEXT", nullable: true),
                    Telefone = table.Column<string>(type: "TEXT", nullable: true),
                    Email = table.Column<string>(type: "TEXT", nullable: true),
                    Site = table.Column<string>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RegionaisCAU", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "Roles",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Codigo = table.Column<string>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    SistemaRole = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Roles", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "TiposEleicaoConfig",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Codigo = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    PermiteVotoOnline = table.Column<bool>(type: "INTEGER", nullable: false),
                    PermiteVotoPresencial = table.Column<bool>(type: "INTEGER", nullable: false),
                    RequerAprovacao = table.Column<bool>(type: "INTEGER", nullable: false),
                    DuracaoMinimaInscricaoDias = table.Column<int>(type: "INTEGER", nullable: true),
                    DuracaoMinimaVotacaoDias = table.Column<int>(type: "INTEGER", nullable: true),
                    PrazoMaximoApuracaoDias = table.Column<int>(type: "INTEGER", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TiposEleicaoConfig", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "Usuarios",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Email = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    NomeCompleto = table.Column<string>(type: "TEXT", nullable: true),
                    Cpf = table.Column<string>(type: "TEXT", nullable: true),
                    Telefone = table.Column<string>(type: "TEXT", nullable: true),
                    PasswordHash = table.Column<string>(type: "TEXT", nullable: false),
                    PasswordSalt = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    UltimoAcesso = table.Column<DateTime>(type: "TEXT", nullable: true),
                    TentativasLogin = table.Column<int>(type: "INTEGER", nullable: false),
                    BloqueadoAte = table.Column<DateTime>(type: "TEXT", nullable: true),
                    EmailConfirmado = table.Column<bool>(type: "INTEGER", nullable: false),
                    TokenConfirmacaoEmail = table.Column<string>(type: "TEXT", nullable: true),
                    TokenConfirmacaoEmailExpiracao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    TokenRecuperacaoSenha = table.Column<string>(type: "TEXT", nullable: true),
                    TokenRecuperacaoSenhaExpiracao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DoisFatoresHabilitado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DoisFatoresSecret = table.Column<string>(type: "TEXT", nullable: true),
                    RefreshToken = table.Column<string>(type: "TEXT", nullable: true),
                    RefreshTokenExpiracao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Usuarios", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "Circunscricoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Codigo = table.Column<string>(type: "TEXT", nullable: true),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Circunscricoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Circunscricoes_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Eleicoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    FaseAtual = table.Column<int>(type: "INTEGER", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Mandato = table.Column<int>(type: "INTEGER", nullable: true),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataFim = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataVotacaoInicio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataVotacaoFim = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataApuracao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ModoVotacao = table.Column<int>(type: "INTEGER", nullable: false),
                    PermiteVotoOnline = table.Column<bool>(type: "INTEGER", nullable: false),
                    PermiteVotoPresencial = table.Column<bool>(type: "INTEGER", nullable: false),
                    QuantidadeVagas = table.Column<int>(type: "INTEGER", nullable: true),
                    QuantidadeSuplentes = table.Column<int>(type: "INTEGER", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Eleicoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Eleicoes_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Filiais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Codigo = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Endereco = table.Column<string>(type: "TEXT", nullable: true),
                    Cidade = table.Column<string>(type: "TEXT", nullable: true),
                    UF = table.Column<string>(type: "TEXT", nullable: true),
                    Cep = table.Column<string>(type: "TEXT", nullable: true),
                    Telefone = table.Column<string>(type: "TEXT", nullable: true),
                    Email = table.Column<string>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Filiais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Filiais_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "RolePermissoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    RoleId = table.Column<Guid>(type: "TEXT", nullable: false),
                    PermissaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RolePermissoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RolePermissoes_Permissoes_PermissaoId",
                        column: x => x.PermissaoId,
                        principalTable: "Permissoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_RolePermissoes_Roles_RoleId",
                        column: x => x.RoleId,
                        principalTable: "Roles",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "CertificadosDigitais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    NumeroSerie = table.Column<string>(type: "TEXT", nullable: false),
                    SubjectName = table.Column<string>(type: "TEXT", nullable: true),
                    IssuerName = table.Column<string>(type: "TEXT", nullable: true),
                    Thumbprint = table.Column<string>(type: "TEXT", nullable: true),
                    NomeTitular = table.Column<string>(type: "TEXT", nullable: true),
                    CpfCnpjTitular = table.Column<string>(type: "TEXT", nullable: true),
                    EmailTitular = table.Column<string>(type: "TEXT", nullable: true),
                    DataEmissao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataValidade = table.Column<DateTime>(type: "TEXT", nullable: false),
                    AutoridadeCertificadora = table.Column<string>(type: "TEXT", nullable: true),
                    CadeiaConfianca = table.Column<string>(type: "TEXT", nullable: true),
                    UrlCRL = table.Column<string>(type: "TEXT", nullable: true),
                    UrlOCSP = table.Column<string>(type: "TEXT", nullable: true),
                    ChavePublicaBase64 = table.Column<string>(type: "TEXT", nullable: true),
                    CertificadoBase64 = table.Column<string>(type: "TEXT", nullable: true),
                    EhIcpBrasil = table.Column<bool>(type: "INTEGER", nullable: false),
                    Revogado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataRevogacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    MotivoRevogacao = table.Column<string>(type: "TEXT", nullable: true),
                    UltimaValidacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ResultadoUltimaValidacao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_CertificadosDigitais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_CertificadosDigitais_Usuarios_UsuarioId",
                        column: x => x.UsuarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "LogsAcesso",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Acao = table.Column<string>(type: "TEXT", nullable: false),
                    Recurso = table.Column<string>(type: "TEXT", nullable: true),
                    Metodo = table.Column<string>(type: "TEXT", nullable: true),
                    Url = table.Column<string>(type: "TEXT", nullable: true),
                    IpAddress = table.Column<string>(type: "TEXT", nullable: true),
                    UserAgent = table.Column<string>(type: "TEXT", nullable: true),
                    DataAcesso = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Sucesso = table.Column<bool>(type: "INTEGER", nullable: false),
                    Mensagem = table.Column<string>(type: "TEXT", nullable: true),
                    DadosRequisicao = table.Column<string>(type: "TEXT", nullable: true),
                    DadosResposta = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_LogsAcesso", x => x.Id);
                    table.ForeignKey(
                        name: "FK_LogsAcesso_Usuarios_UsuarioId",
                        column: x => x.UsuarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "UsuarioRoles",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: false),
                    RoleId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ValidoAte = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_UsuarioRoles", x => x.Id);
                    table.ForeignKey(
                        name: "FK_UsuarioRoles_Roles_RoleId",
                        column: x => x.RoleId,
                        principalTable: "Roles",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_UsuarioRoles_Usuarios_UsuarioId",
                        column: x => x.UsuarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ZonasEleitorais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    CircunscricaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Endereco = table.Column<string>(type: "TEXT", nullable: true),
                    Cidade = table.Column<string>(type: "TEXT", nullable: true),
                    UF = table.Column<string>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ZonasEleitorais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ZonasEleitorais_Circunscricoes_CircunscricaoId",
                        column: x => x.CircunscricaoId,
                        principalTable: "Circunscricoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AtasReuniao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: true),
                    Ano = table.Column<int>(type: "INTEGER", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataReuniao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    HoraInicio = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    HoraFim = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    Local = table.Column<string>(type: "TEXT", nullable: true),
                    Modalidade = table.Column<string>(type: "TEXT", nullable: true),
                    OrgaoResponsavel = table.Column<string>(type: "TEXT", nullable: true),
                    Presidente = table.Column<string>(type: "TEXT", nullable: true),
                    Secretario = table.Column<string>(type: "TEXT", nullable: true),
                    Pauta = table.Column<string>(type: "TEXT", nullable: true),
                    Deliberacoes = table.Column<string>(type: "TEXT", nullable: true),
                    Presentes = table.Column<string>(type: "TEXT", nullable: true),
                    Ausentes = table.Column<string>(type: "TEXT", nullable: true),
                    Justificativas = table.Column<string>(type: "TEXT", nullable: true),
                    DataAprovacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AtasReuniao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AtasReuniao_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Atos",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataVigencia = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Autoridade = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Atos", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Atos_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Avisos",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: true),
                    Ano = table.Column<int>(type: "INTEGER", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Assunto = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataExpiracao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Destaque = table.Column<bool>(type: "INTEGER", nullable: false),
                    Urgente = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Avisos", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Avisos_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Calendarios",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Fase = table.Column<int>(type: "INTEGER", nullable: false),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataFim = table.Column<DateTime>(type: "TEXT", nullable: false),
                    HoraInicio = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    HoraFim = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Obrigatorio = table.Column<bool>(type: "INTEGER", nullable: false),
                    NotificarInicio = table.Column<bool>(type: "INTEGER", nullable: false),
                    NotificarFim = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Calendarios", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Calendarios_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Certificados",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    TipoCertificado = table.Column<string>(type: "TEXT", nullable: true),
                    Finalidade = table.Column<string>(type: "TEXT", nullable: true),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataExpedicao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataValidade = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataEntrega = table.Column<DateTime>(type: "TEXT", nullable: true),
                    LocalExpedicao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    CodigoVerificacao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Certificados", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Certificados_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Certificados_Usuarios_UsuarioId",
                        column: x => x.UsuarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Chapas",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Slogan = table.Column<string>(type: "TEXT", nullable: true),
                    Sigla = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataInscricao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataHomologacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataIndeferimento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    MotivoIndeferimento = table.Column<string>(type: "TEXT", nullable: true),
                    LogoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    FotoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CorPrimaria = table.Column<string>(type: "TEXT", nullable: true),
                    CorSecundaria = table.Column<string>(type: "TEXT", nullable: true),
                    OrdemSorteio = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Chapas", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Chapas_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ComissoesJulgadoras",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Sigla = table.Column<string>(type: "TEXT", nullable: true),
                    Portaria = table.Column<string>(type: "TEXT", nullable: true),
                    DataPortaria = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataFim = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Ativa = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ComissoesJulgadoras", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ComissoesJulgadoras_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Comunicados",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: true),
                    Ano = table.Column<int>(type: "INTEGER", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Assunto = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataExpiracao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Destaque = table.Column<bool>(type: "INTEGER", nullable: false),
                    Urgente = table.Column<bool>(type: "INTEGER", nullable: false),
                    Destinatarios = table.Column<string>(type: "TEXT", nullable: true),
                    Remetente = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Comunicados", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Comunicados_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ConfiguracoesEleicao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Chave = table.Column<string>(type: "TEXT", nullable: false),
                    Valor = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<string>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ConfiguracoesEleicao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ConfiguracoesEleicao_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Convocacoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: true),
                    Ano = table.Column<int>(type: "INTEGER", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Assunto = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataEvento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    HoraEvento = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    Local = table.Column<string>(type: "TEXT", nullable: true),
                    Endereco = table.Column<string>(type: "TEXT", nullable: true),
                    LinkOnline = table.Column<string>(type: "TEXT", nullable: true),
                    Pauta = table.Column<string>(type: "TEXT", nullable: true),
                    Convocados = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Convocacoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Convocacoes_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Declaracoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: true),
                    Ano = table.Column<int>(type: "INTEGER", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Finalidade = table.Column<string>(type: "TEXT", nullable: true),
                    Destinatario = table.Column<string>(type: "TEXT", nullable: true),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataExpedicao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataValidade = table.Column<DateTime>(type: "TEXT", nullable: true),
                    LocalExpedicao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    CodigoVerificacao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Declaracoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Declaracoes_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Declaracoes_Usuarios_UsuarioId",
                        column: x => x.UsuarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Deliberacoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataDeliberacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataVigencia = table.Column<DateTime>(type: "TEXT", nullable: true),
                    OrgaoDeliberante = table.Column<string>(type: "TEXT", nullable: true),
                    NumeroSessao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Deliberacoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Deliberacoes_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Documentos",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Categoria = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataVigencia = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataRevogacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTipo = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    CategoriaDocumentoEntityId = table.Column<Guid>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Documentos", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Documentos_CategoriasDocumento_CategoriaDocumentoEntityId",
                        column: x => x.CategoriaDocumentoEntityId,
                        principalTable: "CategoriasDocumento",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Documentos_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Editais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Editais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Editais_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "EleicaoSituacoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    StatusAnterior = table.Column<int>(type: "INTEGER", nullable: false),
                    StatusNovo = table.Column<int>(type: "INTEGER", nullable: false),
                    FaseAnterior = table.Column<int>(type: "INTEGER", nullable: true),
                    FaseNova = table.Column<int>(type: "INTEGER", nullable: true),
                    DataAlteracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Motivo = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    AlteradoPor = table.Column<string>(type: "TEXT", nullable: true),
                    Ip = table.Column<string>(type: "TEXT", nullable: true),
                    UserAgent = table.Column<string>(type: "TEXT", nullable: true),
                    Automatico = table.Column<bool>(type: "INTEGER", nullable: false),
                    ReferenciaAutomatico = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_EleicaoSituacoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_EleicaoSituacoes_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "EstatisticasEleicao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Categoria = table.Column<string>(type: "TEXT", nullable: false),
                    Indicador = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    ValorNumerico = table.Column<decimal>(type: "TEXT", nullable: true),
                    ValorPercentual = table.Column<double>(type: "REAL", nullable: true),
                    ValorTexto = table.Column<string>(type: "TEXT", nullable: true),
                    DataCalculo = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PeriodoInicio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PeriodoFim = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Dimensao = table.Column<string>(type: "TEXT", nullable: true),
                    Subdimensao = table.Column<string>(type: "TEXT", nullable: true),
                    ValorAnterior = table.Column<decimal>(type: "TEXT", nullable: true),
                    VariacaoPercentual = table.Column<double>(type: "REAL", nullable: true),
                    DadosJson = table.Column<string>(type: "TEXT", nullable: true),
                    MetadadosJson = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_EstatisticasEleicao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_EstatisticasEleicao_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_EstatisticasEleicao_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "EtapasEleicao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Fase = table.Column<int>(type: "INTEGER", nullable: false),
                    Codigo = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataPrevista = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataFim = table.Column<DateTime>(type: "TEXT", nullable: true),
                    HoraInicio = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    HoraFim = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    Obrigatoria = table.Column<bool>(type: "INTEGER", nullable: false),
                    Concluida = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataConclusao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    EtapaAnteriorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ResponsavelId = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_EtapasEleicao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_EtapasEleicao_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_EtapasEleicao_EtapasEleicao_EtapaAnteriorId",
                        column: x => x.EtapaAnteriorId,
                        principalTable: "EtapasEleicao",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ExportacoesDados",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    SolicitanteId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Formato = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataSolicitacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataConclusao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataExpiracao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Filtros = table.Column<string>(type: "TEXT", nullable: true),
                    Campos = table.Column<string>(type: "TEXT", nullable: true),
                    TotalRegistros = table.Column<int>(type: "INTEGER", nullable: true),
                    RegistrosExportados = table.Column<int>(type: "INTEGER", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    ArquivoHash = table.Column<string>(type: "TEXT", nullable: true),
                    MensagemErro = table.Column<string>(type: "TEXT", nullable: true),
                    DownloadsRealizados = table.Column<int>(type: "INTEGER", nullable: false),
                    LimiteDownloads = table.Column<int>(type: "INTEGER", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ExportacoesDados", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ExportacoesDados_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_ExportacoesDados_Usuarios_SolicitanteId",
                        column: x => x.SolicitanteId,
                        principalTable: "Usuarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ImportacoesDados",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    SolicitanteId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Formato = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataSolicitacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataConclusao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: false),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    ArquivoHash = table.Column<string>(type: "TEXT", nullable: true),
                    TotalRegistros = table.Column<int>(type: "INTEGER", nullable: true),
                    RegistrosProcessados = table.Column<int>(type: "INTEGER", nullable: true),
                    RegistrosSucesso = table.Column<int>(type: "INTEGER", nullable: true),
                    RegistrosErro = table.Column<int>(type: "INTEGER", nullable: true),
                    RegistrosDuplicados = table.Column<int>(type: "INTEGER", nullable: true),
                    Mapeamento = table.Column<string>(type: "TEXT", nullable: true),
                    ConfiguracaoValidacao = table.Column<string>(type: "TEXT", nullable: true),
                    LogErros = table.Column<string>(type: "TEXT", nullable: true),
                    LogProcessamento = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoErrosUrl = table.Column<string>(type: "TEXT", nullable: true),
                    MensagemErro = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ImportacoesDados", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ImportacoesDados_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_ImportacoesDados_Usuarios_SolicitanteId",
                        column: x => x.SolicitanteId,
                        principalTable: "Usuarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "MapasVotacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    DataGeracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Consolidado = table.Column<bool>(type: "INTEGER", nullable: false),
                    TotalZonas = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalSecoes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalUrnas = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalEleitoresAptos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotantes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalAbstencoes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosValidos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosBrancos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosNulos = table.Column<int>(type: "INTEGER", nullable: false),
                    PercentualComparecimento = table.Column<double>(type: "REAL", nullable: false),
                    PercentualAbstencao = table.Column<double>(type: "REAL", nullable: false),
                    DadosJson = table.Column<string>(type: "TEXT", nullable: true),
                    DadosGeoJson = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MapasVotacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MapasVotacao_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_MapasVotacao_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Normativas",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataVigencia = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataRevogacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    NormativaRevogadaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Normativas", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Normativas_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Normativas_Normativas_NormativaRevogadaId",
                        column: x => x.NormativaRevogadaId,
                        principalTable: "Normativas",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Portarias",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataVigencia = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataRevogacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PortariaRevogadaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Portarias", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Portarias_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Portarias_Portarias_PortariaRevogadaId",
                        column: x => x.PortariaRevogadaId,
                        principalTable: "Portarias",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "RegioesPleito",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    CircunscricaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Codigo = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Abrangencia = table.Column<string>(type: "TEXT", nullable: true),
                    UFs = table.Column<string>(type: "TEXT", nullable: true),
                    Municipios = table.Column<string>(type: "TEXT", nullable: true),
                    QuantidadeEleitores = table.Column<int>(type: "INTEGER", nullable: true),
                    QuantidadeVagas = table.Column<int>(type: "INTEGER", nullable: true),
                    QuantidadeSuplentes = table.Column<int>(type: "INTEGER", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RegioesPleito", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RegioesPleito_Circunscricoes_CircunscricaoId",
                        column: x => x.CircunscricaoId,
                        principalTable: "Circunscricoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RegioesPleito_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_RegioesPleito_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "RelatoriosVotacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataGeracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PeriodoInicio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PeriodoFim = table.Column<DateTime>(type: "TEXT", nullable: true),
                    GeradoPorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    TotalEleitoresAptos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotantes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalAbstencoes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosValidos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosBrancos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosNulos = table.Column<int>(type: "INTEGER", nullable: false),
                    PercentualComparecimento = table.Column<double>(type: "REAL", nullable: false),
                    PercentualAbstencao = table.Column<double>(type: "REAL", nullable: false),
                    DadosJson = table.Column<string>(type: "TEXT", nullable: true),
                    Filtros = table.Column<string>(type: "TEXT", nullable: true),
                    Formato = table.Column<int>(type: "INTEGER", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RelatoriosVotacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RelatoriosVotacao_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_RelatoriosVotacao_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RelatoriosVotacao_Usuarios_GeradoPorId",
                        column: x => x.GeradoPorId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Resolucoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataVigencia = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataRevogacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ResolucaoRevogadaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Resolucoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Resolucoes_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Resolucoes_Resolucoes_ResolucaoRevogadaId",
                        column: x => x.ResolucaoRevogadaId,
                        principalTable: "Resolucoes",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ResultadosEleicao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Parcial = table.Column<bool>(type: "INTEGER", nullable: false),
                    Final = table.Column<bool>(type: "INTEGER", nullable: false),
                    TotalEleitoresAptos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotantes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalAbstencoes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosValidos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosBrancos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosNulos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosAnulados = table.Column<int>(type: "INTEGER", nullable: false),
                    PercentualComparecimento = table.Column<double>(type: "REAL", nullable: false),
                    PercentualAbstencao = table.Column<double>(type: "REAL", nullable: false),
                    DataApuracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Publicado = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    AtaApuracaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ResultadosEleicao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ResultadosEleicao_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ResultadosFinais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataApuracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataHomologacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Publicado = table.Column<bool>(type: "INTEGER", nullable: false),
                    Homologado = table.Column<bool>(type: "INTEGER", nullable: false),
                    TotalSecoesApuradas = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalUrnas = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalEleitoresAptos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotantes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalAbstencoes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosValidos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosBrancos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosNulos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosAnulados = table.Column<int>(type: "INTEGER", nullable: false),
                    PercentualComparecimento = table.Column<double>(type: "REAL", nullable: false),
                    PercentualAbstencao = table.Column<double>(type: "REAL", nullable: false),
                    ChapaVencedoraId = table.Column<string>(type: "TEXT", nullable: true),
                    VotosChapaVencedora = table.Column<int>(type: "INTEGER", nullable: true),
                    PercentualChapaVencedora = table.Column<double>(type: "REAL", nullable: true),
                    DadosJson = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    AtaApuracaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ResultadosFinais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ResultadosFinais_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_ResultadosFinais_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ResultadosParciais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    NumeroAtualizacao = table.Column<int>(type: "INTEGER", nullable: false),
                    DataHoraAtualizacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalSecoesApuradas = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalSecoesFaltantes = table.Column<int>(type: "INTEGER", nullable: false),
                    PercentualApurado = table.Column<double>(type: "REAL", nullable: false),
                    TotalEleitoresAptos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotantes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalAbstencoes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosValidos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosBrancos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosNulos = table.Column<int>(type: "INTEGER", nullable: false),
                    PercentualComparecimento = table.Column<double>(type: "REAL", nullable: false),
                    PercentualAbstencao = table.Column<double>(type: "REAL", nullable: false),
                    DadosJson = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ResultadosParciais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ResultadosParciais_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_ResultadosParciais_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "TemplatesDocumento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ModeloId = table.Column<Guid>(type: "TEXT", nullable: true),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Codigo = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    ConteudoHtml = table.Column<string>(type: "TEXT", nullable: true),
                    EstilosCSS = table.Column<string>(type: "TEXT", nullable: true),
                    Cabecalho = table.Column<string>(type: "TEXT", nullable: true),
                    Rodape = table.Column<string>(type: "TEXT", nullable: true),
                    Variaveis = table.Column<string>(type: "TEXT", nullable: true),
                    Versao = table.Column<int>(type: "INTEGER", nullable: true),
                    Personalizado = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TemplatesDocumento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TemplatesDocumento_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_TemplatesDocumento_ModelosDocumento_ModeloId",
                        column: x => x.ModeloId,
                        principalTable: "ModelosDocumento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Termos",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: true),
                    Ano = table.Column<int>(type: "INTEGER", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataValidade = table.Column<DateTime>(type: "TEXT", nullable: true),
                    LocalAssinatura = table.Column<string>(type: "TEXT", nullable: true),
                    IpAssinatura = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Termos", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Termos_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Termos_Usuarios_UsuarioId",
                        column: x => x.UsuarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Profissionais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: true),
                    RegistroCAU = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    NomeCompleto = table.Column<string>(type: "TEXT", nullable: true),
                    Cpf = table.Column<string>(type: "TEXT", nullable: false),
                    Rg = table.Column<string>(type: "TEXT", nullable: true),
                    Email = table.Column<string>(type: "TEXT", nullable: true),
                    Telefone = table.Column<string>(type: "TEXT", nullable: true),
                    Celular = table.Column<string>(type: "TEXT", nullable: true),
                    DataNascimento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Nacionalidade = table.Column<string>(type: "TEXT", nullable: true),
                    Naturalidade = table.Column<string>(type: "TEXT", nullable: true),
                    Endereco = table.Column<string>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: true),
                    Complemento = table.Column<string>(type: "TEXT", nullable: true),
                    Bairro = table.Column<string>(type: "TEXT", nullable: true),
                    Cidade = table.Column<string>(type: "TEXT", nullable: true),
                    UF = table.Column<string>(type: "TEXT", nullable: true),
                    Cep = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataRegistro = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataUltimaAtualizacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    FilialId = table.Column<Guid>(type: "TEXT", nullable: true),
                    EleitorApto = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoInaptidao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Profissionais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Profissionais_Filiais_FilialId",
                        column: x => x.FilialId,
                        principalTable: "Filiais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Profissionais_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Profissionais_Usuarios_UsuarioId",
                        column: x => x.UsuarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "SecoesEleitorais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ZonaEleitoralId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Local = table.Column<string>(type: "TEXT", nullable: true),
                    Endereco = table.Column<string>(type: "TEXT", nullable: true),
                    CapacidadeEleitores = table.Column<int>(type: "INTEGER", nullable: false),
                    Acessivel = table.Column<bool>(type: "INTEGER", nullable: false),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_SecoesEleitorais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_SecoesEleitorais_ZonasEleitorais_ZonaEleitoralId",
                        column: x => x.ZonaEleitoralId,
                        principalTable: "ZonasEleitorais",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AtividadesPrincipaisCalendario",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    CalendarioId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataFim = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Concluida = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataConclusao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AtividadesPrincipaisCalendario", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AtividadesPrincipaisCalendario_Calendarios_CalendarioId",
                        column: x => x.CalendarioId,
                        principalTable: "Calendarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "CalendarioSituacoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    CalendarioId = table.Column<Guid>(type: "TEXT", nullable: false),
                    StatusAnterior = table.Column<int>(type: "INTEGER", nullable: false),
                    StatusNovo = table.Column<int>(type: "INTEGER", nullable: false),
                    DataAlteracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Motivo = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    AlteradoPor = table.Column<string>(type: "TEXT", nullable: true),
                    Ip = table.Column<string>(type: "TEXT", nullable: true),
                    UserAgent = table.Column<string>(type: "TEXT", nullable: true),
                    Automatico = table.Column<bool>(type: "INTEGER", nullable: false),
                    ReferenciaAutomatico = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_CalendarioSituacoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_CalendarioSituacoes_Calendarios_CalendarioId",
                        column: x => x.CalendarioId,
                        principalTable: "Calendarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ComposicoesChapa",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Cargo = table.Column<string>(type: "TEXT", nullable: false),
                    Quantidade = table.Column<int>(type: "INTEGER", nullable: false),
                    Obrigatorio = table.Column<bool>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    QuantidadeTitulares = table.Column<int>(type: "INTEGER", nullable: true),
                    QuantidadeSuplentes = table.Column<int>(type: "INTEGER", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ComposicoesChapa", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ComposicoesChapa_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Diplomas",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Cargo = table.Column<string>(type: "TEXT", nullable: true),
                    Funcao = table.Column<string>(type: "TEXT", nullable: true),
                    Mandato = table.Column<string>(type: "TEXT", nullable: true),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataExpedicao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataEntrega = table.Column<DateTime>(type: "TEXT", nullable: true),
                    LocalExpedicao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    CodigoVerificacao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Diplomas", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Diplomas_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Diplomas_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_Diplomas_Usuarios_UsuarioId",
                        column: x => x.UsuarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "HistoricosChapa",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    StatusAnterior = table.Column<int>(type: "INTEGER", nullable: false),
                    StatusNovo = table.Column<int>(type: "INTEGER", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataAlteracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    AlteradoPor = table.Column<string>(type: "TEXT", nullable: true),
                    Ip = table.Column<string>(type: "TEXT", nullable: true),
                    UserAgent = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_HistoricosChapa", x => x.Id);
                    table.ForeignKey(
                        name: "FK_HistoricosChapa_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "PlataformasEleitorais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Resumo = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Missao = table.Column<string>(type: "TEXT", nullable: true),
                    Visao = table.Column<string>(type: "TEXT", nullable: true),
                    Valores = table.Column<string>(type: "TEXT", nullable: true),
                    PropostasJson = table.Column<string>(type: "TEXT", nullable: true),
                    MetasJson = table.Column<string>(type: "TEXT", nullable: true),
                    EixosJson = table.Column<string>(type: "TEXT", nullable: true),
                    VideoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ApresentacaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ConteudoCompleto = table.Column<string>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataAtualizacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Publicada = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PlataformasEleitorais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PlataformasEleitorais_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "TermosPosse",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    UsuarioId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Cargo = table.Column<string>(type: "TEXT", nullable: true),
                    Funcao = table.Column<string>(type: "TEXT", nullable: true),
                    Mandato = table.Column<string>(type: "TEXT", nullable: true),
                    DataDocumento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPosse = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataInicioMandato = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataFimMandato = table.Column<DateTime>(type: "TEXT", nullable: true),
                    LocalPosse = table.Column<string>(type: "TEXT", nullable: true),
                    IpAssinatura = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TermosPosse", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TermosPosse_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_TermosPosse_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_TermosPosse_Usuarios_UsuarioId",
                        column: x => x.UsuarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "SessoesJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ComissaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataSessao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    HoraInicio = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    HoraFim = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    Local = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    ConvocacaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    DataConvocacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_SessoesJulgamento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_SessoesJulgamento_ComissoesJulgadoras_ComissaoId",
                        column: x => x.ComissaoId,
                        principalTable: "ComissoesJulgadoras",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ArquivosDocumento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DocumentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: false),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTipo = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    DataUpload = table.Column<DateTime>(type: "TEXT", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ArquivosDocumento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ArquivosDocumento_Documentos_DocumentoId",
                        column: x => x.DocumentoId,
                        principalTable: "Documentos",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Publicacoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DocumentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    DataAgendada = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Veiculo = table.Column<string>(type: "TEXT", nullable: true),
                    Edicao = table.Column<string>(type: "TEXT", nullable: true),
                    Pagina = table.Column<string>(type: "TEXT", nullable: true),
                    LinkPublicacao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Publicacoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Publicacoes_Documentos_DocumentoId",
                        column: x => x.DocumentoId,
                        principalTable: "Documentos",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "PublicacoesOficiais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DocumentoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    NumeroPublicacao = table.Column<string>(type: "TEXT", nullable: true),
                    AnoPublicacao = table.Column<int>(type: "INTEGER", nullable: true),
                    DataAgendada = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataConfirmacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    VeiculoPublicacao = table.Column<string>(type: "TEXT", nullable: true),
                    Edicao = table.Column<string>(type: "TEXT", nullable: true),
                    Secao = table.Column<string>(type: "TEXT", nullable: true),
                    Pagina = table.Column<string>(type: "TEXT", nullable: true),
                    LinkPublicacao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    AtoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    AvisoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ConvocacaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DeliberacaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    NormativaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    PortariaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ResolucaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PublicacoesOficiais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PublicacoesOficiais_Atos_AtoId",
                        column: x => x.AtoId,
                        principalTable: "Atos",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_PublicacoesOficiais_Avisos_AvisoId",
                        column: x => x.AvisoId,
                        principalTable: "Avisos",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_PublicacoesOficiais_Convocacoes_ConvocacaoId",
                        column: x => x.ConvocacaoId,
                        principalTable: "Convocacoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_PublicacoesOficiais_Deliberacoes_DeliberacaoId",
                        column: x => x.DeliberacaoId,
                        principalTable: "Deliberacoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_PublicacoesOficiais_Documentos_DocumentoId",
                        column: x => x.DocumentoId,
                        principalTable: "Documentos",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_PublicacoesOficiais_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_PublicacoesOficiais_Normativas_NormativaId",
                        column: x => x.NormativaId,
                        principalTable: "Normativas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_PublicacoesOficiais_Portarias_PortariaId",
                        column: x => x.PortariaId,
                        principalTable: "Portarias",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_PublicacoesOficiais_Resolucoes_ResolucaoId",
                        column: x => x.ResolucaoId,
                        principalTable: "Resolucoes",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "AtasApuracao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ResultadoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataApuracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    HoraInicio = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    HoraFim = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    Local = table.Column<string>(type: "TEXT", nullable: true),
                    ComissaoApuradora = table.Column<string>(type: "TEXT", nullable: true),
                    Presidente = table.Column<string>(type: "TEXT", nullable: true),
                    Secretario = table.Column<string>(type: "TEXT", nullable: true),
                    Membros = table.Column<string>(type: "TEXT", nullable: true),
                    TotalUrnas = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosApurados = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosValidos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosBrancos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosNulos = table.Column<int>(type: "INTEGER", nullable: false),
                    Ocorrencias = table.Column<string>(type: "TEXT", nullable: true),
                    Impugnacoes = table.Column<string>(type: "TEXT", nullable: true),
                    Decisoes = table.Column<string>(type: "TEXT", nullable: true),
                    DataHomologacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AtasApuracao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AtasApuracao_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_AtasApuracao_ResultadosEleicao_ResultadoId",
                        column: x => x.ResultadoId,
                        principalTable: "ResultadosEleicao",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "GraficosResultado",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ResultadoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    DataGeracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    ConfiguracaoJson = table.Column<string>(type: "TEXT", nullable: true),
                    DadosJson = table.Column<string>(type: "TEXT", nullable: true),
                    LabelsJson = table.Column<string>(type: "TEXT", nullable: true),
                    CoresJson = table.Column<string>(type: "TEXT", nullable: true),
                    Largura = table.Column<int>(type: "INTEGER", nullable: true),
                    Altura = table.Column<int>(type: "INTEGER", nullable: true),
                    ImagemUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ImagemBase64 = table.Column<string>(type: "TEXT", nullable: true),
                    Publicado = table.Column<bool>(type: "INTEGER", nullable: false),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_GraficosResultado", x => x.Id);
                    table.ForeignKey(
                        name: "FK_GraficosResultado_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_GraficosResultado_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_GraficosResultado_ResultadosEleicao_ResultadoId",
                        column: x => x.ResultadoId,
                        principalTable: "ResultadosEleicao",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Conselheiros",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ProfissionalId = table.Column<Guid>(type: "TEXT", nullable: false),
                    NumeroConselheiro = table.Column<string>(type: "TEXT", nullable: true),
                    Cargo = table.Column<string>(type: "TEXT", nullable: true),
                    Comissao = table.Column<string>(type: "TEXT", nullable: true),
                    InicioMandato = table.Column<DateTime>(type: "TEXT", nullable: true),
                    FimMandato = table.Column<DateTime>(type: "TEXT", nullable: true),
                    MandatoAtivo = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoFinalizacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataFinalizacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Conselheiros", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Conselheiros_Profissionais_ProfissionalId",
                        column: x => x.ProfissionalId,
                        principalTable: "Profissionais",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "MembrosChapa",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ProfissionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Cpf = table.Column<string>(type: "TEXT", nullable: true),
                    RegistroCAU = table.Column<string>(type: "TEXT", nullable: true),
                    Email = table.Column<string>(type: "TEXT", nullable: true),
                    Telefone = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Cargo = table.Column<string>(type: "TEXT", nullable: true),
                    Titular = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataConfirmacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    TokenConfirmacao = table.Column<string>(type: "TEXT", nullable: true),
                    TokenConfirmacaoExpiracao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    MotivoRecusa = table.Column<string>(type: "TEXT", nullable: true),
                    MotivoInabilitacao = table.Column<string>(type: "TEXT", nullable: true),
                    FotoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CurriculoResumo = table.Column<string>(type: "TEXT", nullable: true),
                    SubstituidoPorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DataSubstituicao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    MotivoSubstituicao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MembrosChapa", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MembrosChapa_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_MembrosChapa_MembrosChapa_SubstituidoPorId",
                        column: x => x.SubstituidoPorId,
                        principalTable: "MembrosChapa",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_MembrosChapa_Profissionais_ProfissionalId",
                        column: x => x.ProfissionalId,
                        principalTable: "Profissionais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "BoletinsUrna",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SecaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ZonaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    NumeroUrna = table.Column<string>(type: "TEXT", nullable: true),
                    CodigoIdentificacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataGeracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    HoraAbertura = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    HoraEncerramento = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    TotalEleitoresAptos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalComparecimento = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalAbstencao = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosValidos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosBrancos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosNulos = table.Column<int>(type: "INTEGER", nullable: false),
                    PercentualComparecimento = table.Column<double>(type: "REAL", nullable: false),
                    PercentualAbstencao = table.Column<double>(type: "REAL", nullable: false),
                    HashVerificacao = table.Column<string>(type: "TEXT", nullable: true),
                    Assinaturas = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_BoletinsUrna", x => x.Id);
                    table.ForeignKey(
                        name: "FK_BoletinsUrna_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_BoletinsUrna_SecoesEleitorais_SecaoId",
                        column: x => x.SecaoId,
                        principalTable: "SecoesEleitorais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_BoletinsUrna_ZonasEleitorais_ZonaId",
                        column: x => x.ZonaId,
                        principalTable: "ZonasEleitorais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Eleitores",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ProfissionalId = table.Column<Guid>(type: "TEXT", nullable: false),
                    NumeroInscricao = table.Column<string>(type: "TEXT", nullable: true),
                    Apto = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoInaptidao = table.Column<string>(type: "TEXT", nullable: true),
                    Votou = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataVoto = table.Column<DateTime>(type: "TEXT", nullable: true),
                    SecaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ComprovanteVotacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Eleitores", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Eleitores_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_Eleitores_Profissionais_ProfissionalId",
                        column: x => x.ProfissionalId,
                        principalTable: "Profissionais",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_Eleitores_SecoesEleitorais_SecaoId",
                        column: x => x.SecaoId,
                        principalTable: "SecoesEleitorais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "RegistrosApuracaoVotos",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SecaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ZonaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    NumeroSequencial = table.Column<int>(type: "INTEGER", nullable: false),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataFim = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Duracao = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    ResponsavelId = table.Column<string>(type: "TEXT", nullable: true),
                    EquipeApuracao = table.Column<string>(type: "TEXT", nullable: true),
                    TotalVotosApurados = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosValidos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosBrancos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosNulos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosAnulados = table.Column<int>(type: "INTEGER", nullable: false),
                    DiscrepanciasEncontradas = table.Column<int>(type: "INTEGER", nullable: true),
                    DescricaoDiscrepancias = table.Column<string>(type: "TEXT", nullable: true),
                    Auditada = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataAuditoria = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ResultadoAuditoria = table.Column<string>(type: "TEXT", nullable: true),
                    HashVerificacao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RegistrosApuracaoVotos", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RegistrosApuracaoVotos_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_RegistrosApuracaoVotos_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RegistrosApuracaoVotos_SecoesEleitorais_SecaoId",
                        column: x => x.SecaoId,
                        principalTable: "SecoesEleitorais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RegistrosApuracaoVotos_ZonasEleitorais_ZonaId",
                        column: x => x.ZonaId,
                        principalTable: "ZonasEleitorais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "UrnasEletronicas",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    RegiaoPleitoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    SecaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    NumeroSerie = table.Column<string>(type: "TEXT", nullable: false),
                    Codigo = table.Column<string>(type: "TEXT", nullable: false),
                    Modelo = table.Column<string>(type: "TEXT", nullable: true),
                    Versao = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    DataInstalacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataAtivacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataDesativacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    HashInicial = table.Column<string>(type: "TEXT", nullable: true),
                    HashFinal = table.Column<string>(type: "TEXT", nullable: true),
                    ChavePublica = table.Column<string>(type: "TEXT", nullable: true),
                    TotalVotosRegistrados = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosConfirmados = table.Column<int>(type: "INTEGER", nullable: false),
                    Ip = table.Column<string>(type: "TEXT", nullable: true),
                    MacAddress = table.Column<string>(type: "TEXT", nullable: true),
                    Localizacao = table.Column<string>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_UrnasEletronicas", x => x.Id);
                    table.ForeignKey(
                        name: "FK_UrnasEletronicas_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_UrnasEletronicas_RegioesPleito_RegiaoPleitoId",
                        column: x => x.RegiaoPleitoId,
                        principalTable: "RegioesPleito",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_UrnasEletronicas_SecoesEleitorais_SecaoId",
                        column: x => x.SecaoId,
                        principalTable: "SecoesEleitorais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "AtividadesSecundariasCalendario",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    CalendarioId = table.Column<Guid>(type: "TEXT", nullable: false),
                    AtividadePrincipalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataFim = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Concluida = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataConclusao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AtividadesSecundariasCalendario", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AtividadesSecundariasCalendario_AtividadesPrincipaisCalendario_AtividadePrincipalId",
                        column: x => x.AtividadePrincipalId,
                        principalTable: "AtividadesPrincipaisCalendario",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AtividadesSecundariasCalendario_Calendarios_CalendarioId",
                        column: x => x.CalendarioId,
                        principalTable: "Calendarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AtasSessao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: true),
                    DataAprovacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Aprovada = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    AssinaturaUrl = table.Column<string>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Publicada = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AtasSessao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AtasSessao_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "JulgamentosFinais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    NumeroProcessoOrigem = table.Column<string>(type: "TEXT", nullable: true),
                    Partes = table.Column<string>(type: "TEXT", nullable: true),
                    Assunto = table.Column<string>(type: "TEXT", nullable: true),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Relatorio = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Dispositivo = table.Column<string>(type: "TEXT", nullable: true),
                    TipoDecisao = table.Column<int>(type: "INTEGER", nullable: true),
                    DataJulgamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    AcordaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CertidaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_JulgamentosFinais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_JulgamentosFinais_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_JulgamentosFinais_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "AssinaturasDigitais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DocumentoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    TermoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    TermoPosseId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DiplomaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    CertificadoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DeclaracaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    AtaReuniaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    AtaApuracaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    SignatarioId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    NomeSignatario = table.Column<string>(type: "TEXT", nullable: true),
                    CpfSignatario = table.Column<string>(type: "TEXT", nullable: true),
                    EmailSignatario = table.Column<string>(type: "TEXT", nullable: true),
                    CargoSignatario = table.Column<string>(type: "TEXT", nullable: true),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataValidadeAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    HashDocumento = table.Column<string>(type: "TEXT", nullable: true),
                    AssinaturaCriptografada = table.Column<string>(type: "TEXT", nullable: true),
                    CertificadoBase64 = table.Column<string>(type: "TEXT", nullable: true),
                    CertificadoDigitalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    CarimboTempoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    IpAssinatura = table.Column<string>(type: "TEXT", nullable: true),
                    UserAgentAssinatura = table.Column<string>(type: "TEXT", nullable: true),
                    Geolocalizacao = table.Column<string>(type: "TEXT", nullable: true),
                    MotivoRecusa = table.Column<string>(type: "TEXT", nullable: true),
                    DataRecusa = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Validada = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataValidacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ResultadoValidacao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    CertificadoDigitalId1 = table.Column<Guid>(type: "TEXT", nullable: true),
                    ResultadoFinalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AssinaturasDigitais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_AtasApuracao_AtaApuracaoId",
                        column: x => x.AtaApuracaoId,
                        principalTable: "AtasApuracao",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_AtasReuniao_AtaReuniaoId",
                        column: x => x.AtaReuniaoId,
                        principalTable: "AtasReuniao",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_CarimbosTempo_CarimboTempoId",
                        column: x => x.CarimboTempoId,
                        principalTable: "CarimbosTempo",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.SetNull);
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_CertificadosDigitais_CertificadoDigitalId",
                        column: x => x.CertificadoDigitalId,
                        principalTable: "CertificadosDigitais",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.SetNull);
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_CertificadosDigitais_CertificadoDigitalId1",
                        column: x => x.CertificadoDigitalId1,
                        principalTable: "CertificadosDigitais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_Certificados_CertificadoId",
                        column: x => x.CertificadoId,
                        principalTable: "Certificados",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_Declaracoes_DeclaracaoId",
                        column: x => x.DeclaracaoId,
                        principalTable: "Declaracoes",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_Diplomas_DiplomaId",
                        column: x => x.DiplomaId,
                        principalTable: "Diplomas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_Documentos_DocumentoId",
                        column: x => x.DocumentoId,
                        principalTable: "Documentos",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_ResultadosFinais_ResultadoFinalId",
                        column: x => x.ResultadoFinalId,
                        principalTable: "ResultadosFinais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_TermosPosse_TermoPosseId",
                        column: x => x.TermoPosseId,
                        principalTable: "TermosPosse",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_Termos_TermoId",
                        column: x => x.TermoId,
                        principalTable: "Termos",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_AssinaturasDigitais_Usuarios_SignatarioId",
                        column: x => x.SignatarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "HistoricosExtratoConselheiro",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ConselheiroId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    DataEvento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    Documento = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_HistoricosExtratoConselheiro", x => x.Id);
                    table.ForeignKey(
                        name: "FK_HistoricosExtratoConselheiro_Conselheiros_ConselheiroId",
                        column: x => x.ConselheiroId,
                        principalTable: "Conselheiros",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "MembrosComissaoJulgadora",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ComissaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ConselheiroId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataFim = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoAfastamento = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MembrosComissaoJulgadora", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MembrosComissaoJulgadora_ComissoesJulgadoras_ComissaoId",
                        column: x => x.ComissaoId,
                        principalTable: "ComissoesJulgadoras",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_MembrosComissaoJulgadora_Conselheiros_ConselheiroId",
                        column: x => x.ConselheiroId,
                        principalTable: "Conselheiros",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "PareceristaProcuradores",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    ProcuradorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    NomeProcurador = table.Column<string>(type: "TEXT", nullable: false),
                    RegistroProfissional = table.Column<string>(type: "TEXT", nullable: true),
                    OrgaoOrigem = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataDistribuicao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPrazo = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataConclusao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    ResumoFatos = table.Column<string>(type: "TEXT", nullable: true),
                    AnaliseLegal = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Conclusao = table.Column<string>(type: "TEXT", nullable: true),
                    Recomendacao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoParecerUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Assinado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Homologado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataHomologacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    HomologadoPor = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PareceristaProcuradores", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PareceristaProcuradores_Conselheiros_ProcuradorId",
                        column: x => x.ProcuradorId,
                        principalTable: "Conselheiros",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ConfirmacoesMembrosChapa",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    MembroId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Token = table.Column<string>(type: "TEXT", nullable: false),
                    TokenExpiracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Confirmado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataConfirmacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    IpConfirmacao = table.Column<string>(type: "TEXT", nullable: true),
                    UserAgentConfirmacao = table.Column<string>(type: "TEXT", nullable: true),
                    Recusado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataRecusa = table.Column<DateTime>(type: "TEXT", nullable: true),
                    MotivoRecusa = table.Column<string>(type: "TEXT", nullable: true),
                    TentativasEnvio = table.Column<int>(type: "INTEGER", nullable: false),
                    UltimoEnvio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ConfirmacoesMembrosChapa", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ConfirmacoesMembrosChapa_MembrosChapa_MembroId",
                        column: x => x.MembroId,
                        principalTable: "MembrosChapa",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Denuncias",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    MembroId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DenuncianteId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Anonima = table.Column<bool>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataDenuncia = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataRecebimento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PrazoDefesa = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PrazoRecurso = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Denuncias", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Denuncias_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Denuncias_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_Denuncias_MembrosChapa_MembroId",
                        column: x => x.MembroId,
                        principalTable: "MembrosChapa",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Denuncias_Profissionais_DenuncianteId",
                        column: x => x.DenuncianteId,
                        principalTable: "Profissionais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "DocumentosChapa",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    MembroId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: false),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTipo = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    DataEnvio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataAnalise = table.Column<DateTime>(type: "TEXT", nullable: true),
                    AnalisadoPor = table.Column<string>(type: "TEXT", nullable: true),
                    AnalistaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    MotivoRejeicao = table.Column<string>(type: "TEXT", nullable: true),
                    ParecerAnalise = table.Column<string>(type: "TEXT", nullable: true),
                    Obrigatorio = table.Column<bool>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_DocumentosChapa", x => x.Id);
                    table.ForeignKey(
                        name: "FK_DocumentosChapa_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_DocumentosChapa_MembrosChapa_MembroId",
                        column: x => x.MembroId,
                        principalTable: "MembrosChapa",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Impugnacoes",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    ChapaImpugnanteId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ChapaImpugnadaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    MembroImpugnadoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ImpugnanteId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataImpugnacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataRecebimento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PrazoAlegacoes = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PrazoContraAlegacoes = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Impugnacoes", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Impugnacoes_Chapas_ChapaImpugnadaId",
                        column: x => x.ChapaImpugnadaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Impugnacoes_Chapas_ChapaImpugnanteId",
                        column: x => x.ChapaImpugnanteId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Impugnacoes_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_Impugnacoes_MembrosChapa_MembroImpugnadoId",
                        column: x => x.MembroImpugnadoId,
                        principalTable: "MembrosChapa",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Impugnacoes_Profissionais_ImpugnanteId",
                        column: x => x.ImpugnanteId,
                        principalTable: "Profissionais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "SubstituicoesMembrosChapa",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    MembroAntigoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    MembroNovoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Motivo = table.Column<string>(type: "TEXT", nullable: false),
                    DataSubstituicao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Aprovada = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataAprovacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    AprovadoPor = table.Column<string>(type: "TEXT", nullable: true),
                    MotivoReprovacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_SubstituicoesMembrosChapa", x => x.Id);
                    table.ForeignKey(
                        name: "FK_SubstituicoesMembrosChapa_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_SubstituicoesMembrosChapa_MembrosChapa_MembroAntigoId",
                        column: x => x.MembroAntigoId,
                        principalTable: "MembrosChapa",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_SubstituicoesMembrosChapa_MembrosChapa_MembroNovoId",
                        column: x => x.MembroNovoId,
                        principalTable: "MembrosChapa",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "VotosChapa",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ResultadoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TotalVotos = table.Column<int>(type: "INTEGER", nullable: false),
                    Percentual = table.Column<double>(type: "REAL", nullable: false),
                    Posicao = table.Column<int>(type: "INTEGER", nullable: false),
                    Eleita = table.Column<bool>(type: "INTEGER", nullable: false),
                    BoletimUrnaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ResultadoFinalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ResultadoParcialId = table.Column<Guid>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotosChapa", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotosChapa_BoletinsUrna_BoletimUrnaId",
                        column: x => x.BoletimUrnaId,
                        principalTable: "BoletinsUrna",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosChapa_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotosChapa_ResultadosEleicao_ResultadoId",
                        column: x => x.ResultadoId,
                        principalTable: "ResultadosEleicao",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotosChapa_ResultadosFinais_ResultadoFinalId",
                        column: x => x.ResultadoFinalId,
                        principalTable: "ResultadosFinais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosChapa_ResultadosParciais_ResultadoParcialId",
                        column: x => x.ResultadoParcialId,
                        principalTable: "ResultadosParciais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "RelatoriosApuracao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ApuracaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataGeracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    GeradoPorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    TotalSecoesApuradas = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalSecoesPendentes = table.Column<int>(type: "INTEGER", nullable: false),
                    PercentualApurado = table.Column<double>(type: "REAL", nullable: false),
                    TotalVotosApurados = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosValidos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosBrancos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosNulos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosAnulados = table.Column<int>(type: "INTEGER", nullable: false),
                    TempoApuracao = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    DadosJson = table.Column<string>(type: "TEXT", nullable: true),
                    ResumoChapas = table.Column<string>(type: "TEXT", nullable: true),
                    Formato = table.Column<int>(type: "INTEGER", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RelatoriosApuracao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RelatoriosApuracao_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_RelatoriosApuracao_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RelatoriosApuracao_RegistrosApuracaoVotos_ApuracaoId",
                        column: x => x.ApuracaoId,
                        principalTable: "RegistrosApuracaoVotos",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RelatoriosApuracao_Usuarios_GeradoPorId",
                        column: x => x.GeradoPorId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "TotaisVotos",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ApuracaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    QuantidadeVotos = table.Column<int>(type: "INTEGER", nullable: false),
                    Percentual = table.Column<double>(type: "REAL", nullable: false),
                    VotoBranco = table.Column<bool>(type: "INTEGER", nullable: false),
                    VotoNulo = table.Column<bool>(type: "INTEGER", nullable: false),
                    VotoAnulado = table.Column<bool>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TotaisVotos", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TotaisVotos_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_TotaisVotos_RegistrosApuracaoVotos_ApuracaoId",
                        column: x => x.ApuracaoId,
                        principalTable: "RegistrosApuracaoVotos",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "VotosBrancos",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SecaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ZonaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ApuracaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Quantidade = table.Column<int>(type: "INTEGER", nullable: false),
                    Percentual = table.Column<double>(type: "REAL", nullable: false),
                    DataRegistro = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotosBrancos", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotosBrancos_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotosBrancos_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosBrancos_RegistrosApuracaoVotos_ApuracaoId",
                        column: x => x.ApuracaoId,
                        principalTable: "RegistrosApuracaoVotos",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosBrancos_SecoesEleitorais_SecaoId",
                        column: x => x.SecaoId,
                        principalTable: "SecoesEleitorais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosBrancos_ZonasEleitorais_ZonaId",
                        column: x => x.ZonaId,
                        principalTable: "ZonasEleitorais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "VotosNulos",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SecaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ZonaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    RegionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ApuracaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Quantidade = table.Column<int>(type: "INTEGER", nullable: false),
                    Percentual = table.Column<double>(type: "REAL", nullable: false),
                    Motivo = table.Column<string>(type: "TEXT", nullable: true),
                    DataRegistro = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotosNulos", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotosNulos_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotosNulos_RegionaisCAU_RegionalId",
                        column: x => x.RegionalId,
                        principalTable: "RegionaisCAU",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosNulos_RegistrosApuracaoVotos_ApuracaoId",
                        column: x => x.ApuracaoId,
                        principalTable: "RegistrosApuracaoVotos",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosNulos_SecoesEleitorais_SecaoId",
                        column: x => x.SecaoId,
                        principalTable: "SecoesEleitorais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosNulos_ZonasEleitorais_ZonaId",
                        column: x => x.ZonaId,
                        principalTable: "ZonasEleitorais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ApuracaoResultados",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    RegiaoPleitoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    SecaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    UrnaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataFim = table.Column<DateTime>(type: "TEXT", nullable: true),
                    TotalEleitoresAptos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotantes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalAbstencoes = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosValidos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosNulos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosBrancos = table.Column<int>(type: "INTEGER", nullable: false),
                    TotalVotosAnulados = table.Column<int>(type: "INTEGER", nullable: false),
                    PercentualParticipacao = table.Column<decimal>(type: "TEXT", nullable: false),
                    PercentualAbstencao = table.Column<decimal>(type: "TEXT", nullable: false),
                    ChapaVencedoraId = table.Column<Guid>(type: "TEXT", nullable: true),
                    VotosChapaVencedora = table.Column<int>(type: "INTEGER", nullable: true),
                    HashApuracao = table.Column<string>(type: "TEXT", nullable: true),
                    AssinaturaDigital = table.Column<string>(type: "TEXT", nullable: true),
                    Parcial = table.Column<bool>(type: "INTEGER", nullable: false),
                    PercentualApurado = table.Column<int>(type: "INTEGER", nullable: true),
                    Homologada = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataHomologacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    HomologadoPor = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ApuracaoResultados", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ApuracaoResultados_Chapas_ChapaVencedoraId",
                        column: x => x.ChapaVencedoraId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_ApuracaoResultados_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_ApuracaoResultados_RegioesPleito_RegiaoPleitoId",
                        column: x => x.RegiaoPleitoId,
                        principalTable: "RegioesPleito",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_ApuracaoResultados_SecoesEleitorais_SecaoId",
                        column: x => x.SecaoId,
                        principalTable: "SecoesEleitorais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_ApuracaoResultados_UrnasEletronicas_UrnaId",
                        column: x => x.UrnaId,
                        principalTable: "UrnasEletronicas",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "MesasReceptoras",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    RegiaoPleitoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    SecaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Local = table.Column<string>(type: "TEXT", nullable: true),
                    Endereco = table.Column<string>(type: "TEXT", nullable: true),
                    Sala = table.Column<string>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataInstalacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataAbertura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataEncerramento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    HoraAbertura = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    HoraEncerramento = table.Column<TimeSpan>(type: "TEXT", nullable: true),
                    CapacidadeEleitores = table.Column<int>(type: "INTEGER", nullable: true),
                    TotalEleitoresVotaram = table.Column<int>(type: "INTEGER", nullable: false),
                    PresidenteId = table.Column<Guid>(type: "TEXT", nullable: true),
                    SecretarioId = table.Column<Guid>(type: "TEXT", nullable: true),
                    UrnaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Acessivel = table.Column<bool>(type: "INTEGER", nullable: false),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    Ativo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MesasReceptoras", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MesasReceptoras_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_MesasReceptoras_RegioesPleito_RegiaoPleitoId",
                        column: x => x.RegiaoPleitoId,
                        principalTable: "RegioesPleito",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_MesasReceptoras_SecoesEleitorais_SecaoId",
                        column: x => x.SecaoId,
                        principalTable: "SecoesEleitorais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_MesasReceptoras_UrnasEletronicas_UrnaId",
                        column: x => x.UrnaId,
                        principalTable: "UrnasEletronicas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_MesasReceptoras_Usuarios_PresidenteId",
                        column: x => x.PresidenteId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_MesasReceptoras_Usuarios_SecretarioId",
                        column: x => x.SecretarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "Votos",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Modo = table.Column<int>(type: "INTEGER", nullable: false),
                    HashEleitor = table.Column<string>(type: "TEXT", nullable: false),
                    HashVoto = table.Column<string>(type: "TEXT", nullable: false),
                    DataVoto = table.Column<DateTime>(type: "TEXT", nullable: false),
                    IpAddress = table.Column<string>(type: "TEXT", nullable: true),
                    UserAgent = table.Column<string>(type: "TEXT", nullable: true),
                    SecaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    UrnaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Comprovante = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Votos", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Votos_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Votos_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_Votos_SecoesEleitorais_SecaoId",
                        column: x => x.SecaoId,
                        principalTable: "SecoesEleitorais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Votos_UrnasEletronicas_UrnaId",
                        column: x => x.UrnaId,
                        principalTable: "UrnasEletronicas",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "AlegacoesFinais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoFinalId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Parte = table.Column<string>(type: "TEXT", nullable: true),
                    Representante = table.Column<string>(type: "TEXT", nullable: true),
                    DataProtocolo = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPrazoFinal = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: true),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Pedido = table.Column<string>(type: "TEXT", nullable: true),
                    Tempestiva = table.Column<bool>(type: "INTEGER", nullable: false),
                    ObservacaoTempestividade = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AlegacoesFinais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AlegacoesFinais_JulgamentosFinais_JulgamentoFinalId",
                        column: x => x.JulgamentoFinalId,
                        principalTable: "JulgamentosFinais",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AcordaosJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Ano = table.Column<int>(type: "INTEGER", nullable: false),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Relatorio = table.Column<string>(type: "TEXT", nullable: true),
                    Voto = table.Column<string>(type: "TEXT", nullable: true),
                    Acordao = table.Column<string>(type: "TEXT", nullable: true),
                    RelatorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DataJulgamento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    AssinaturaUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Publicado = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AcordaosJulgamento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AcordaosJulgamento_MembrosComissaoJulgadora_RelatorId",
                        column: x => x.RelatorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ArquivamentosJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Motivo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    SolicitadoPorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DataSolicitacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataAnalise = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataArquivamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Justificativa = table.Column<string>(type: "TEXT", nullable: true),
                    FundamentoLegal = table.Column<string>(type: "TEXT", nullable: true),
                    ParecerAnalise = table.Column<string>(type: "TEXT", nullable: true),
                    MotivoIndeferimento = table.Column<string>(type: "TEXT", nullable: true),
                    AnalisadoPorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Definitivo = table.Column<bool>(type: "INTEGER", nullable: false),
                    Reativavel = table.Column<bool>(type: "INTEGER", nullable: false),
                    PrazoReativacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CondicoesReativacao = table.Column<string>(type: "TEXT", nullable: true),
                    Reativado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataReativacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    MotivoReativacao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoSolicitacaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoDecisaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    LocalArquivamentoFisico = table.Column<string>(type: "TEXT", nullable: true),
                    NumeroArquivamento = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ArquivamentosJulgamento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ArquivamentosJulgamento_MembrosComissaoJulgadora_AnalisadoPorId",
                        column: x => x.AnalisadoPorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_ArquivamentosJulgamento_MembrosComissaoJulgadora_SolicitadoPorId",
                        column: x => x.SolicitadoPorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "DiligenciasJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DeterminadaPorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DataDeterminacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPrazo = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoDias = table.Column<int>(type: "INTEGER", nullable: false),
                    DataCumprimento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Destinatario = table.Column<string>(type: "TEXT", nullable: true),
                    Objeto = table.Column<string>(type: "TEXT", nullable: true),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    FundamentoLegal = table.Column<string>(type: "TEXT", nullable: true),
                    Instrucoes = table.Column<string>(type: "TEXT", nullable: true),
                    ResultadoCumprimento = table.Column<string>(type: "TEXT", nullable: true),
                    MotivoNaoCumprimento = table.Column<string>(type: "TEXT", nullable: true),
                    Prorrogada = table.Column<bool>(type: "INTEGER", nullable: false),
                    DiasProrrogacao = table.Column<int>(type: "INTEGER", nullable: true),
                    NovoPrazo = table.Column<DateTime>(type: "TEXT", nullable: true),
                    MotivoProrrogacao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoDeterminacaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoRespostaUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_DiligenciasJulgamento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_DiligenciasJulgamento_MembrosComissaoJulgadora_DeterminadaPorId",
                        column: x => x.DeterminadaPorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "EmendasJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    ProponenteId = table.Column<Guid>(type: "TEXT", nullable: false),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataVotacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: true),
                    TextoOriginal = table.Column<string>(type: "TEXT", nullable: true),
                    TextoProposto = table.Column<string>(type: "TEXT", nullable: true),
                    Justificativa = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    VotosFavoraveis = table.Column<int>(type: "INTEGER", nullable: false),
                    VotosContrarios = table.Column<int>(type: "INTEGER", nullable: false),
                    Abstencoes = table.Column<int>(type: "INTEGER", nullable: false),
                    ResultadoVotacao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_EmendasJulgamento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_EmendasJulgamento_MembrosComissaoJulgadora_ProponenteId",
                        column: x => x.ProponenteId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_EmendasJulgamento_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ObservacoesJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    DataObservacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Autor = table.Column<string>(type: "TEXT", nullable: true),
                    CargoAutor = table.Column<string>(type: "TEXT", nullable: true),
                    MembroComissaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Categoria = table.Column<string>(type: "TEXT", nullable: true),
                    Interna = table.Column<bool>(type: "INTEGER", nullable: false),
                    Confidencial = table.Column<bool>(type: "INTEGER", nullable: false),
                    ObservacaoRelacionadaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ArquivoAnexoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ObservacoesJulgamento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ObservacoesJulgamento_MembrosComissaoJulgadora_MembroComissaoId",
                        column: x => x.MembroComissaoId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_ObservacoesJulgamento_ObservacoesJulgamento_ObservacaoRelacionadaId",
                        column: x => x.ObservacaoRelacionadaId,
                        principalTable: "ObservacoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "PautasSessao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    TipoProcesso = table.Column<int>(type: "INTEGER", nullable: false),
                    NumeroProcesso = table.Column<string>(type: "TEXT", nullable: false),
                    Partes = table.Column<string>(type: "TEXT", nullable: true),
                    Assunto = table.Column<string>(type: "TEXT", nullable: true),
                    RelatorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Julgado = table.Column<bool>(type: "INTEGER", nullable: false),
                    Adiado = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoAdiamento = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PautasSessao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PautasSessao_MembrosComissaoJulgadora_RelatorId",
                        column: x => x.RelatorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_PautasSessao_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ProvasJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Parte = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataAnalise = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: true),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Objetivo = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    ParecerAdmissibilidade = table.Column<string>(type: "TEXT", nullable: true),
                    MotivoInadmissao = table.Column<string>(type: "TEXT", nullable: true),
                    AnalisadoPorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    TamanhoArquivo = table.Column<long>(type: "INTEGER", nullable: true),
                    TipoArquivo = table.Column<string>(type: "TEXT", nullable: true),
                    HashArquivo = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ProvasJulgamento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ProvasJulgamento_MembrosComissaoJulgadora_AnalisadoPorId",
                        column: x => x.AnalisadoPorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "RelatoriosJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    RelatorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DataElaboracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataAprovacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    HistoricoProcessual = table.Column<string>(type: "TEXT", nullable: true),
                    ResumoFatos = table.Column<string>(type: "TEXT", nullable: true),
                    AnalisePreliminar = table.Column<string>(type: "TEXT", nullable: true),
                    AnaliseMerito = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Conclusao = table.Column<string>(type: "TEXT", nullable: true),
                    Proposta = table.Column<string>(type: "TEXT", nullable: true),
                    Aprovado = table.Column<bool>(type: "INTEGER", nullable: false),
                    ObservacaoAprovacao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Assinado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RelatoriosJulgamento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RelatoriosJulgamento_MembrosComissaoJulgadora_RelatorId",
                        column: x => x.RelatorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "SubstituicoesJulgamentoFinal",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoFinalId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    DataSolicitacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataAnalise = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataEfetivacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Justificativa = table.Column<string>(type: "TEXT", nullable: true),
                    DescricaoAlteracao = table.Column<string>(type: "TEXT", nullable: true),
                    TextoOriginal = table.Column<string>(type: "TEXT", nullable: true),
                    TextoSubstituto = table.Column<string>(type: "TEXT", nullable: true),
                    SolicitanteId = table.Column<Guid>(type: "TEXT", nullable: true),
                    AprovadorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ParecerAnalise = table.Column<string>(type: "TEXT", nullable: true),
                    MotivoRejeicao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoSolicitacaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoDecisaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_SubstituicoesJulgamentoFinal", x => x.Id);
                    table.ForeignKey(
                        name: "FK_SubstituicoesJulgamentoFinal_JulgamentosFinais_JulgamentoFinalId",
                        column: x => x.JulgamentoFinalId,
                        principalTable: "JulgamentosFinais",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_SubstituicoesJulgamentoFinal_MembrosComissaoJulgadora_AprovadorId",
                        column: x => x.AprovadorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_SubstituicoesJulgamentoFinal_MembrosComissaoJulgadora_SolicitanteId",
                        column: x => x.SolicitanteId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "SuspensoesJulgamento",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Motivo = table.Column<int>(type: "INTEGER", nullable: false),
                    DeterminadaPorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPrevistaRetorno = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataRetorno = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    MotivoDetalhado = table.Column<string>(type: "TEXT", nullable: true),
                    CondicaoRetorno = table.Column<string>(type: "TEXT", nullable: true),
                    Ativa = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoEncerramento = table.Column<string>(type: "TEXT", nullable: true),
                    DiligenciaRelacionadaId = table.Column<int>(type: "INTEGER", nullable: true),
                    ProcessoRelacionado = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoDecisaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoRetomadaUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_SuspensoesJulgamento", x => x.Id);
                    table.ForeignKey(
                        name: "FK_SuspensoesJulgamento_MembrosComissaoJulgadora_DeterminadaPorId",
                        column: x => x.DeterminadaPorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "VotosJulgamentoFinal",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    MembroComissaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Voto = table.Column<int>(type: "INTEGER", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataVoto = table.Column<DateTime>(type: "TEXT", nullable: false),
                    VotoVencedor = table.Column<bool>(type: "INTEGER", nullable: false),
                    VotoRelator = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotosJulgamentoFinal", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotosJulgamentoFinal_JulgamentosFinais_JulgamentoId",
                        column: x => x.JulgamentoId,
                        principalTable: "JulgamentosFinais",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotosJulgamentoFinal_MembrosComissaoJulgadora_MembroComissaoId",
                        column: x => x.MembroComissaoId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "VotosRelator",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    RelatorId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Voto = table.Column<int>(type: "INTEGER", nullable: false),
                    DataVoto = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Relatorio = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Dispositivo = table.Column<string>(type: "TEXT", nullable: true),
                    Proposta = table.Column<string>(type: "TEXT", nullable: true),
                    VotoVencedor = table.Column<bool>(type: "INTEGER", nullable: false),
                    VotoCondutor = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoVotoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Assinado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataLeitura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    LidoEmSessao = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotosRelator", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotosRelator_MembrosComissaoJulgadora_RelatorId",
                        column: x => x.RelatorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotosRelator_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "VotosRevisor",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    RevisorId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Voto = table.Column<int>(type: "INTEGER", nullable: false),
                    DataVoto = table.Column<DateTime>(type: "TEXT", nullable: false),
                    AcompanhaRelator = table.Column<bool>(type: "INTEGER", nullable: false),
                    DivergenteEm = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Ressalva = table.Column<string>(type: "TEXT", nullable: true),
                    Complementacao = table.Column<string>(type: "TEXT", nullable: true),
                    VotoVencedor = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoVotoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Assinado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataLeitura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    LidoEmSessao = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotosRevisor", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotosRevisor_MembrosComissaoJulgadora_RevisorId",
                        column: x => x.RevisorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotosRevisor_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "VotosVogal",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    VogalId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Voto = table.Column<int>(type: "INTEGER", nullable: false),
                    DataVoto = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    AcompanhaRelator = table.Column<bool>(type: "INTEGER", nullable: false),
                    AcompanhaRevisor = table.Column<bool>(type: "INTEGER", nullable: false),
                    VotoAcompanha = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Ressalva = table.Column<string>(type: "TEXT", nullable: true),
                    DeclaracaoVoto = table.Column<string>(type: "TEXT", nullable: true),
                    VotoVencedor = table.Column<bool>(type: "INTEGER", nullable: false),
                    Impedido = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoImpedimento = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoVotoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Assinado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotosVogal", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotosVogal_MembrosComissaoJulgadora_VogalId",
                        column: x => x.VogalId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotosVogal_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "AdmissibilidadesDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    AnalistId = table.Column<Guid>(type: "TEXT", nullable: true),
                    AnalistaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Admitida = table.Column<bool>(type: "INTEGER", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataAnalise = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DocumentoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AdmissibilidadesDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AdmissibilidadesDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_AdmissibilidadesDenuncia_Usuarios_AnalistaId",
                        column: x => x.AnalistaId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "AlegacoesDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoLimite = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Tempestiva = table.Column<bool>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AlegacoesDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AlegacoesDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AnalisesDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    AnalistaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Parecer = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataConclusao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PrazoAnalise = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Recomendacao = table.Column<bool>(type: "INTEGER", nullable: true),
                    RecomendacaoDescricao = table.Column<string>(type: "TEXT", nullable: true),
                    DocumentoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AnalisesDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AnalisesDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_AnalisesDenuncia_Usuarios_AnalistaId",
                        column: x => x.AnalistaId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "AnexosDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Titulo = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Referencia = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: false),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTipo = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    DataAnexacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Publico = table.Column<bool>(type: "INTEGER", nullable: false),
                    Confidencial = table.Column<bool>(type: "INTEGER", nullable: false),
                    Hash = table.Column<string>(type: "TEXT", nullable: true),
                    Origem = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AnexosDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AnexosDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ArquivosDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: false),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTipo = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    DataEnvio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Publico = table.Column<bool>(type: "INTEGER", nullable: false),
                    Hash = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ArquivosDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ArquivosDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "DefesasDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    MembroId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoLimite = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Tempestiva = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_DefesasDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_DefesasDenuncia_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_DefesasDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_DefesasDenuncia_MembrosChapa_MembroId",
                        column: x => x.MembroId,
                        principalTable: "MembrosChapa",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "DenunciaChapas",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Principal = table.Column<bool>(type: "INTEGER", nullable: false),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataVinculacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_DenunciaChapas", x => x.Id);
                    table.ForeignKey(
                        name: "FK_DenunciaChapas_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_DenunciaChapas_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "DenunciaMembros",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    MembroId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Principal = table.Column<bool>(type: "INTEGER", nullable: false),
                    Papel = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataVinculacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_DenunciaMembros", x => x.Id);
                    table.ForeignKey(
                        name: "FK_DenunciaMembros_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_DenunciaMembros_MembrosChapa_MembroId",
                        column: x => x.MembroId,
                        principalTable: "MembrosChapa",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "DespachosDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    AutoridadeId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Assunto = table.Column<string>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Determinacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataDespacho = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PrazoCumprimento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Cumprido = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataCumprimento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    ObservacaoCumprimento = table.Column<string>(type: "TEXT", nullable: true),
                    DocumentoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_DespachosDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_DespachosDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_DespachosDenuncia_Usuarios_AutoridadeId",
                        column: x => x.AutoridadeId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "EncaminhamentosDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    RemetenteId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DestinatarioId = table.Column<Guid>(type: "TEXT", nullable: true),
                    SetorDestino = table.Column<string>(type: "TEXT", nullable: true),
                    Motivo = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataEncaminhamento = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataRecebimento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PrazoResposta = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Despacho = table.Column<string>(type: "TEXT", nullable: true),
                    Resposta = table.Column<string>(type: "TEXT", nullable: true),
                    DataResposta = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_EncaminhamentosDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_EncaminhamentosDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_EncaminhamentosDenuncia_Usuarios_DestinatarioId",
                        column: x => x.DestinatarioId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_EncaminhamentosDenuncia_Usuarios_RemetenteId",
                        column: x => x.RemetenteId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "HistoricosDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    StatusAnterior = table.Column<int>(type: "INTEGER", nullable: false),
                    StatusNovo = table.Column<int>(type: "INTEGER", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataAlteracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    AlteradoPor = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_HistoricosDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_HistoricosDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "JulgamentosDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    TipoDecisao = table.Column<int>(type: "INTEGER", nullable: true),
                    Procedente = table.Column<bool>(type: "INTEGER", nullable: true),
                    Improcedente = table.Column<bool>(type: "INTEGER", nullable: true),
                    ParcialmenteProcedente = table.Column<bool>(type: "INTEGER", nullable: true),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Dispositivo = table.Column<string>(type: "TEXT", nullable: true),
                    Penalidade = table.Column<string>(type: "TEXT", nullable: true),
                    DataJulgamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    AcordaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_JulgamentosDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_JulgamentosDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_JulgamentosDenuncia_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "NotificacoesDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<string>(type: "TEXT", nullable: false),
                    Destinatario = table.Column<string>(type: "TEXT", nullable: false),
                    Email = table.Column<string>(type: "TEXT", nullable: true),
                    Assunto = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    DataEnvio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Enviada = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataLeitura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Erro = table.Column<string>(type: "TEXT", nullable: true),
                    TentativasEnvio = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_NotificacoesDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_NotificacoesDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "PareceresDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    PareceristaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Numero = table.Column<string>(type: "TEXT", nullable: false),
                    Assunto = table.Column<string>(type: "TEXT", nullable: true),
                    Ementa = table.Column<string>(type: "TEXT", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Conclusao = table.Column<string>(type: "TEXT", nullable: true),
                    DataElaboracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataRevisao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataAprovacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    RevisorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DocumentoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Favoravel = table.Column<bool>(type: "INTEGER", nullable: true),
                    Recomendacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PareceresDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PareceresDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_PareceresDenuncia_Usuarios_PareceristaId",
                        column: x => x.PareceristaId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_PareceresDenuncia_Usuarios_RevisorId",
                        column: x => x.RevisorId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ProvasDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: false),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTipo = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    DataEnvio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Aceita = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoRejeicao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ProvasDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ProvasDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "RecursosDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: false),
                    Pedido = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoLimite = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Tempestivo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RecursosDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RecursosDenuncia_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RecursosDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "VistasDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DenunciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    SolicitanteId = table.Column<Guid>(type: "TEXT", nullable: true),
                    NomeSolicitante = table.Column<string>(type: "TEXT", nullable: true),
                    CpfSolicitante = table.Column<string>(type: "TEXT", nullable: true),
                    OabSolicitante = table.Column<string>(type: "TEXT", nullable: true),
                    Motivo = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataSolicitacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataConcessao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataInicio = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataFim = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PrazoDias = table.Column<int>(type: "INTEGER", nullable: true),
                    Prorrogada = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataProrrogacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    PrazoProrrogacao = table.Column<int>(type: "INTEGER", nullable: true),
                    MotivoNegativa = table.Column<string>(type: "TEXT", nullable: true),
                    AutorizadorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VistasDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VistasDenuncia_Denuncias_DenunciaId",
                        column: x => x.DenunciaId,
                        principalTable: "Denuncias",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VistasDenuncia_Usuarios_AutorizadorId",
                        column: x => x.AutorizadorId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VistasDenuncia_Usuarios_SolicitanteId",
                        column: x => x.SolicitanteId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "AlegacoesImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ImpugnacaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoLimite = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Tempestiva = table.Column<bool>(type: "INTEGER", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AlegacoesImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AlegacoesImpugnacao_Impugnacoes_ImpugnacaoId",
                        column: x => x.ImpugnacaoId,
                        principalTable: "Impugnacoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "DefesasImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ImpugnacaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoLimite = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Tempestiva = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_DefesasImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_DefesasImpugnacao_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_DefesasImpugnacao_Impugnacoes_ImpugnacaoId",
                        column: x => x.ImpugnacaoId,
                        principalTable: "Impugnacoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "HistoricosImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ImpugnacaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    StatusAnterior = table.Column<int>(type: "INTEGER", nullable: false),
                    StatusNovo = table.Column<int>(type: "INTEGER", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataAlteracao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    AlteradoPor = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_HistoricosImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_HistoricosImpugnacao_Impugnacoes_ImpugnacaoId",
                        column: x => x.ImpugnacaoId,
                        principalTable: "Impugnacoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "JulgamentosImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ImpugnacaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    TipoDecisao = table.Column<int>(type: "INTEGER", nullable: true),
                    Procedente = table.Column<bool>(type: "INTEGER", nullable: true),
                    Improcedente = table.Column<bool>(type: "INTEGER", nullable: true),
                    ParcialmenteProcedente = table.Column<bool>(type: "INTEGER", nullable: true),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Dispositivo = table.Column<string>(type: "TEXT", nullable: true),
                    Efeitos = table.Column<string>(type: "TEXT", nullable: true),
                    DataJulgamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    AcordaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_JulgamentosImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_JulgamentosImpugnacao_Impugnacoes_ImpugnacaoId",
                        column: x => x.ImpugnacaoId,
                        principalTable: "Impugnacoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_JulgamentosImpugnacao_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "PedidosImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ImpugnacaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Deferido = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoIndeferimento = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PedidosImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PedidosImpugnacao_Impugnacoes_ImpugnacaoId",
                        column: x => x.ImpugnacaoId,
                        principalTable: "Impugnacoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "RecursosImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ImpugnacaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: false),
                    Pedido = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoLimite = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Tempestivo = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RecursosImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RecursosImpugnacao_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RecursosImpugnacao_Impugnacoes_ImpugnacaoId",
                        column: x => x.ImpugnacaoId,
                        principalTable: "Impugnacoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ApuracaoResultadosChapa",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    ApuracaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TotalVotos = table.Column<int>(type: "INTEGER", nullable: false),
                    PercentualVotos = table.Column<decimal>(type: "TEXT", nullable: false),
                    PercentualVotosValidos = table.Column<decimal>(type: "TEXT", nullable: false),
                    Posicao = table.Column<int>(type: "INTEGER", nullable: false),
                    Eleita = table.Column<bool>(type: "INTEGER", nullable: false),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ApuracaoResultadosChapa", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ApuracaoResultadosChapa_ApuracaoResultados_ApuracaoId",
                        column: x => x.ApuracaoId,
                        principalTable: "ApuracaoResultados",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_ApuracaoResultadosChapa_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "FiscaisEleicao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ProfissionalId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    MesaReceptoraId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    NumeroCredencial = table.Column<string>(type: "TEXT", nullable: true),
                    DataCredenciamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataValidadeCredencial = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Funcao = table.Column<string>(type: "TEXT", nullable: true),
                    Turno = table.Column<string>(type: "TEXT", nullable: true),
                    DataInicioAtividade = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataFimAtividade = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Observacao = table.Column<string>(type: "TEXT", nullable: true),
                    MotivoCancelamento = table.Column<string>(type: "TEXT", nullable: true),
                    DataCancelamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CredenciadoPor = table.Column<string>(type: "TEXT", nullable: true),
                    CanceladoPor = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_FiscaisEleicao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_FiscaisEleicao_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_FiscaisEleicao_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_FiscaisEleicao_MesasReceptoras_MesaReceptoraId",
                        column: x => x.MesaReceptoraId,
                        principalTable: "MesasReceptoras",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_FiscaisEleicao_Profissionais_ProfissionalId",
                        column: x => x.ProfissionalId,
                        principalTable: "Profissionais",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "VotosAnulados",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EleicaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    VotoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    SecaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ZonaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ApuracaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Quantidade = table.Column<int>(type: "INTEGER", nullable: false),
                    Percentual = table.Column<double>(type: "REAL", nullable: false),
                    Motivo = table.Column<string>(type: "TEXT", nullable: false),
                    Justificativa = table.Column<string>(type: "TEXT", nullable: true),
                    AnuladoPorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    DataAnulacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    NumeroProcesso = table.Column<string>(type: "TEXT", nullable: true),
                    Decisao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Observacoes = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotosAnulados", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotosAnulados_Eleicoes_EleicaoId",
                        column: x => x.EleicaoId,
                        principalTable: "Eleicoes",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotosAnulados_RegistrosApuracaoVotos_ApuracaoId",
                        column: x => x.ApuracaoId,
                        principalTable: "RegistrosApuracaoVotos",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosAnulados_SecoesEleitorais_SecaoId",
                        column: x => x.SecaoId,
                        principalTable: "SecoesEleitorais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosAnulados_Usuarios_AnuladoPorId",
                        column: x => x.AnuladoPorId,
                        principalTable: "Usuarios",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosAnulados_Votos_VotoId",
                        column: x => x.VotoId,
                        principalTable: "Votos",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosAnulados_ZonasEleitorais_ZonaId",
                        column: x => x.ZonaId,
                        principalTable: "ZonasEleitorais",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ContraAlegacoesFinais",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    AlegacaoFinalId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Parte = table.Column<string>(type: "TEXT", nullable: true),
                    Representante = table.Column<string>(type: "TEXT", nullable: true),
                    DataProtocolo = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataPrazoFinal = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Titulo = table.Column<string>(type: "TEXT", nullable: true),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Conclusao = table.Column<string>(type: "TEXT", nullable: true),
                    Tempestiva = table.Column<bool>(type: "INTEGER", nullable: false),
                    ObservacaoTempestividade = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ContraAlegacoesFinais", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ContraAlegacoesFinais_AlegacoesFinais_AlegacaoFinalId",
                        column: x => x.AlegacaoFinalId,
                        principalTable: "AlegacoesFinais",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "VotosEmenda",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    EmendaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    MembroComissaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Voto = table.Column<int>(type: "INTEGER", nullable: false),
                    DataVoto = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Ressalva = table.Column<string>(type: "TEXT", nullable: true),
                    Impedido = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoImpedimento = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotosEmenda", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotosEmenda_EmendasJulgamento_EmendaId",
                        column: x => x.EmendaId,
                        principalTable: "EmendasJulgamento",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotosEmenda_MembrosComissaoJulgadora_MembroComissaoId",
                        column: x => x.MembroComissaoId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ContraAlegacoesDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    AlegacaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    MembroId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoLimite = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Tempestiva = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ContraAlegacoesDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ContraAlegacoesDenuncia_AlegacoesDenuncia_AlegacaoId",
                        column: x => x.AlegacaoId,
                        principalTable: "AlegacoesDenuncia",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_ContraAlegacoesDenuncia_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_ContraAlegacoesDenuncia_MembrosChapa_MembroId",
                        column: x => x.MembroId,
                        principalTable: "MembrosChapa",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ArquivosDefesa",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    DefesaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: false),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTipo = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    DataEnvio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ArquivosDefesa", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ArquivosDefesa_DefesasDenuncia_DefesaId",
                        column: x => x.DefesaId,
                        principalTable: "DefesasDenuncia",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "VotacoesJulgamentoDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    MembroComissaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Voto = table.Column<int>(type: "INTEGER", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataVoto = table.Column<DateTime>(type: "TEXT", nullable: false),
                    VotoVencedor = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotacoesJulgamentoDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotacoesJulgamentoDenuncia_JulgamentosDenuncia_JulgamentoId",
                        column: x => x.JulgamentoId,
                        principalTable: "JulgamentosDenuncia",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotacoesJulgamentoDenuncia_MembrosComissaoJulgadora_MembroComissaoId",
                        column: x => x.MembroComissaoId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ContrarrazoesRecursoDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    RecursoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ProfissionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoLimite = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Tempestiva = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ContrarrazoesRecursoDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ContrarrazoesRecursoDenuncia_Profissionais_ProfissionalId",
                        column: x => x.ProfissionalId,
                        principalTable: "Profissionais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_ContrarrazoesRecursoDenuncia_RecursosDenuncia_RecursoId",
                        column: x => x.RecursoId,
                        principalTable: "RecursosDenuncia",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "JulgamentosRecursoDenuncia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    RecursoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    TipoDecisao = table.Column<int>(type: "INTEGER", nullable: true),
                    Provido = table.Column<bool>(type: "INTEGER", nullable: true),
                    Desprovido = table.Column<bool>(type: "INTEGER", nullable: true),
                    ParcialmenteProvido = table.Column<bool>(type: "INTEGER", nullable: true),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Dispositivo = table.Column<string>(type: "TEXT", nullable: true),
                    DataJulgamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    AcordaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_JulgamentosRecursoDenuncia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_JulgamentosRecursoDenuncia_RecursosDenuncia_RecursoId",
                        column: x => x.RecursoId,
                        principalTable: "RecursosDenuncia",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_JulgamentosRecursoDenuncia_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ContraAlegacoesImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    AlegacaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ChapaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoLimite = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Tempestiva = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ContraAlegacoesImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ContraAlegacoesImpugnacao_AlegacoesImpugnacao_AlegacaoId",
                        column: x => x.AlegacaoId,
                        principalTable: "AlegacoesImpugnacao",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_ContraAlegacoesImpugnacao_Chapas_ChapaId",
                        column: x => x.ChapaId,
                        principalTable: "Chapas",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "ProvasImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    AlegacaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: false),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTipo = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    DataEnvio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Aceita = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoRejeicao = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ProvasImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ProvasImpugnacao_AlegacoesImpugnacao_AlegacaoId",
                        column: x => x.AlegacaoId,
                        principalTable: "AlegacoesImpugnacao",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "VotacoesJulgamentoImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    MembroComissaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Voto = table.Column<int>(type: "INTEGER", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataVoto = table.Column<DateTime>(type: "TEXT", nullable: false),
                    VotoVencedor = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotacoesJulgamentoImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotacoesJulgamentoImpugnacao_JulgamentosImpugnacao_JulgamentoId",
                        column: x => x.JulgamentoId,
                        principalTable: "JulgamentosImpugnacao",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_VotacoesJulgamentoImpugnacao_MembrosComissaoJulgadora_MembroComissaoId",
                        column: x => x.MembroComissaoId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ArquivosPedidoImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    PedidoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Nome = table.Column<string>(type: "TEXT", nullable: false),
                    Descricao = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: false),
                    ArquivoNome = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTipo = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoTamanho = table.Column<long>(type: "INTEGER", nullable: true),
                    DataEnvio = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ArquivosPedidoImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ArquivosPedidoImpugnacao_PedidosImpugnacao_PedidoId",
                        column: x => x.PedidoId,
                        principalTable: "PedidosImpugnacao",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "ContrarrazoesRecursoImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    RecursoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    ProfissionalId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Conteudo = table.Column<string>(type: "TEXT", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    DataApresentacao = table.Column<DateTime>(type: "TEXT", nullable: false),
                    PrazoLimite = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Tempestiva = table.Column<bool>(type: "INTEGER", nullable: false),
                    ArquivoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ContrarrazoesRecursoImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ContrarrazoesRecursoImpugnacao_Profissionais_ProfissionalId",
                        column: x => x.ProfissionalId,
                        principalTable: "Profissionais",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_ContrarrazoesRecursoImpugnacao_RecursosImpugnacao_RecursoId",
                        column: x => x.RecursoId,
                        principalTable: "RecursosImpugnacao",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "JulgamentosRecursoImpugnacao",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    RecursoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    TipoDecisao = table.Column<int>(type: "INTEGER", nullable: true),
                    Provido = table.Column<bool>(type: "INTEGER", nullable: true),
                    Desprovido = table.Column<bool>(type: "INTEGER", nullable: true),
                    ParcialmenteProvido = table.Column<bool>(type: "INTEGER", nullable: true),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Dispositivo = table.Column<string>(type: "TEXT", nullable: true),
                    DataJulgamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    AcordaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_JulgamentosRecursoImpugnacao", x => x.Id);
                    table.ForeignKey(
                        name: "FK_JulgamentosRecursoImpugnacao_RecursosImpugnacao_RecursoId",
                        column: x => x.RecursoId,
                        principalTable: "RecursosImpugnacao",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_JulgamentosRecursoImpugnacao_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "JulgamentosRecursoSegundaInstancia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    RecursoSegundaInstanciaId = table.Column<Guid>(type: "TEXT", nullable: false),
                    SessaoId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    TipoDecisao = table.Column<int>(type: "INTEGER", nullable: true),
                    DataJulgamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataPublicacao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Ementa = table.Column<string>(type: "TEXT", nullable: true),
                    Relatorio = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Dispositivo = table.Column<string>(type: "TEXT", nullable: true),
                    Resultado = table.Column<string>(type: "TEXT", nullable: true),
                    VotosFavoraveis = table.Column<int>(type: "INTEGER", nullable: false),
                    VotosContrarios = table.Column<int>(type: "INTEGER", nullable: false),
                    Abstencoes = table.Column<int>(type: "INTEGER", nullable: false),
                    AcordaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CertidaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Publicado = table.Column<bool>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_JulgamentosRecursoSegundaInstancia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_JulgamentosRecursoSegundaInstancia_SessoesJulgamento_SessaoId",
                        column: x => x.SessaoId,
                        principalTable: "SessoesJulgamento",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "VotosPlenario",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoJulgamento = table.Column<string>(type: "TEXT", nullable: false),
                    JulgamentoRecursoSegundaInstanciaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    MembroComissaoId = table.Column<Guid>(type: "TEXT", nullable: false),
                    TipoVoto = table.Column<int>(type: "INTEGER", nullable: false),
                    Voto = table.Column<int>(type: "INTEGER", nullable: false),
                    DataVoto = table.Column<DateTime>(type: "TEXT", nullable: false),
                    Ordem = table.Column<int>(type: "INTEGER", nullable: false),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Ressalva = table.Column<string>(type: "TEXT", nullable: true),
                    DeclaracaoVoto = table.Column<string>(type: "TEXT", nullable: true),
                    VotoVencedor = table.Column<bool>(type: "INTEGER", nullable: false),
                    VotoDesempate = table.Column<bool>(type: "INTEGER", nullable: false),
                    Impedido = table.Column<bool>(type: "INTEGER", nullable: false),
                    MotivoImpedimento = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoVotoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    Assinado = table.Column<bool>(type: "INTEGER", nullable: false),
                    DataAssinatura = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VotosPlenario", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VotosPlenario_JulgamentosRecursoSegundaInstancia_JulgamentoRecursoSegundaInstanciaId",
                        column: x => x.JulgamentoRecursoSegundaInstanciaId,
                        principalTable: "JulgamentosRecursoSegundaInstancia",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_VotosPlenario_MembrosComissaoJulgadora_MembroComissaoId",
                        column: x => x.MembroComissaoId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "RecursosJulgamentoFinal",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    JulgamentoFinalId = table.Column<Guid>(type: "TEXT", nullable: false),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Recorrente = table.Column<string>(type: "TEXT", nullable: true),
                    Recorrido = table.Column<string>(type: "TEXT", nullable: true),
                    DataProtocolo = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataAdmissibilidade = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataJulgamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Pedido = table.Column<string>(type: "TEXT", nullable: true),
                    DecisaoAdmissibilidade = table.Column<string>(type: "TEXT", nullable: true),
                    FundamentacaoAdmissibilidade = table.Column<string>(type: "TEXT", nullable: true),
                    RelatorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ArquivoRecursoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    ArquivoDecisaoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    TransformadoSegundaInstancia = table.Column<bool>(type: "INTEGER", nullable: false),
                    RecursoSegundaInstanciaId = table.Column<Guid>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RecursosJulgamentoFinal", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RecursosJulgamentoFinal_JulgamentosFinais_JulgamentoFinalId",
                        column: x => x.JulgamentoFinalId,
                        principalTable: "JulgamentosFinais",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_RecursosJulgamentoFinal_MembrosComissaoJulgadora_RelatorId",
                        column: x => x.RelatorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                });

            migrationBuilder.CreateTable(
                name: "RecursosSegundaInstancia",
                columns: table => new
                {
                    Id = table.Column<Guid>(type: "TEXT", nullable: false),
                    RecursoJulgamentoFinalOrigemId = table.Column<Guid>(type: "TEXT", nullable: true),
                    Protocolo = table.Column<string>(type: "TEXT", nullable: false),
                    Tipo = table.Column<int>(type: "INTEGER", nullable: false),
                    Status = table.Column<int>(type: "INTEGER", nullable: false),
                    Recorrente = table.Column<string>(type: "TEXT", nullable: true),
                    Recorrido = table.Column<string>(type: "TEXT", nullable: true),
                    DataProtocolo = table.Column<DateTime>(type: "TEXT", nullable: false),
                    DataDistribuicao = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataAdmissibilidade = table.Column<DateTime>(type: "TEXT", nullable: true),
                    DataJulgamento = table.Column<DateTime>(type: "TEXT", nullable: true),
                    NumeroProcessoOrigem = table.Column<string>(type: "TEXT", nullable: true),
                    OrgaoOrigem = table.Column<string>(type: "TEXT", nullable: true),
                    Fundamentacao = table.Column<string>(type: "TEXT", nullable: true),
                    Pedido = table.Column<string>(type: "TEXT", nullable: true),
                    DecisaoAdmissibilidade = table.Column<string>(type: "TEXT", nullable: true),
                    ComissaoJulgadoraId = table.Column<Guid>(type: "TEXT", nullable: true),
                    RelatorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    RevisorId = table.Column<Guid>(type: "TEXT", nullable: true),
                    ArquivoRecursoUrl = table.Column<string>(type: "TEXT", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false),
                    UpdatedAt = table.Column<DateTime>(type: "TEXT", nullable: true),
                    CreatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    UpdatedBy = table.Column<string>(type: "TEXT", nullable: true),
                    IsDeleted = table.Column<bool>(type: "INTEGER", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RecursosSegundaInstancia", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RecursosSegundaInstancia_ComissoesJulgadoras_ComissaoJulgadoraId",
                        column: x => x.ComissaoJulgadoraId,
                        principalTable: "ComissoesJulgadoras",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RecursosSegundaInstancia_MembrosComissaoJulgadora_RelatorId",
                        column: x => x.RelatorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RecursosSegundaInstancia_MembrosComissaoJulgadora_RevisorId",
                        column: x => x.RevisorId,
                        principalTable: "MembrosComissaoJulgadora",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_RecursosSegundaInstancia_RecursosJulgamentoFinal_RecursoJulgamentoFinalOrigemId",
                        column: x => x.RecursoJulgamentoFinalOrigemId,
                        principalTable: "RecursosJulgamentoFinal",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.SetNull);
                });

            migrationBuilder.CreateIndex(
                name: "IX_AcordaosJulgamento_RelatorId",
                table: "AcordaosJulgamento",
                column: "RelatorId");

            migrationBuilder.CreateIndex(
                name: "IX_AdmissibilidadesDenuncia_AnalistaId",
                table: "AdmissibilidadesDenuncia",
                column: "AnalistaId");

            migrationBuilder.CreateIndex(
                name: "IX_AdmissibilidadesDenuncia_DenunciaId",
                table: "AdmissibilidadesDenuncia",
                column: "DenunciaId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_AlegacoesDenuncia_DenunciaId",
                table: "AlegacoesDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_AlegacoesFinais_JulgamentoFinalId",
                table: "AlegacoesFinais",
                column: "JulgamentoFinalId");

            migrationBuilder.CreateIndex(
                name: "IX_AlegacoesImpugnacao_ImpugnacaoId",
                table: "AlegacoesImpugnacao",
                column: "ImpugnacaoId");

            migrationBuilder.CreateIndex(
                name: "IX_AnalisesDenuncia_AnalistaId",
                table: "AnalisesDenuncia",
                column: "AnalistaId");

            migrationBuilder.CreateIndex(
                name: "IX_AnalisesDenuncia_DenunciaId",
                table: "AnalisesDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_AnexosDenuncia_DenunciaId",
                table: "AnexosDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_ApuracaoResultados_ChapaVencedoraId",
                table: "ApuracaoResultados",
                column: "ChapaVencedoraId");

            migrationBuilder.CreateIndex(
                name: "IX_ApuracaoResultados_EleicaoId",
                table: "ApuracaoResultados",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ApuracaoResultados_RegiaoPleitoId",
                table: "ApuracaoResultados",
                column: "RegiaoPleitoId");

            migrationBuilder.CreateIndex(
                name: "IX_ApuracaoResultados_SecaoId",
                table: "ApuracaoResultados",
                column: "SecaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ApuracaoResultados_UrnaId",
                table: "ApuracaoResultados",
                column: "UrnaId");

            migrationBuilder.CreateIndex(
                name: "IX_ApuracaoResultadosChapa_ApuracaoId",
                table: "ApuracaoResultadosChapa",
                column: "ApuracaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ApuracaoResultadosChapa_ChapaId",
                table: "ApuracaoResultadosChapa",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_ArquivamentosJulgamento_AnalisadoPorId",
                table: "ArquivamentosJulgamento",
                column: "AnalisadoPorId");

            migrationBuilder.CreateIndex(
                name: "IX_ArquivamentosJulgamento_SolicitadoPorId",
                table: "ArquivamentosJulgamento",
                column: "SolicitadoPorId");

            migrationBuilder.CreateIndex(
                name: "IX_ArquivosDefesa_DefesaId",
                table: "ArquivosDefesa",
                column: "DefesaId");

            migrationBuilder.CreateIndex(
                name: "IX_ArquivosDenuncia_DenunciaId",
                table: "ArquivosDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_ArquivosDocumento_DocumentoId",
                table: "ArquivosDocumento",
                column: "DocumentoId");

            migrationBuilder.CreateIndex(
                name: "IX_ArquivosPedidoImpugnacao_PedidoId",
                table: "ArquivosPedidoImpugnacao",
                column: "PedidoId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_AtaApuracaoId",
                table: "AssinaturasDigitais",
                column: "AtaApuracaoId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_AtaReuniaoId",
                table: "AssinaturasDigitais",
                column: "AtaReuniaoId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_CarimboTempoId",
                table: "AssinaturasDigitais",
                column: "CarimboTempoId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_CertificadoDigitalId",
                table: "AssinaturasDigitais",
                column: "CertificadoDigitalId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_CertificadoDigitalId1",
                table: "AssinaturasDigitais",
                column: "CertificadoDigitalId1");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_CertificadoId",
                table: "AssinaturasDigitais",
                column: "CertificadoId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_DeclaracaoId",
                table: "AssinaturasDigitais",
                column: "DeclaracaoId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_DiplomaId",
                table: "AssinaturasDigitais",
                column: "DiplomaId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_DocumentoId",
                table: "AssinaturasDigitais",
                column: "DocumentoId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_ResultadoFinalId",
                table: "AssinaturasDigitais",
                column: "ResultadoFinalId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_SignatarioId",
                table: "AssinaturasDigitais",
                column: "SignatarioId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_TermoId",
                table: "AssinaturasDigitais",
                column: "TermoId");

            migrationBuilder.CreateIndex(
                name: "IX_AssinaturasDigitais_TermoPosseId",
                table: "AssinaturasDigitais",
                column: "TermoPosseId");

            migrationBuilder.CreateIndex(
                name: "IX_AtasApuracao_EleicaoId",
                table: "AtasApuracao",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_AtasApuracao_ResultadoId",
                table: "AtasApuracao",
                column: "ResultadoId");

            migrationBuilder.CreateIndex(
                name: "IX_AtasReuniao_EleicaoId",
                table: "AtasReuniao",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_AtasSessao_SessaoId",
                table: "AtasSessao",
                column: "SessaoId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_AtividadesPrincipaisCalendario_CalendarioId",
                table: "AtividadesPrincipaisCalendario",
                column: "CalendarioId");

            migrationBuilder.CreateIndex(
                name: "IX_AtividadesSecundariasCalendario_AtividadePrincipalId",
                table: "AtividadesSecundariasCalendario",
                column: "AtividadePrincipalId");

            migrationBuilder.CreateIndex(
                name: "IX_AtividadesSecundariasCalendario_CalendarioId",
                table: "AtividadesSecundariasCalendario",
                column: "CalendarioId");

            migrationBuilder.CreateIndex(
                name: "IX_Atos_EleicaoId",
                table: "Atos",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Avisos_EleicaoId",
                table: "Avisos",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_BoletinsUrna_EleicaoId",
                table: "BoletinsUrna",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_BoletinsUrna_SecaoId",
                table: "BoletinsUrna",
                column: "SecaoId");

            migrationBuilder.CreateIndex(
                name: "IX_BoletinsUrna_ZonaId",
                table: "BoletinsUrna",
                column: "ZonaId");

            migrationBuilder.CreateIndex(
                name: "IX_Calendarios_EleicaoId",
                table: "Calendarios",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_CalendarioSituacoes_CalendarioId",
                table: "CalendarioSituacoes",
                column: "CalendarioId");

            migrationBuilder.CreateIndex(
                name: "IX_CategoriasDocumento_CategoriaPaiId",
                table: "CategoriasDocumento",
                column: "CategoriaPaiId");

            migrationBuilder.CreateIndex(
                name: "IX_Certificados_EleicaoId",
                table: "Certificados",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Certificados_UsuarioId",
                table: "Certificados",
                column: "UsuarioId");

            migrationBuilder.CreateIndex(
                name: "IX_CertificadosDigitais_UsuarioId",
                table: "CertificadosDigitais",
                column: "UsuarioId");

            migrationBuilder.CreateIndex(
                name: "IX_Chapas_EleicaoId",
                table: "Chapas",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Circunscricoes_RegionalId",
                table: "Circunscricoes",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_ComissoesJulgadoras_EleicaoId",
                table: "ComissoesJulgadoras",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ComposicoesChapa_ChapaId",
                table: "ComposicoesChapa",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_Comunicados_EleicaoId",
                table: "Comunicados",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ConfiguracoesEleicao_EleicaoId",
                table: "ConfiguracoesEleicao",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ConfirmacoesMembrosChapa_MembroId",
                table: "ConfirmacoesMembrosChapa",
                column: "MembroId");

            migrationBuilder.CreateIndex(
                name: "IX_Conselheiros_ProfissionalId",
                table: "Conselheiros",
                column: "ProfissionalId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_ContraAlegacoesDenuncia_AlegacaoId",
                table: "ContraAlegacoesDenuncia",
                column: "AlegacaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ContraAlegacoesDenuncia_ChapaId",
                table: "ContraAlegacoesDenuncia",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_ContraAlegacoesDenuncia_MembroId",
                table: "ContraAlegacoesDenuncia",
                column: "MembroId");

            migrationBuilder.CreateIndex(
                name: "IX_ContraAlegacoesFinais_AlegacaoFinalId",
                table: "ContraAlegacoesFinais",
                column: "AlegacaoFinalId");

            migrationBuilder.CreateIndex(
                name: "IX_ContraAlegacoesImpugnacao_AlegacaoId",
                table: "ContraAlegacoesImpugnacao",
                column: "AlegacaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ContraAlegacoesImpugnacao_ChapaId",
                table: "ContraAlegacoesImpugnacao",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_ContrarrazoesRecursoDenuncia_ProfissionalId",
                table: "ContrarrazoesRecursoDenuncia",
                column: "ProfissionalId");

            migrationBuilder.CreateIndex(
                name: "IX_ContrarrazoesRecursoDenuncia_RecursoId",
                table: "ContrarrazoesRecursoDenuncia",
                column: "RecursoId");

            migrationBuilder.CreateIndex(
                name: "IX_ContrarrazoesRecursoImpugnacao_ProfissionalId",
                table: "ContrarrazoesRecursoImpugnacao",
                column: "ProfissionalId");

            migrationBuilder.CreateIndex(
                name: "IX_ContrarrazoesRecursoImpugnacao_RecursoId",
                table: "ContrarrazoesRecursoImpugnacao",
                column: "RecursoId");

            migrationBuilder.CreateIndex(
                name: "IX_Convocacoes_EleicaoId",
                table: "Convocacoes",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Declaracoes_EleicaoId",
                table: "Declaracoes",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Declaracoes_UsuarioId",
                table: "Declaracoes",
                column: "UsuarioId");

            migrationBuilder.CreateIndex(
                name: "IX_DefesasDenuncia_ChapaId",
                table: "DefesasDenuncia",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_DefesasDenuncia_DenunciaId",
                table: "DefesasDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_DefesasDenuncia_MembroId",
                table: "DefesasDenuncia",
                column: "MembroId");

            migrationBuilder.CreateIndex(
                name: "IX_DefesasImpugnacao_ChapaId",
                table: "DefesasImpugnacao",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_DefesasImpugnacao_ImpugnacaoId",
                table: "DefesasImpugnacao",
                column: "ImpugnacaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Deliberacoes_EleicaoId",
                table: "Deliberacoes",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_DenunciaChapas_ChapaId",
                table: "DenunciaChapas",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_DenunciaChapas_DenunciaId",
                table: "DenunciaChapas",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_DenunciaMembros_DenunciaId",
                table: "DenunciaMembros",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_DenunciaMembros_MembroId",
                table: "DenunciaMembros",
                column: "MembroId");

            migrationBuilder.CreateIndex(
                name: "IX_Denuncias_ChapaId",
                table: "Denuncias",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_Denuncias_DenuncianteId",
                table: "Denuncias",
                column: "DenuncianteId");

            migrationBuilder.CreateIndex(
                name: "IX_Denuncias_EleicaoId",
                table: "Denuncias",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Denuncias_MembroId",
                table: "Denuncias",
                column: "MembroId");

            migrationBuilder.CreateIndex(
                name: "IX_DespachosDenuncia_AutoridadeId",
                table: "DespachosDenuncia",
                column: "AutoridadeId");

            migrationBuilder.CreateIndex(
                name: "IX_DespachosDenuncia_DenunciaId",
                table: "DespachosDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_DiligenciasJulgamento_DeterminadaPorId",
                table: "DiligenciasJulgamento",
                column: "DeterminadaPorId");

            migrationBuilder.CreateIndex(
                name: "IX_Diplomas_ChapaId",
                table: "Diplomas",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_Diplomas_EleicaoId",
                table: "Diplomas",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Diplomas_UsuarioId",
                table: "Diplomas",
                column: "UsuarioId");

            migrationBuilder.CreateIndex(
                name: "IX_Documentos_CategoriaDocumentoEntityId",
                table: "Documentos",
                column: "CategoriaDocumentoEntityId");

            migrationBuilder.CreateIndex(
                name: "IX_Documentos_EleicaoId",
                table: "Documentos",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_DocumentosChapa_ChapaId",
                table: "DocumentosChapa",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_DocumentosChapa_MembroId",
                table: "DocumentosChapa",
                column: "MembroId");

            migrationBuilder.CreateIndex(
                name: "IX_Editais_EleicaoId",
                table: "Editais",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_EleicaoSituacoes_EleicaoId",
                table: "EleicaoSituacoes",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Eleicoes_RegionalId",
                table: "Eleicoes",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_Eleitores_EleicaoId",
                table: "Eleitores",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Eleitores_ProfissionalId",
                table: "Eleitores",
                column: "ProfissionalId");

            migrationBuilder.CreateIndex(
                name: "IX_Eleitores_SecaoId",
                table: "Eleitores",
                column: "SecaoId");

            migrationBuilder.CreateIndex(
                name: "IX_EmendasJulgamento_ProponenteId",
                table: "EmendasJulgamento",
                column: "ProponenteId");

            migrationBuilder.CreateIndex(
                name: "IX_EmendasJulgamento_SessaoId",
                table: "EmendasJulgamento",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_EncaminhamentosDenuncia_DenunciaId",
                table: "EncaminhamentosDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_EncaminhamentosDenuncia_DestinatarioId",
                table: "EncaminhamentosDenuncia",
                column: "DestinatarioId");

            migrationBuilder.CreateIndex(
                name: "IX_EncaminhamentosDenuncia_RemetenteId",
                table: "EncaminhamentosDenuncia",
                column: "RemetenteId");

            migrationBuilder.CreateIndex(
                name: "IX_EstatisticasEleicao_EleicaoId",
                table: "EstatisticasEleicao",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_EstatisticasEleicao_RegionalId",
                table: "EstatisticasEleicao",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_EtapasEleicao_EleicaoId",
                table: "EtapasEleicao",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_EtapasEleicao_EtapaAnteriorId",
                table: "EtapasEleicao",
                column: "EtapaAnteriorId");

            migrationBuilder.CreateIndex(
                name: "IX_ExportacoesDados_EleicaoId",
                table: "ExportacoesDados",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ExportacoesDados_SolicitanteId",
                table: "ExportacoesDados",
                column: "SolicitanteId");

            migrationBuilder.CreateIndex(
                name: "IX_Filiais_RegionalId",
                table: "Filiais",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_FiscaisEleicao_ChapaId",
                table: "FiscaisEleicao",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_FiscaisEleicao_EleicaoId",
                table: "FiscaisEleicao",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_FiscaisEleicao_MesaReceptoraId",
                table: "FiscaisEleicao",
                column: "MesaReceptoraId");

            migrationBuilder.CreateIndex(
                name: "IX_FiscaisEleicao_ProfissionalId",
                table: "FiscaisEleicao",
                column: "ProfissionalId");

            migrationBuilder.CreateIndex(
                name: "IX_GraficosResultado_EleicaoId",
                table: "GraficosResultado",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_GraficosResultado_RegionalId",
                table: "GraficosResultado",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_GraficosResultado_ResultadoId",
                table: "GraficosResultado",
                column: "ResultadoId");

            migrationBuilder.CreateIndex(
                name: "IX_HistoricosChapa_ChapaId",
                table: "HistoricosChapa",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_HistoricosDenuncia_DenunciaId",
                table: "HistoricosDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_HistoricosExtratoConselheiro_ConselheiroId",
                table: "HistoricosExtratoConselheiro",
                column: "ConselheiroId");

            migrationBuilder.CreateIndex(
                name: "IX_HistoricosImpugnacao_ImpugnacaoId",
                table: "HistoricosImpugnacao",
                column: "ImpugnacaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ImportacoesDados_EleicaoId",
                table: "ImportacoesDados",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ImportacoesDados_SolicitanteId",
                table: "ImportacoesDados",
                column: "SolicitanteId");

            migrationBuilder.CreateIndex(
                name: "IX_Impugnacoes_ChapaImpugnadaId",
                table: "Impugnacoes",
                column: "ChapaImpugnadaId");

            migrationBuilder.CreateIndex(
                name: "IX_Impugnacoes_ChapaImpugnanteId",
                table: "Impugnacoes",
                column: "ChapaImpugnanteId");

            migrationBuilder.CreateIndex(
                name: "IX_Impugnacoes_EleicaoId",
                table: "Impugnacoes",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Impugnacoes_ImpugnanteId",
                table: "Impugnacoes",
                column: "ImpugnanteId");

            migrationBuilder.CreateIndex(
                name: "IX_Impugnacoes_MembroImpugnadoId",
                table: "Impugnacoes",
                column: "MembroImpugnadoId");

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosDenuncia_DenunciaId",
                table: "JulgamentosDenuncia",
                column: "DenunciaId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosDenuncia_SessaoId",
                table: "JulgamentosDenuncia",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosFinais_EleicaoId",
                table: "JulgamentosFinais",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosFinais_SessaoId",
                table: "JulgamentosFinais",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosImpugnacao_ImpugnacaoId",
                table: "JulgamentosImpugnacao",
                column: "ImpugnacaoId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosImpugnacao_SessaoId",
                table: "JulgamentosImpugnacao",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosRecursoDenuncia_RecursoId",
                table: "JulgamentosRecursoDenuncia",
                column: "RecursoId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosRecursoDenuncia_SessaoId",
                table: "JulgamentosRecursoDenuncia",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosRecursoImpugnacao_RecursoId",
                table: "JulgamentosRecursoImpugnacao",
                column: "RecursoId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosRecursoImpugnacao_SessaoId",
                table: "JulgamentosRecursoImpugnacao",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosRecursoSegundaInstancia_RecursoSegundaInstanciaId",
                table: "JulgamentosRecursoSegundaInstancia",
                column: "RecursoSegundaInstanciaId");

            migrationBuilder.CreateIndex(
                name: "IX_JulgamentosRecursoSegundaInstancia_SessaoId",
                table: "JulgamentosRecursoSegundaInstancia",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_LogsAcesso_UsuarioId",
                table: "LogsAcesso",
                column: "UsuarioId");

            migrationBuilder.CreateIndex(
                name: "IX_MapasVotacao_EleicaoId",
                table: "MapasVotacao",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_MapasVotacao_RegionalId",
                table: "MapasVotacao",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_MembrosChapa_ChapaId",
                table: "MembrosChapa",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_MembrosChapa_ProfissionalId",
                table: "MembrosChapa",
                column: "ProfissionalId");

            migrationBuilder.CreateIndex(
                name: "IX_MembrosChapa_SubstituidoPorId",
                table: "MembrosChapa",
                column: "SubstituidoPorId");

            migrationBuilder.CreateIndex(
                name: "IX_MembrosComissaoJulgadora_ComissaoId",
                table: "MembrosComissaoJulgadora",
                column: "ComissaoId");

            migrationBuilder.CreateIndex(
                name: "IX_MembrosComissaoJulgadora_ConselheiroId",
                table: "MembrosComissaoJulgadora",
                column: "ConselheiroId");

            migrationBuilder.CreateIndex(
                name: "IX_MesasReceptoras_EleicaoId",
                table: "MesasReceptoras",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_MesasReceptoras_PresidenteId",
                table: "MesasReceptoras",
                column: "PresidenteId");

            migrationBuilder.CreateIndex(
                name: "IX_MesasReceptoras_RegiaoPleitoId",
                table: "MesasReceptoras",
                column: "RegiaoPleitoId");

            migrationBuilder.CreateIndex(
                name: "IX_MesasReceptoras_SecaoId",
                table: "MesasReceptoras",
                column: "SecaoId");

            migrationBuilder.CreateIndex(
                name: "IX_MesasReceptoras_SecretarioId",
                table: "MesasReceptoras",
                column: "SecretarioId");

            migrationBuilder.CreateIndex(
                name: "IX_MesasReceptoras_UrnaId",
                table: "MesasReceptoras",
                column: "UrnaId");

            migrationBuilder.CreateIndex(
                name: "IX_Normativas_EleicaoId",
                table: "Normativas",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Normativas_NormativaRevogadaId",
                table: "Normativas",
                column: "NormativaRevogadaId");

            migrationBuilder.CreateIndex(
                name: "IX_NotificacoesDenuncia_DenunciaId",
                table: "NotificacoesDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_ObservacoesJulgamento_MembroComissaoId",
                table: "ObservacoesJulgamento",
                column: "MembroComissaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ObservacoesJulgamento_ObservacaoRelacionadaId",
                table: "ObservacoesJulgamento",
                column: "ObservacaoRelacionadaId");

            migrationBuilder.CreateIndex(
                name: "IX_PareceresDenuncia_DenunciaId",
                table: "PareceresDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_PareceresDenuncia_PareceristaId",
                table: "PareceresDenuncia",
                column: "PareceristaId");

            migrationBuilder.CreateIndex(
                name: "IX_PareceresDenuncia_RevisorId",
                table: "PareceresDenuncia",
                column: "RevisorId");

            migrationBuilder.CreateIndex(
                name: "IX_PareceristaProcuradores_ProcuradorId",
                table: "PareceristaProcuradores",
                column: "ProcuradorId");

            migrationBuilder.CreateIndex(
                name: "IX_PautasSessao_RelatorId",
                table: "PautasSessao",
                column: "RelatorId");

            migrationBuilder.CreateIndex(
                name: "IX_PautasSessao_SessaoId",
                table: "PautasSessao",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_PedidosImpugnacao_ImpugnacaoId",
                table: "PedidosImpugnacao",
                column: "ImpugnacaoId");

            migrationBuilder.CreateIndex(
                name: "IX_PlataformasEleitorais_ChapaId",
                table: "PlataformasEleitorais",
                column: "ChapaId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_Portarias_EleicaoId",
                table: "Portarias",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Portarias_PortariaRevogadaId",
                table: "Portarias",
                column: "PortariaRevogadaId");

            migrationBuilder.CreateIndex(
                name: "IX_Profissionais_FilialId",
                table: "Profissionais",
                column: "FilialId");

            migrationBuilder.CreateIndex(
                name: "IX_Profissionais_RegionalId",
                table: "Profissionais",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_Profissionais_UsuarioId",
                table: "Profissionais",
                column: "UsuarioId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_ProvasDenuncia_DenunciaId",
                table: "ProvasDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_ProvasImpugnacao_AlegacaoId",
                table: "ProvasImpugnacao",
                column: "AlegacaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ProvasJulgamento_AnalisadoPorId",
                table: "ProvasJulgamento",
                column: "AnalisadoPorId");

            migrationBuilder.CreateIndex(
                name: "IX_Publicacoes_DocumentoId",
                table: "Publicacoes",
                column: "DocumentoId");

            migrationBuilder.CreateIndex(
                name: "IX_PublicacoesOficiais_AtoId",
                table: "PublicacoesOficiais",
                column: "AtoId");

            migrationBuilder.CreateIndex(
                name: "IX_PublicacoesOficiais_AvisoId",
                table: "PublicacoesOficiais",
                column: "AvisoId");

            migrationBuilder.CreateIndex(
                name: "IX_PublicacoesOficiais_ConvocacaoId",
                table: "PublicacoesOficiais",
                column: "ConvocacaoId");

            migrationBuilder.CreateIndex(
                name: "IX_PublicacoesOficiais_DeliberacaoId",
                table: "PublicacoesOficiais",
                column: "DeliberacaoId");

            migrationBuilder.CreateIndex(
                name: "IX_PublicacoesOficiais_DocumentoId",
                table: "PublicacoesOficiais",
                column: "DocumentoId");

            migrationBuilder.CreateIndex(
                name: "IX_PublicacoesOficiais_EleicaoId",
                table: "PublicacoesOficiais",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_PublicacoesOficiais_NormativaId",
                table: "PublicacoesOficiais",
                column: "NormativaId");

            migrationBuilder.CreateIndex(
                name: "IX_PublicacoesOficiais_PortariaId",
                table: "PublicacoesOficiais",
                column: "PortariaId");

            migrationBuilder.CreateIndex(
                name: "IX_PublicacoesOficiais_ResolucaoId",
                table: "PublicacoesOficiais",
                column: "ResolucaoId");

            migrationBuilder.CreateIndex(
                name: "IX_RecursosDenuncia_ChapaId",
                table: "RecursosDenuncia",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_RecursosDenuncia_DenunciaId",
                table: "RecursosDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_RecursosImpugnacao_ChapaId",
                table: "RecursosImpugnacao",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_RecursosImpugnacao_ImpugnacaoId",
                table: "RecursosImpugnacao",
                column: "ImpugnacaoId");

            migrationBuilder.CreateIndex(
                name: "IX_RecursosJulgamentoFinal_JulgamentoFinalId",
                table: "RecursosJulgamentoFinal",
                column: "JulgamentoFinalId");

            migrationBuilder.CreateIndex(
                name: "IX_RecursosJulgamentoFinal_RecursoSegundaInstanciaId",
                table: "RecursosJulgamentoFinal",
                column: "RecursoSegundaInstanciaId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_RecursosJulgamentoFinal_RelatorId",
                table: "RecursosJulgamentoFinal",
                column: "RelatorId");

            migrationBuilder.CreateIndex(
                name: "IX_RecursosSegundaInstancia_ComissaoJulgadoraId",
                table: "RecursosSegundaInstancia",
                column: "ComissaoJulgadoraId");

            migrationBuilder.CreateIndex(
                name: "IX_RecursosSegundaInstancia_RecursoJulgamentoFinalOrigemId",
                table: "RecursosSegundaInstancia",
                column: "RecursoJulgamentoFinalOrigemId",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_RecursosSegundaInstancia_RelatorId",
                table: "RecursosSegundaInstancia",
                column: "RelatorId");

            migrationBuilder.CreateIndex(
                name: "IX_RecursosSegundaInstancia_RevisorId",
                table: "RecursosSegundaInstancia",
                column: "RevisorId");

            migrationBuilder.CreateIndex(
                name: "IX_RegioesPleito_CircunscricaoId",
                table: "RegioesPleito",
                column: "CircunscricaoId");

            migrationBuilder.CreateIndex(
                name: "IX_RegioesPleito_EleicaoId",
                table: "RegioesPleito",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_RegioesPleito_RegionalId",
                table: "RegioesPleito",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_RegistrosApuracaoVotos_EleicaoId",
                table: "RegistrosApuracaoVotos",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_RegistrosApuracaoVotos_RegionalId",
                table: "RegistrosApuracaoVotos",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_RegistrosApuracaoVotos_SecaoId",
                table: "RegistrosApuracaoVotos",
                column: "SecaoId");

            migrationBuilder.CreateIndex(
                name: "IX_RegistrosApuracaoVotos_ZonaId",
                table: "RegistrosApuracaoVotos",
                column: "ZonaId");

            migrationBuilder.CreateIndex(
                name: "IX_RelatoriosApuracao_ApuracaoId",
                table: "RelatoriosApuracao",
                column: "ApuracaoId");

            migrationBuilder.CreateIndex(
                name: "IX_RelatoriosApuracao_EleicaoId",
                table: "RelatoriosApuracao",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_RelatoriosApuracao_GeradoPorId",
                table: "RelatoriosApuracao",
                column: "GeradoPorId");

            migrationBuilder.CreateIndex(
                name: "IX_RelatoriosApuracao_RegionalId",
                table: "RelatoriosApuracao",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_RelatoriosJulgamento_RelatorId",
                table: "RelatoriosJulgamento",
                column: "RelatorId");

            migrationBuilder.CreateIndex(
                name: "IX_RelatoriosVotacao_EleicaoId",
                table: "RelatoriosVotacao",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_RelatoriosVotacao_GeradoPorId",
                table: "RelatoriosVotacao",
                column: "GeradoPorId");

            migrationBuilder.CreateIndex(
                name: "IX_RelatoriosVotacao_RegionalId",
                table: "RelatoriosVotacao",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_Resolucoes_EleicaoId",
                table: "Resolucoes",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Resolucoes_ResolucaoRevogadaId",
                table: "Resolucoes",
                column: "ResolucaoRevogadaId");

            migrationBuilder.CreateIndex(
                name: "IX_ResultadosEleicao_EleicaoId",
                table: "ResultadosEleicao",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ResultadosFinais_EleicaoId",
                table: "ResultadosFinais",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ResultadosFinais_RegionalId",
                table: "ResultadosFinais",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_ResultadosParciais_EleicaoId",
                table: "ResultadosParciais",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_ResultadosParciais_RegionalId",
                table: "ResultadosParciais",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_RolePermissoes_PermissaoId",
                table: "RolePermissoes",
                column: "PermissaoId");

            migrationBuilder.CreateIndex(
                name: "IX_RolePermissoes_RoleId",
                table: "RolePermissoes",
                column: "RoleId");

            migrationBuilder.CreateIndex(
                name: "IX_SecoesEleitorais_ZonaEleitoralId",
                table: "SecoesEleitorais",
                column: "ZonaEleitoralId");

            migrationBuilder.CreateIndex(
                name: "IX_SessoesJulgamento_ComissaoId",
                table: "SessoesJulgamento",
                column: "ComissaoId");

            migrationBuilder.CreateIndex(
                name: "IX_SubstituicoesJulgamentoFinal_AprovadorId",
                table: "SubstituicoesJulgamentoFinal",
                column: "AprovadorId");

            migrationBuilder.CreateIndex(
                name: "IX_SubstituicoesJulgamentoFinal_JulgamentoFinalId",
                table: "SubstituicoesJulgamentoFinal",
                column: "JulgamentoFinalId");

            migrationBuilder.CreateIndex(
                name: "IX_SubstituicoesJulgamentoFinal_SolicitanteId",
                table: "SubstituicoesJulgamentoFinal",
                column: "SolicitanteId");

            migrationBuilder.CreateIndex(
                name: "IX_SubstituicoesMembrosChapa_ChapaId",
                table: "SubstituicoesMembrosChapa",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_SubstituicoesMembrosChapa_MembroAntigoId",
                table: "SubstituicoesMembrosChapa",
                column: "MembroAntigoId");

            migrationBuilder.CreateIndex(
                name: "IX_SubstituicoesMembrosChapa_MembroNovoId",
                table: "SubstituicoesMembrosChapa",
                column: "MembroNovoId");

            migrationBuilder.CreateIndex(
                name: "IX_SuspensoesJulgamento_DeterminadaPorId",
                table: "SuspensoesJulgamento",
                column: "DeterminadaPorId");

            migrationBuilder.CreateIndex(
                name: "IX_TemplatesDocumento_EleicaoId",
                table: "TemplatesDocumento",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_TemplatesDocumento_ModeloId",
                table: "TemplatesDocumento",
                column: "ModeloId");

            migrationBuilder.CreateIndex(
                name: "IX_Termos_EleicaoId",
                table: "Termos",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Termos_UsuarioId",
                table: "Termos",
                column: "UsuarioId");

            migrationBuilder.CreateIndex(
                name: "IX_TermosPosse_ChapaId",
                table: "TermosPosse",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_TermosPosse_EleicaoId",
                table: "TermosPosse",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_TermosPosse_UsuarioId",
                table: "TermosPosse",
                column: "UsuarioId");

            migrationBuilder.CreateIndex(
                name: "IX_TotaisVotos_ApuracaoId",
                table: "TotaisVotos",
                column: "ApuracaoId");

            migrationBuilder.CreateIndex(
                name: "IX_TotaisVotos_ChapaId",
                table: "TotaisVotos",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_UrnasEletronicas_EleicaoId",
                table: "UrnasEletronicas",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_UrnasEletronicas_RegiaoPleitoId",
                table: "UrnasEletronicas",
                column: "RegiaoPleitoId");

            migrationBuilder.CreateIndex(
                name: "IX_UrnasEletronicas_SecaoId",
                table: "UrnasEletronicas",
                column: "SecaoId");

            migrationBuilder.CreateIndex(
                name: "IX_UsuarioRoles_RoleId",
                table: "UsuarioRoles",
                column: "RoleId");

            migrationBuilder.CreateIndex(
                name: "IX_UsuarioRoles_UsuarioId",
                table: "UsuarioRoles",
                column: "UsuarioId");

            migrationBuilder.CreateIndex(
                name: "IX_VistasDenuncia_AutorizadorId",
                table: "VistasDenuncia",
                column: "AutorizadorId");

            migrationBuilder.CreateIndex(
                name: "IX_VistasDenuncia_DenunciaId",
                table: "VistasDenuncia",
                column: "DenunciaId");

            migrationBuilder.CreateIndex(
                name: "IX_VistasDenuncia_SolicitanteId",
                table: "VistasDenuncia",
                column: "SolicitanteId");

            migrationBuilder.CreateIndex(
                name: "IX_VotacoesJulgamentoDenuncia_JulgamentoId",
                table: "VotacoesJulgamentoDenuncia",
                column: "JulgamentoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotacoesJulgamentoDenuncia_MembroComissaoId",
                table: "VotacoesJulgamentoDenuncia",
                column: "MembroComissaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotacoesJulgamentoImpugnacao_JulgamentoId",
                table: "VotacoesJulgamentoImpugnacao",
                column: "JulgamentoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotacoesJulgamentoImpugnacao_MembroComissaoId",
                table: "VotacoesJulgamentoImpugnacao",
                column: "MembroComissaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Votos_ChapaId",
                table: "Votos",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_Votos_EleicaoId",
                table: "Votos",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Votos_SecaoId",
                table: "Votos",
                column: "SecaoId");

            migrationBuilder.CreateIndex(
                name: "IX_Votos_UrnaId",
                table: "Votos",
                column: "UrnaId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosAnulados_AnuladoPorId",
                table: "VotosAnulados",
                column: "AnuladoPorId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosAnulados_ApuracaoId",
                table: "VotosAnulados",
                column: "ApuracaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosAnulados_EleicaoId",
                table: "VotosAnulados",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosAnulados_SecaoId",
                table: "VotosAnulados",
                column: "SecaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosAnulados_VotoId",
                table: "VotosAnulados",
                column: "VotoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosAnulados_ZonaId",
                table: "VotosAnulados",
                column: "ZonaId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosBrancos_ApuracaoId",
                table: "VotosBrancos",
                column: "ApuracaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosBrancos_EleicaoId",
                table: "VotosBrancos",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosBrancos_RegionalId",
                table: "VotosBrancos",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosBrancos_SecaoId",
                table: "VotosBrancos",
                column: "SecaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosBrancos_ZonaId",
                table: "VotosBrancos",
                column: "ZonaId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosChapa_BoletimUrnaId",
                table: "VotosChapa",
                column: "BoletimUrnaId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosChapa_ChapaId",
                table: "VotosChapa",
                column: "ChapaId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosChapa_ResultadoFinalId",
                table: "VotosChapa",
                column: "ResultadoFinalId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosChapa_ResultadoId",
                table: "VotosChapa",
                column: "ResultadoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosChapa_ResultadoParcialId",
                table: "VotosChapa",
                column: "ResultadoParcialId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosEmenda_EmendaId",
                table: "VotosEmenda",
                column: "EmendaId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosEmenda_MembroComissaoId",
                table: "VotosEmenda",
                column: "MembroComissaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosJulgamentoFinal_JulgamentoId",
                table: "VotosJulgamentoFinal",
                column: "JulgamentoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosJulgamentoFinal_MembroComissaoId",
                table: "VotosJulgamentoFinal",
                column: "MembroComissaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosNulos_ApuracaoId",
                table: "VotosNulos",
                column: "ApuracaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosNulos_EleicaoId",
                table: "VotosNulos",
                column: "EleicaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosNulos_RegionalId",
                table: "VotosNulos",
                column: "RegionalId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosNulos_SecaoId",
                table: "VotosNulos",
                column: "SecaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosNulos_ZonaId",
                table: "VotosNulos",
                column: "ZonaId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosPlenario_JulgamentoRecursoSegundaInstanciaId",
                table: "VotosPlenario",
                column: "JulgamentoRecursoSegundaInstanciaId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosPlenario_MembroComissaoId",
                table: "VotosPlenario",
                column: "MembroComissaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosRelator_RelatorId",
                table: "VotosRelator",
                column: "RelatorId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosRelator_SessaoId",
                table: "VotosRelator",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosRevisor_RevisorId",
                table: "VotosRevisor",
                column: "RevisorId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosRevisor_SessaoId",
                table: "VotosRevisor",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosVogal_SessaoId",
                table: "VotosVogal",
                column: "SessaoId");

            migrationBuilder.CreateIndex(
                name: "IX_VotosVogal_VogalId",
                table: "VotosVogal",
                column: "VogalId");

            migrationBuilder.CreateIndex(
                name: "IX_ZonasEleitorais_CircunscricaoId",
                table: "ZonasEleitorais",
                column: "CircunscricaoId");

            migrationBuilder.AddForeignKey(
                name: "FK_JulgamentosRecursoSegundaInstancia_RecursosSegundaInstancia_RecursoSegundaInstanciaId",
                table: "JulgamentosRecursoSegundaInstancia",
                column: "RecursoSegundaInstanciaId",
                principalTable: "RecursosSegundaInstancia",
                principalColumn: "Id",
                onDelete: ReferentialAction.Cascade);

            migrationBuilder.AddForeignKey(
                name: "FK_RecursosJulgamentoFinal_RecursosSegundaInstancia_RecursoSegundaInstanciaId",
                table: "RecursosJulgamentoFinal",
                column: "RecursoSegundaInstanciaId",
                principalTable: "RecursosSegundaInstancia",
                principalColumn: "Id",
                onDelete: ReferentialAction.SetNull);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_RecursosJulgamentoFinal_MembrosComissaoJulgadora_RelatorId",
                table: "RecursosJulgamentoFinal");

            migrationBuilder.DropForeignKey(
                name: "FK_RecursosSegundaInstancia_MembrosComissaoJulgadora_RelatorId",
                table: "RecursosSegundaInstancia");

            migrationBuilder.DropForeignKey(
                name: "FK_RecursosSegundaInstancia_MembrosComissaoJulgadora_RevisorId",
                table: "RecursosSegundaInstancia");

            migrationBuilder.DropForeignKey(
                name: "FK_RecursosJulgamentoFinal_JulgamentosFinais_JulgamentoFinalId",
                table: "RecursosJulgamentoFinal");

            migrationBuilder.DropForeignKey(
                name: "FK_ComissoesJulgadoras_Eleicoes_EleicaoId",
                table: "ComissoesJulgadoras");

            migrationBuilder.DropForeignKey(
                name: "FK_RecursosJulgamentoFinal_RecursosSegundaInstancia_RecursoSegundaInstanciaId",
                table: "RecursosJulgamentoFinal");

            migrationBuilder.DropTable(
                name: "AcordaosJulgamento");

            migrationBuilder.DropTable(
                name: "AdmissibilidadesDenuncia");

            migrationBuilder.DropTable(
                name: "AnalisesDenuncia");

            migrationBuilder.DropTable(
                name: "AnexosDenuncia");

            migrationBuilder.DropTable(
                name: "ApuracaoResultadosChapa");

            migrationBuilder.DropTable(
                name: "ArquivamentosJulgamento");

            migrationBuilder.DropTable(
                name: "ArquivosDefesa");

            migrationBuilder.DropTable(
                name: "ArquivosDenuncia");

            migrationBuilder.DropTable(
                name: "ArquivosDocumento");

            migrationBuilder.DropTable(
                name: "ArquivosJulgamento");

            migrationBuilder.DropTable(
                name: "ArquivosPedidoImpugnacao");

            migrationBuilder.DropTable(
                name: "AssinaturasDigitais");

            migrationBuilder.DropTable(
                name: "AtasSessao");

            migrationBuilder.DropTable(
                name: "AtividadesSecundariasCalendario");

            migrationBuilder.DropTable(
                name: "AuditoriaLogs");

            migrationBuilder.DropTable(
                name: "CalendarioSituacoes");

            migrationBuilder.DropTable(
                name: "CertidoesJulgamento");

            migrationBuilder.DropTable(
                name: "ComposicoesChapa");

            migrationBuilder.DropTable(
                name: "Comunicados");

            migrationBuilder.DropTable(
                name: "Configuracoes");

            migrationBuilder.DropTable(
                name: "ConfiguracoesEleicao");

            migrationBuilder.DropTable(
                name: "ConfirmacoesMembrosChapa");

            migrationBuilder.DropTable(
                name: "ContraAlegacoesDenuncia");

            migrationBuilder.DropTable(
                name: "ContraAlegacoesFinais");

            migrationBuilder.DropTable(
                name: "ContraAlegacoesImpugnacao");

            migrationBuilder.DropTable(
                name: "ContrarrazoesRecursoDenuncia");

            migrationBuilder.DropTable(
                name: "ContrarrazoesRecursoImpugnacao");

            migrationBuilder.DropTable(
                name: "DecisoesJulgamento");

            migrationBuilder.DropTable(
                name: "DefesasImpugnacao");

            migrationBuilder.DropTable(
                name: "DenunciaChapas");

            migrationBuilder.DropTable(
                name: "DenunciaMembros");

            migrationBuilder.DropTable(
                name: "DespachosDenuncia");

            migrationBuilder.DropTable(
                name: "DiligenciasJulgamento");

            migrationBuilder.DropTable(
                name: "DocumentosChapa");

            migrationBuilder.DropTable(
                name: "Editais");

            migrationBuilder.DropTable(
                name: "EleicaoSituacoes");

            migrationBuilder.DropTable(
                name: "Eleitores");

            migrationBuilder.DropTable(
                name: "EncaminhamentosDenuncia");

            migrationBuilder.DropTable(
                name: "EstatisticasEleicao");

            migrationBuilder.DropTable(
                name: "EtapasEleicao");

            migrationBuilder.DropTable(
                name: "ExportacoesDados");

            migrationBuilder.DropTable(
                name: "FasesEleicaoConfig");

            migrationBuilder.DropTable(
                name: "FiscaisEleicao");

            migrationBuilder.DropTable(
                name: "GraficosResultado");

            migrationBuilder.DropTable(
                name: "HistoricosChapa");

            migrationBuilder.DropTable(
                name: "HistoricosDenuncia");

            migrationBuilder.DropTable(
                name: "HistoricosExtratoConselheiro");

            migrationBuilder.DropTable(
                name: "HistoricosImpugnacao");

            migrationBuilder.DropTable(
                name: "ImportacoesDados");

            migrationBuilder.DropTable(
                name: "IntimacoesJulgamento");

            migrationBuilder.DropTable(
                name: "JulgamentosRecursoDenuncia");

            migrationBuilder.DropTable(
                name: "JulgamentosRecursoImpugnacao");

            migrationBuilder.DropTable(
                name: "LogsAcesso");

            migrationBuilder.DropTable(
                name: "MapasVotacao");

            migrationBuilder.DropTable(
                name: "Notificacoes");

            migrationBuilder.DropTable(
                name: "NotificacoesDenuncia");

            migrationBuilder.DropTable(
                name: "NotificacoesJulgamento");

            migrationBuilder.DropTable(
                name: "ObservacoesJulgamento");

            migrationBuilder.DropTable(
                name: "ParametrosEleicao");

            migrationBuilder.DropTable(
                name: "PareceresDenuncia");

            migrationBuilder.DropTable(
                name: "PareceristaProcuradores");

            migrationBuilder.DropTable(
                name: "PautasSessao");

            migrationBuilder.DropTable(
                name: "PlataformasEleitorais");

            migrationBuilder.DropTable(
                name: "ProvasDenuncia");

            migrationBuilder.DropTable(
                name: "ProvasImpugnacao");

            migrationBuilder.DropTable(
                name: "ProvasJulgamento");

            migrationBuilder.DropTable(
                name: "Publicacoes");

            migrationBuilder.DropTable(
                name: "PublicacoesJulgamento");

            migrationBuilder.DropTable(
                name: "PublicacoesOficiais");

            migrationBuilder.DropTable(
                name: "RelatoriosApuracao");

            migrationBuilder.DropTable(
                name: "RelatoriosJulgamento");

            migrationBuilder.DropTable(
                name: "RelatoriosVotacao");

            migrationBuilder.DropTable(
                name: "RolePermissoes");

            migrationBuilder.DropTable(
                name: "SubstituicoesJulgamentoFinal");

            migrationBuilder.DropTable(
                name: "SubstituicoesMembrosChapa");

            migrationBuilder.DropTable(
                name: "SuspensoesJulgamento");

            migrationBuilder.DropTable(
                name: "TemplatesDocumento");

            migrationBuilder.DropTable(
                name: "TiposEleicaoConfig");

            migrationBuilder.DropTable(
                name: "TotaisVotos");

            migrationBuilder.DropTable(
                name: "UsuarioRoles");

            migrationBuilder.DropTable(
                name: "VistasDenuncia");

            migrationBuilder.DropTable(
                name: "VotacoesJulgamentoDenuncia");

            migrationBuilder.DropTable(
                name: "VotacoesJulgamentoImpugnacao");

            migrationBuilder.DropTable(
                name: "VotosAnulados");

            migrationBuilder.DropTable(
                name: "VotosBrancos");

            migrationBuilder.DropTable(
                name: "VotosChapa");

            migrationBuilder.DropTable(
                name: "VotosEmenda");

            migrationBuilder.DropTable(
                name: "VotosJulgamentoFinal");

            migrationBuilder.DropTable(
                name: "VotosNulos");

            migrationBuilder.DropTable(
                name: "VotosPlenario");

            migrationBuilder.DropTable(
                name: "VotosRelator");

            migrationBuilder.DropTable(
                name: "VotosRevisor");

            migrationBuilder.DropTable(
                name: "VotosVogal");

            migrationBuilder.DropTable(
                name: "ApuracaoResultados");

            migrationBuilder.DropTable(
                name: "DefesasDenuncia");

            migrationBuilder.DropTable(
                name: "PedidosImpugnacao");

            migrationBuilder.DropTable(
                name: "AtasApuracao");

            migrationBuilder.DropTable(
                name: "AtasReuniao");

            migrationBuilder.DropTable(
                name: "CarimbosTempo");

            migrationBuilder.DropTable(
                name: "CertificadosDigitais");

            migrationBuilder.DropTable(
                name: "Certificados");

            migrationBuilder.DropTable(
                name: "Declaracoes");

            migrationBuilder.DropTable(
                name: "Diplomas");

            migrationBuilder.DropTable(
                name: "TermosPosse");

            migrationBuilder.DropTable(
                name: "Termos");

            migrationBuilder.DropTable(
                name: "AtividadesPrincipaisCalendario");

            migrationBuilder.DropTable(
                name: "AlegacoesDenuncia");

            migrationBuilder.DropTable(
                name: "AlegacoesFinais");

            migrationBuilder.DropTable(
                name: "MesasReceptoras");

            migrationBuilder.DropTable(
                name: "RecursosDenuncia");

            migrationBuilder.DropTable(
                name: "RecursosImpugnacao");

            migrationBuilder.DropTable(
                name: "AlegacoesImpugnacao");

            migrationBuilder.DropTable(
                name: "Atos");

            migrationBuilder.DropTable(
                name: "Avisos");

            migrationBuilder.DropTable(
                name: "Convocacoes");

            migrationBuilder.DropTable(
                name: "Deliberacoes");

            migrationBuilder.DropTable(
                name: "Documentos");

            migrationBuilder.DropTable(
                name: "Normativas");

            migrationBuilder.DropTable(
                name: "Portarias");

            migrationBuilder.DropTable(
                name: "Resolucoes");

            migrationBuilder.DropTable(
                name: "Permissoes");

            migrationBuilder.DropTable(
                name: "ModelosDocumento");

            migrationBuilder.DropTable(
                name: "Roles");

            migrationBuilder.DropTable(
                name: "JulgamentosDenuncia");

            migrationBuilder.DropTable(
                name: "JulgamentosImpugnacao");

            migrationBuilder.DropTable(
                name: "Votos");

            migrationBuilder.DropTable(
                name: "BoletinsUrna");

            migrationBuilder.DropTable(
                name: "ResultadosFinais");

            migrationBuilder.DropTable(
                name: "ResultadosParciais");

            migrationBuilder.DropTable(
                name: "EmendasJulgamento");

            migrationBuilder.DropTable(
                name: "RegistrosApuracaoVotos");

            migrationBuilder.DropTable(
                name: "JulgamentosRecursoSegundaInstancia");

            migrationBuilder.DropTable(
                name: "ResultadosEleicao");

            migrationBuilder.DropTable(
                name: "Calendarios");

            migrationBuilder.DropTable(
                name: "CategoriasDocumento");

            migrationBuilder.DropTable(
                name: "Denuncias");

            migrationBuilder.DropTable(
                name: "Impugnacoes");

            migrationBuilder.DropTable(
                name: "UrnasEletronicas");

            migrationBuilder.DropTable(
                name: "MembrosChapa");

            migrationBuilder.DropTable(
                name: "RegioesPleito");

            migrationBuilder.DropTable(
                name: "SecoesEleitorais");

            migrationBuilder.DropTable(
                name: "Chapas");

            migrationBuilder.DropTable(
                name: "ZonasEleitorais");

            migrationBuilder.DropTable(
                name: "Circunscricoes");

            migrationBuilder.DropTable(
                name: "MembrosComissaoJulgadora");

            migrationBuilder.DropTable(
                name: "Conselheiros");

            migrationBuilder.DropTable(
                name: "Profissionais");

            migrationBuilder.DropTable(
                name: "Filiais");

            migrationBuilder.DropTable(
                name: "Usuarios");

            migrationBuilder.DropTable(
                name: "JulgamentosFinais");

            migrationBuilder.DropTable(
                name: "SessoesJulgamento");

            migrationBuilder.DropTable(
                name: "Eleicoes");

            migrationBuilder.DropTable(
                name: "RegionaisCAU");

            migrationBuilder.DropTable(
                name: "RecursosSegundaInstancia");

            migrationBuilder.DropTable(
                name: "ComissoesJulgadoras");

            migrationBuilder.DropTable(
                name: "RecursosJulgamentoFinal");
        }
    }
}
