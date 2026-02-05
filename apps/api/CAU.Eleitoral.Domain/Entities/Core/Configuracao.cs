using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Entidade para configuracoes do sistema
/// Armazena configuracoes globais do sistema eleitoral
/// </summary>
public class Configuracao : BaseEntity
{
    /// <summary>
    /// Chave unica da configuracao
    /// </summary>
    public string Chave { get; set; } = string.Empty;

    /// <summary>
    /// Valor da configuracao
    /// </summary>
    public string Valor { get; set; } = string.Empty;

    /// <summary>
    /// Descricao da configuracao
    /// </summary>
    public string? Descricao { get; set; }

    /// <summary>
    /// Tipo de dado da configuracao (string, int, bool, json)
    /// </summary>
    public string Tipo { get; set; } = "string";

    /// <summary>
    /// Categoria da configuracao (Geral, Email, Seguranca, Votacao)
    /// </summary>
    public string Categoria { get; set; } = "Geral";

    /// <summary>
    /// Indica se a configuracao pode ser editada pelo usuario
    /// </summary>
    public bool Editavel { get; set; } = true;

    /// <summary>
    /// Indica se a configuracao esta ativa
    /// </summary>
    public bool Ativo { get; set; } = true;

    /// <summary>
    /// Valor padrao da configuracao
    /// </summary>
    public string? ValorPadrao { get; set; }

    /// <summary>
    /// Id do usuario que atualizou pela ultima vez
    /// </summary>
    public Guid? UltimoUsuarioAtualizacaoId { get; set; }
}

/// <summary>
/// Categorias de configuracao do sistema
/// </summary>
public static class CategoriaConfiguracao
{
    public const string Geral = "Geral";
    public const string Email = "Email";
    public const string Seguranca = "Seguranca";
    public const string Votacao = "Votacao";
    public const string Sistema = "Sistema";
}

/// <summary>
/// Chaves de configuracao padrao do sistema
/// </summary>
public static class ChavesConfiguracao
{
    // Geral
    public const string NomeSistema = "geral.nome_sistema";
    public const string Versao = "geral.versao";
    public const string LogoUrl = "geral.logo_url";
    public const string FaviconUrl = "geral.favicon_url";
    public const string CorPrimaria = "geral.cor_primaria";
    public const string CorSecundaria = "geral.cor_secundaria";
    public const string ModoManutencao = "geral.modo_manutencao";
    public const string MensagemManutencao = "geral.mensagem_manutencao";
    public const string TimeZone = "geral.timezone";
    public const string Locale = "geral.locale";

    // Email
    public const string SmtpHost = "email.smtp_host";
    public const string SmtpPort = "email.smtp_port";
    public const string SmtpUseSsl = "email.smtp_use_ssl";
    public const string SmtpUsername = "email.smtp_username";
    public const string SmtpPassword = "email.smtp_password";
    public const string EmailRemetente = "email.remetente";
    public const string NomeRemetente = "email.nome_remetente";
    public const string EmailHabilitado = "email.habilitado";

    // Seguranca
    public const string TentativasLoginMax = "seguranca.tentativas_login_max";
    public const string TempoBloqueioConta = "seguranca.tempo_bloqueio_conta";
    public const string ExpiracaoSenhaEmDias = "seguranca.expiracao_senha_dias";
    public const string TamanhoMinimoSenha = "seguranca.tamanho_minimo_senha";
    public const string RequerLetraMaiuscula = "seguranca.requer_letra_maiuscula";
    public const string RequerNumero = "seguranca.requer_numero";
    public const string RequerCaractereEspecial = "seguranca.requer_caractere_especial";
    public const string ExpiracaoTokenEmMinutos = "seguranca.expiracao_token_minutos";
    public const string ExpiracaoRefreshTokenEmDias = "seguranca.expiracao_refresh_token_dias";
    public const string DoisFatoresObrigatorio = "seguranca.dois_fatores_obrigatorio";

    // Votacao
    public const string PermitirVotoBranco = "votacao.permitir_voto_branco";
    public const string PermitirVotoNulo = "votacao.permitir_voto_nulo";
    public const string MostrarResultadoParcial = "votacao.mostrar_resultado_parcial";
    public const string NotificarVotoRegistrado = "votacao.notificar_voto_registrado";
    public const string TempoSessaoVotacaoEmMinutos = "votacao.tempo_sessao_minutos";
    public const string ConfirmacaoVotoObrigatoria = "votacao.confirmacao_voto_obrigatoria";
    public const string MensagemVotacao = "votacao.mensagem_votacao";
    public const string MensagemConfirmacao = "votacao.mensagem_confirmacao";
}
