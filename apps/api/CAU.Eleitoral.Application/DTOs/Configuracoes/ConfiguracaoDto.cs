namespace CAU.Eleitoral.Application.DTOs.Configuracoes;

/// <summary>
/// DTO para configuracoes completas do sistema
/// </summary>
public record ConfiguracaoSistemaDto
{
    public ConfiguracaoGeralDto Geral { get; init; } = new();
    public ConfiguracaoEmailDto Email { get; init; } = new();
    public ConfiguracaoSegurancaDto Seguranca { get; init; } = new();
    public ConfiguracaoVotacaoDto Votacao { get; init; } = new();
}

/// <summary>
/// DTO para configuracoes gerais
/// </summary>
public record ConfiguracaoGeralDto
{
    public string NomeSistema { get; init; } = "CAU Sistema Eleitoral";
    public string Versao { get; init; } = "1.0.0";
    public string? LogoUrl { get; init; }
    public string? FaviconUrl { get; init; }
    public string? CorPrimaria { get; init; } = "#1E40AF";
    public string? CorSecundaria { get; init; } = "#3B82F6";
    public bool ModoManutencao { get; init; }
    public string? MensagemManutencao { get; init; }
    public string TimeZone { get; init; } = "America/Sao_Paulo";
    public string Locale { get; init; } = "pt-BR";
}

/// <summary>
/// DTO para configuracoes de email
/// </summary>
public record ConfiguracaoEmailDto
{
    public string SmtpHost { get; init; } = string.Empty;
    public int SmtpPort { get; init; } = 587;
    public bool SmtpUseSsl { get; init; } = true;
    public string? SmtpUsername { get; init; }
    public string? SmtpPassword { get; init; }
    public string EmailRemetente { get; init; } = string.Empty;
    public string NomeRemetente { get; init; } = "CAU Sistema Eleitoral";
    public bool EmailHabilitado { get; init; }
}

/// <summary>
/// DTO para configuracoes de seguranca
/// </summary>
public record ConfiguracaoSegurancaDto
{
    public int TentativasLoginMax { get; init; } = 5;
    public int TempoBloqueioConta { get; init; } = 30;
    public int ExpiracaoSenhaEmDias { get; init; } = 90;
    public int TamanhoMinimoSenha { get; init; } = 8;
    public bool RequerLetraMaiuscula { get; init; } = true;
    public bool RequerNumero { get; init; } = true;
    public bool RequerCaractereEspecial { get; init; } = true;
    public int ExpiracaoTokenEmMinutos { get; init; } = 60;
    public int ExpiracaoRefreshTokenEmDias { get; init; } = 7;
    public bool DoisFatoresObrigatorio { get; init; }
}

/// <summary>
/// DTO para configuracoes de votacao
/// </summary>
public record ConfiguracaoVotacaoDto
{
    public bool PermitirVotoBranco { get; init; } = true;
    public bool PermitirVotoNulo { get; init; } = true;
    public bool MostrarResultadoParcial { get; init; }
    public bool NotificarVotoRegistrado { get; init; } = true;
    public int TempoSessaoVotacaoEmMinutos { get; init; } = 30;
    public bool ConfirmacaoVotoObrigatoria { get; init; } = true;
    public string? MensagemVotacao { get; init; }
    public string? MensagemConfirmacao { get; init; }
}

/// <summary>
/// DTO para um item individual de configuracao
/// </summary>
public record ConfiguracaoItemDto
{
    public string Chave { get; init; } = string.Empty;
    public string Valor { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string Tipo { get; init; } = "string";
    public string Categoria { get; init; } = "Geral";
    public bool Editavel { get; init; } = true;
    public DateTime? UltimaAtualizacao { get; init; }
    public string? AtualizadoPor { get; init; }
}

/// <summary>
/// DTO para atualizar uma configuracao
/// </summary>
public record UpdateConfiguracaoDto
{
    public string Valor { get; init; } = string.Empty;
}

/// <summary>
/// DTO para atualizar multiplas configuracoes
/// </summary>
public record UpdateMultiplasConfiguracoesDto
{
    public Dictionary<string, string> Configuracoes { get; init; } = new();
}

/// <summary>
/// DTO para requisicao de teste de email
/// </summary>
public record TestarEmailRequest
{
    public string EmailDestino { get; init; } = string.Empty;
}

/// <summary>
/// DTO para role/papel do sistema
/// </summary>
public record RoleDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public List<string> Permissoes { get; init; } = new();
    public bool Editavel { get; init; } = true;
    public bool Ativo { get; init; } = true;
}

/// <summary>
/// DTO para informacoes do sistema
/// </summary>
public record InfoSistemaDto
{
    public string Nome { get; init; } = "CAU Sistema Eleitoral";
    public string Versao { get; init; } = "1.0.0";
    public string Ambiente { get; init; } = "Development";
    public DateTime DataHoraServidor { get; init; } = DateTime.UtcNow;
    public string TimeZone { get; init; } = "America/Sao_Paulo";
    public bool EmManutencao { get; init; }
    public string? MensagemManutencao { get; init; }
}

/// <summary>
/// DTO para configuracoes padrao de eleicao
/// </summary>
public record ConfiguracaoEleicaoDefaultDto
{
    public int DuracaoMandatoAnos { get; init; } = 3;
    public int QuantidadeVagasPadrao { get; init; } = 9;
    public int QuantidadeSuplementesPadrao { get; init; } = 3;
    public int DiasInscricaoChapa { get; init; } = 30;
    public int DiasImpugnacao { get; init; } = 5;
    public int DiasRecurso { get; init; } = 5;
    public int DiasDefesa { get; init; } = 5;
    public int DiasVotacao { get; init; } = 1;
    public int DiasApuracao { get; init; } = 1;
    public bool PermitirVotacaoOnline { get; init; } = true;
    public bool PermitirVotacaoPresencial { get; init; } = true;
    public bool ExigirQuorum { get; init; } = true;
    public decimal PercentualQuorumMinimo { get; init; } = 50;
    public bool PermitirVotoBranco { get; init; } = true;
    public bool PermitirVotoNulo { get; init; } = true;
    public bool NotificarInscricaoChapa { get; init; } = true;
    public bool NotificarAprovacaoChapa { get; init; } = true;
    public bool NotificarInicioVotacao { get; init; } = true;
    public bool NotificarResultado { get; init; } = true;
    public string? TemplateEmailInscricao { get; init; }
    public string? TemplateEmailAprovacao { get; init; }
    public string? TemplateEmailVotacao { get; init; }
    public string? TemplateEmailResultado { get; init; }
}

/// <summary>
/// DTO para configuracao especifica de eleicao
/// </summary>
public record ConfiguracaoEleicaoItemDto
{
    public Guid Id { get; init; }
    public Guid EleicaoId { get; init; }
    public string Chave { get; init; } = string.Empty;
    public string Valor { get; init; } = string.Empty;
    public string? Descricao { get; init; }
    public string? Tipo { get; init; }
    public bool Ativo { get; init; } = true;
}
