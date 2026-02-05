using System.Text;
using System.Text.Json;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Configuracoes;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

/// <summary>
/// Servico de configuracoes do sistema
/// </summary>
public class ConfiguracaoService : IConfiguracaoService
{
    private readonly IRepository<Configuracao> _configuracaoRepository;
    private readonly IRepository<ConfiguracaoEleicao> _configuracaoEleicaoRepository;
    private readonly IRepository<Role> _roleRepository;
    private readonly IRepository<Usuario> _usuarioRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly IConfiguration _configuration;
    private readonly ILogger<ConfiguracaoService> _logger;

    public ConfiguracaoService(
        IRepository<Configuracao> configuracaoRepository,
        IRepository<ConfiguracaoEleicao> configuracaoEleicaoRepository,
        IRepository<Role> roleRepository,
        IRepository<Usuario> usuarioRepository,
        IUnitOfWork unitOfWork,
        IConfiguration configuration,
        ILogger<ConfiguracaoService> logger)
    {
        _configuracaoRepository = configuracaoRepository;
        _configuracaoEleicaoRepository = configuracaoEleicaoRepository;
        _roleRepository = roleRepository;
        _usuarioRepository = usuarioRepository;
        _unitOfWork = unitOfWork;
        _configuration = configuration;
        _logger = logger;
    }

    public async Task<ConfiguracaoSistemaDto> GetAllAsync(CancellationToken cancellationToken = default)
    {
        await EnsureDefaultConfiguracoesAsync(cancellationToken);

        var configuracoes = await _configuracaoRepository.Query()
            .Where(c => c.Ativo)
            .ToListAsync(cancellationToken);

        return new ConfiguracaoSistemaDto
        {
            Geral = MapToGeralDto(configuracoes),
            Email = MapToEmailDto(configuracoes),
            Seguranca = MapToSegurancaDto(configuracoes),
            Votacao = MapToVotacaoDto(configuracoes)
        };
    }

    public async Task<ConfiguracaoItemDto?> GetByChaveAsync(string chave, CancellationToken cancellationToken = default)
    {
        var configuracao = await _configuracaoRepository.Query()
            .FirstOrDefaultAsync(c => c.Chave == chave && c.Ativo, cancellationToken);

        if (configuracao == null)
            return null;

        string? atualizadoPor = null;
        if (configuracao.UltimoUsuarioAtualizacaoId.HasValue)
        {
            var usuario = await _usuarioRepository.GetByIdAsync(configuracao.UltimoUsuarioAtualizacaoId.Value, cancellationToken);
            atualizadoPor = usuario?.Nome;
        }

        return new ConfiguracaoItemDto
        {
            Chave = configuracao.Chave,
            Valor = configuracao.Valor,
            Descricao = configuracao.Descricao,
            Tipo = configuracao.Tipo,
            Categoria = configuracao.Categoria,
            Editavel = configuracao.Editavel,
            UltimaAtualizacao = configuracao.UpdatedAt,
            AtualizadoPor = atualizadoPor
        };
    }

    public async Task<ConfiguracaoItemDto> UpdateAsync(string chave, string valor, Guid userId, CancellationToken cancellationToken = default)
    {
        var configuracao = await _configuracaoRepository.Query()
            .FirstOrDefaultAsync(c => c.Chave == chave, cancellationToken)
            ?? throw new KeyNotFoundException($"Configuracao '{chave}' nao encontrada");

        if (!configuracao.Editavel)
            throw new InvalidOperationException($"Configuracao '{chave}' nao pode ser editada");

        configuracao.Valor = valor;
        configuracao.UltimoUsuarioAtualizacaoId = userId;
        configuracao.UpdatedBy = userId.ToString();

        await _configuracaoRepository.UpdateAsync(configuracao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Configuracao {Chave} atualizada pelo usuario {UserId}", chave, userId);

        var usuario = await _usuarioRepository.GetByIdAsync(userId, cancellationToken);

        return new ConfiguracaoItemDto
        {
            Chave = configuracao.Chave,
            Valor = configuracao.Valor,
            Descricao = configuracao.Descricao,
            Tipo = configuracao.Tipo,
            Categoria = configuracao.Categoria,
            Editavel = configuracao.Editavel,
            UltimaAtualizacao = configuracao.UpdatedAt,
            AtualizadoPor = usuario?.Nome
        };
    }

    public async Task<ConfiguracaoSistemaDto> UpdateMultipleAsync(Dictionary<string, string> configuracoes, Guid userId, CancellationToken cancellationToken = default)
    {
        foreach (var (chave, valor) in configuracoes)
        {
            var configuracao = await _configuracaoRepository.Query()
                .FirstOrDefaultAsync(c => c.Chave == chave, cancellationToken);

            if (configuracao != null && configuracao.Editavel)
            {
                configuracao.Valor = valor;
                configuracao.UltimoUsuarioAtualizacaoId = userId;
                configuracao.UpdatedBy = userId.ToString();
                await _configuracaoRepository.UpdateAsync(configuracao, cancellationToken);
            }
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);
        _logger.LogInformation("{Count} configuracoes atualizadas pelo usuario {UserId}", configuracoes.Count, userId);

        return await GetAllAsync(cancellationToken);
    }

    public async Task<ConfiguracaoEmailDto> GetEmailConfigAsync(CancellationToken cancellationToken = default)
    {
        await EnsureDefaultConfiguracoesAsync(cancellationToken);

        var configuracoes = await _configuracaoRepository.Query()
            .Where(c => c.Categoria == CategoriaConfiguracao.Email && c.Ativo)
            .ToListAsync(cancellationToken);

        return MapToEmailDto(configuracoes);
    }

    public async Task<ConfiguracaoEmailDto> UpdateEmailConfigAsync(ConfiguracaoEmailDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var updates = new Dictionary<string, string>
        {
            { ChavesConfiguracao.SmtpHost, dto.SmtpHost },
            { ChavesConfiguracao.SmtpPort, dto.SmtpPort.ToString() },
            { ChavesConfiguracao.SmtpUseSsl, dto.SmtpUseSsl.ToString().ToLower() },
            { ChavesConfiguracao.SmtpUsername, dto.SmtpUsername ?? "" },
            { ChavesConfiguracao.SmtpPassword, dto.SmtpPassword ?? "" },
            { ChavesConfiguracao.EmailRemetente, dto.EmailRemetente },
            { ChavesConfiguracao.NomeRemetente, dto.NomeRemetente },
            { ChavesConfiguracao.EmailHabilitado, dto.EmailHabilitado.ToString().ToLower() }
        };

        await UpdateMultipleAsync(updates, userId, cancellationToken);

        return await GetEmailConfigAsync(cancellationToken);
    }

    public async Task<bool> TestarEmailAsync(string emailDestino, CancellationToken cancellationToken = default)
    {
        // Implementacao simplificada - em producao integrar com servico de email
        _logger.LogInformation("Teste de email solicitado para {EmailDestino}", emailDestino);

        // Simular envio de email
        await Task.Delay(1000, cancellationToken);

        return true;
    }

    public async Task<ConfiguracaoSegurancaDto> GetSegurancaConfigAsync(CancellationToken cancellationToken = default)
    {
        await EnsureDefaultConfiguracoesAsync(cancellationToken);

        var configuracoes = await _configuracaoRepository.Query()
            .Where(c => c.Categoria == CategoriaConfiguracao.Seguranca && c.Ativo)
            .ToListAsync(cancellationToken);

        return MapToSegurancaDto(configuracoes);
    }

    public async Task<ConfiguracaoSegurancaDto> UpdateSegurancaConfigAsync(ConfiguracaoSegurancaDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var updates = new Dictionary<string, string>
        {
            { ChavesConfiguracao.TentativasLoginMax, dto.TentativasLoginMax.ToString() },
            { ChavesConfiguracao.TempoBloqueioConta, dto.TempoBloqueioConta.ToString() },
            { ChavesConfiguracao.ExpiracaoSenhaEmDias, dto.ExpiracaoSenhaEmDias.ToString() },
            { ChavesConfiguracao.TamanhoMinimoSenha, dto.TamanhoMinimoSenha.ToString() },
            { ChavesConfiguracao.RequerLetraMaiuscula, dto.RequerLetraMaiuscula.ToString().ToLower() },
            { ChavesConfiguracao.RequerNumero, dto.RequerNumero.ToString().ToLower() },
            { ChavesConfiguracao.RequerCaractereEspecial, dto.RequerCaractereEspecial.ToString().ToLower() },
            { ChavesConfiguracao.ExpiracaoTokenEmMinutos, dto.ExpiracaoTokenEmMinutos.ToString() },
            { ChavesConfiguracao.ExpiracaoRefreshTokenEmDias, dto.ExpiracaoRefreshTokenEmDias.ToString() },
            { ChavesConfiguracao.DoisFatoresObrigatorio, dto.DoisFatoresObrigatorio.ToString().ToLower() }
        };

        await UpdateMultipleAsync(updates, userId, cancellationToken);

        return await GetSegurancaConfigAsync(cancellationToken);
    }

    public async Task<ConfiguracaoVotacaoDto> GetVotacaoConfigAsync(CancellationToken cancellationToken = default)
    {
        await EnsureDefaultConfiguracoesAsync(cancellationToken);

        var configuracoes = await _configuracaoRepository.Query()
            .Where(c => c.Categoria == CategoriaConfiguracao.Votacao && c.Ativo)
            .ToListAsync(cancellationToken);

        return MapToVotacaoDto(configuracoes);
    }

    public async Task<ConfiguracaoVotacaoDto> UpdateVotacaoConfigAsync(ConfiguracaoVotacaoDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var updates = new Dictionary<string, string>
        {
            { ChavesConfiguracao.PermitirVotoBranco, dto.PermitirVotoBranco.ToString().ToLower() },
            { ChavesConfiguracao.PermitirVotoNulo, dto.PermitirVotoNulo.ToString().ToLower() },
            { ChavesConfiguracao.MostrarResultadoParcial, dto.MostrarResultadoParcial.ToString().ToLower() },
            { ChavesConfiguracao.NotificarVotoRegistrado, dto.NotificarVotoRegistrado.ToString().ToLower() },
            { ChavesConfiguracao.TempoSessaoVotacaoEmMinutos, dto.TempoSessaoVotacaoEmMinutos.ToString() },
            { ChavesConfiguracao.ConfirmacaoVotoObrigatoria, dto.ConfirmacaoVotoObrigatoria.ToString().ToLower() },
            { ChavesConfiguracao.MensagemVotacao, dto.MensagemVotacao ?? "" },
            { ChavesConfiguracao.MensagemConfirmacao, dto.MensagemConfirmacao ?? "" }
        };

        await UpdateMultipleAsync(updates, userId, cancellationToken);

        return await GetVotacaoConfigAsync(cancellationToken);
    }

    public async Task<ConfiguracaoSistemaDto> RestaurarPadraoAsync(Guid userId, CancellationToken cancellationToken = default)
    {
        var configuracoes = await _configuracaoRepository.Query()
            .Where(c => c.Editavel && c.ValorPadrao != null)
            .ToListAsync(cancellationToken);

        foreach (var config in configuracoes)
        {
            config.Valor = config.ValorPadrao!;
            config.UltimoUsuarioAtualizacaoId = userId;
            config.UpdatedBy = userId.ToString();
            await _configuracaoRepository.UpdateAsync(config, cancellationToken);
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);
        _logger.LogInformation("Configuracoes restauradas para padrao pelo usuario {UserId}", userId);

        return await GetAllAsync(cancellationToken);
    }

    public async Task<(byte[] Content, string ContentType, string FileName)> ExportarAsync(CancellationToken cancellationToken = default)
    {
        var configuracoes = await _configuracaoRepository.Query()
            .Where(c => c.Ativo && c.Editavel)
            .Select(c => new { c.Chave, c.Valor, c.Categoria, c.Tipo })
            .ToListAsync(cancellationToken);

        var json = JsonSerializer.Serialize(configuracoes, new JsonSerializerOptions
        {
            WriteIndented = true
        });

        var content = Encoding.UTF8.GetBytes(json);
        var fileName = $"configuracoes_cau_{DateTime.UtcNow:yyyyMMdd_HHmmss}.json";

        return (content, "application/json", fileName);
    }

    public async Task<ConfiguracaoSistemaDto> ImportarAsync(Stream stream, Guid userId, CancellationToken cancellationToken = default)
    {
        using var reader = new StreamReader(stream);
        var json = await reader.ReadToEndAsync(cancellationToken);

        var importados = JsonSerializer.Deserialize<List<ConfiguracaoImportDto>>(json)
            ?? throw new InvalidOperationException("Formato de arquivo invalido");

        foreach (var item in importados)
        {
            var configuracao = await _configuracaoRepository.Query()
                .FirstOrDefaultAsync(c => c.Chave == item.Chave, cancellationToken);

            if (configuracao != null && configuracao.Editavel)
            {
                configuracao.Valor = item.Valor;
                configuracao.UltimoUsuarioAtualizacaoId = userId;
                configuracao.UpdatedBy = userId.ToString();
                await _configuracaoRepository.UpdateAsync(configuracao, cancellationToken);
            }
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);
        _logger.LogInformation("{Count} configuracoes importadas pelo usuario {UserId}", importados.Count, userId);

        return await GetAllAsync(cancellationToken);
    }

    public async Task<IEnumerable<RoleDto>> GetRolesAsync(CancellationToken cancellationToken = default)
    {
        var roles = await _roleRepository.Query()
            .Include(r => r.RolePermissoes)
            .ThenInclude(rp => rp.Permissao)
            .Where(r => r.Ativo)
            .ToListAsync(cancellationToken);

        return roles.Select(r => new RoleDto
        {
            Id = r.Id,
            Nome = r.Nome,
            Descricao = r.Descricao,
            Permissoes = r.RolePermissoes.Select(rp => rp.Permissao.Nome).ToList(),
            Editavel = !r.SistemaRole,
            Ativo = r.Ativo
        });
    }

    public async Task<InfoSistemaDto> GetInfoSistemaAsync(CancellationToken cancellationToken = default)
    {
        await EnsureDefaultConfiguracoesAsync(cancellationToken);

        var configuracoes = await _configuracaoRepository.Query()
            .Where(c => c.Categoria == CategoriaConfiguracao.Geral && c.Ativo)
            .ToListAsync(cancellationToken);

        var ambiente = _configuration["ASPNETCORE_ENVIRONMENT"] ?? "Development";

        return new InfoSistemaDto
        {
            Nome = GetValue(configuracoes, ChavesConfiguracao.NomeSistema, "CAU Sistema Eleitoral"),
            Versao = GetValue(configuracoes, ChavesConfiguracao.Versao, "1.0.0"),
            Ambiente = ambiente,
            DataHoraServidor = DateTime.UtcNow,
            TimeZone = GetValue(configuracoes, ChavesConfiguracao.TimeZone, "America/Sao_Paulo"),
            EmManutencao = GetBoolValue(configuracoes, ChavesConfiguracao.ModoManutencao, false),
            MensagemManutencao = GetValue(configuracoes, ChavesConfiguracao.MensagemManutencao, null)
        };
    }

    public async Task<ConfiguracaoEleicaoDefaultDto> GetEleicaoConfigAsync(CancellationToken cancellationToken = default)
    {
        await EnsureDefaultConfiguracoesAsync(cancellationToken);

        var configuracoes = await _configuracaoRepository.Query()
            .Where(c => c.Categoria == "Eleicao" && c.Ativo)
            .ToListAsync(cancellationToken);

        return new ConfiguracaoEleicaoDefaultDto
        {
            DuracaoMandatoAnos = GetIntValue(configuracoes, "eleicao.duracao_mandato_anos", 3),
            QuantidadeVagasPadrao = GetIntValue(configuracoes, "eleicao.quantidade_vagas_padrao", 9),
            QuantidadeSuplementesPadrao = GetIntValue(configuracoes, "eleicao.quantidade_suplentes_padrao", 3),
            DiasInscricaoChapa = GetIntValue(configuracoes, "eleicao.dias_inscricao_chapa", 30),
            DiasImpugnacao = GetIntValue(configuracoes, "eleicao.dias_impugnacao", 5),
            DiasRecurso = GetIntValue(configuracoes, "eleicao.dias_recurso", 5),
            DiasDefesa = GetIntValue(configuracoes, "eleicao.dias_defesa", 5),
            DiasVotacao = GetIntValue(configuracoes, "eleicao.dias_votacao", 1),
            DiasApuracao = GetIntValue(configuracoes, "eleicao.dias_apuracao", 1),
            PermitirVotacaoOnline = GetBoolValue(configuracoes, "eleicao.permitir_votacao_online", true),
            PermitirVotacaoPresencial = GetBoolValue(configuracoes, "eleicao.permitir_votacao_presencial", true),
            ExigirQuorum = GetBoolValue(configuracoes, "eleicao.exigir_quorum", true),
            PercentualQuorumMinimo = GetDecimalValue(configuracoes, "eleicao.percentual_quorum_minimo", 50),
            PermitirVotoBranco = GetBoolValue(configuracoes, "eleicao.permitir_voto_branco", true),
            PermitirVotoNulo = GetBoolValue(configuracoes, "eleicao.permitir_voto_nulo", true),
            NotificarInscricaoChapa = GetBoolValue(configuracoes, "eleicao.notificar_inscricao_chapa", true),
            NotificarAprovacaoChapa = GetBoolValue(configuracoes, "eleicao.notificar_aprovacao_chapa", true),
            NotificarInicioVotacao = GetBoolValue(configuracoes, "eleicao.notificar_inicio_votacao", true),
            NotificarResultado = GetBoolValue(configuracoes, "eleicao.notificar_resultado", true),
            TemplateEmailInscricao = GetValue(configuracoes, "eleicao.template_email_inscricao", null),
            TemplateEmailAprovacao = GetValue(configuracoes, "eleicao.template_email_aprovacao", null),
            TemplateEmailVotacao = GetValue(configuracoes, "eleicao.template_email_votacao", null),
            TemplateEmailResultado = GetValue(configuracoes, "eleicao.template_email_resultado", null)
        };
    }

    public async Task<ConfiguracaoEleicaoDefaultDto> UpdateEleicaoConfigAsync(ConfiguracaoEleicaoDefaultDto dto, Guid userId, CancellationToken cancellationToken = default)
    {
        var updates = new Dictionary<string, string>
        {
            { "eleicao.duracao_mandato_anos", dto.DuracaoMandatoAnos.ToString() },
            { "eleicao.quantidade_vagas_padrao", dto.QuantidadeVagasPadrao.ToString() },
            { "eleicao.quantidade_suplentes_padrao", dto.QuantidadeSuplementesPadrao.ToString() },
            { "eleicao.dias_inscricao_chapa", dto.DiasInscricaoChapa.ToString() },
            { "eleicao.dias_impugnacao", dto.DiasImpugnacao.ToString() },
            { "eleicao.dias_recurso", dto.DiasRecurso.ToString() },
            { "eleicao.dias_defesa", dto.DiasDefesa.ToString() },
            { "eleicao.dias_votacao", dto.DiasVotacao.ToString() },
            { "eleicao.dias_apuracao", dto.DiasApuracao.ToString() },
            { "eleicao.permitir_votacao_online", dto.PermitirVotacaoOnline.ToString().ToLower() },
            { "eleicao.permitir_votacao_presencial", dto.PermitirVotacaoPresencial.ToString().ToLower() },
            { "eleicao.exigir_quorum", dto.ExigirQuorum.ToString().ToLower() },
            { "eleicao.percentual_quorum_minimo", dto.PercentualQuorumMinimo.ToString() },
            { "eleicao.permitir_voto_branco", dto.PermitirVotoBranco.ToString().ToLower() },
            { "eleicao.permitir_voto_nulo", dto.PermitirVotoNulo.ToString().ToLower() },
            { "eleicao.notificar_inscricao_chapa", dto.NotificarInscricaoChapa.ToString().ToLower() },
            { "eleicao.notificar_aprovacao_chapa", dto.NotificarAprovacaoChapa.ToString().ToLower() },
            { "eleicao.notificar_inicio_votacao", dto.NotificarInicioVotacao.ToString().ToLower() },
            { "eleicao.notificar_resultado", dto.NotificarResultado.ToString().ToLower() }
        };

        if (dto.TemplateEmailInscricao != null)
            updates.Add("eleicao.template_email_inscricao", dto.TemplateEmailInscricao);
        if (dto.TemplateEmailAprovacao != null)
            updates.Add("eleicao.template_email_aprovacao", dto.TemplateEmailAprovacao);
        if (dto.TemplateEmailVotacao != null)
            updates.Add("eleicao.template_email_votacao", dto.TemplateEmailVotacao);
        if (dto.TemplateEmailResultado != null)
            updates.Add("eleicao.template_email_resultado", dto.TemplateEmailResultado);

        // Criar configuracoes de eleicao se nao existirem
        foreach (var (chave, valor) in updates)
        {
            var config = await _configuracaoRepository.Query()
                .FirstOrDefaultAsync(c => c.Chave == chave, cancellationToken);

            if (config == null)
            {
                await _configuracaoRepository.AddAsync(new Configuracao
                {
                    Chave = chave,
                    Valor = valor,
                    ValorPadrao = valor,
                    Categoria = "Eleicao",
                    Tipo = chave.Contains("permitir") || chave.Contains("exigir") || chave.Contains("notificar") ? "bool" : "string",
                    Editavel = true,
                    Ativo = true,
                    UltimoUsuarioAtualizacaoId = userId
                }, cancellationToken);
            }
            else
            {
                config.Valor = valor;
                config.UltimoUsuarioAtualizacaoId = userId;
                await _configuracaoRepository.UpdateAsync(config, cancellationToken);
            }
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        return await GetEleicaoConfigAsync(cancellationToken);
    }

    public async Task<IEnumerable<ConfiguracaoItemDto>> GetConfiguracoesEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var configuracoes = await _configuracaoEleicaoRepository.Query()
            .Where(c => c.EleicaoId == eleicaoId && c.Ativo)
            .ToListAsync(cancellationToken);

        return configuracoes.Select(c => new ConfiguracaoItemDto
        {
            Chave = c.Chave,
            Valor = c.Valor,
            Descricao = c.Descricao,
            Tipo = c.Tipo ?? "string",
            Categoria = "Eleicao",
            Editavel = true,
            UltimaAtualizacao = c.UpdatedAt
        });
    }

    public async Task<IEnumerable<ConfiguracaoItemDto>> UpdateConfiguracoesEleicaoAsync(
        Guid eleicaoId,
        Dictionary<string, string> configuracoes,
        Guid userId,
        CancellationToken cancellationToken = default)
    {
        foreach (var (chave, valor) in configuracoes)
        {
            var config = await _configuracaoEleicaoRepository.Query()
                .FirstOrDefaultAsync(c => c.EleicaoId == eleicaoId && c.Chave == chave, cancellationToken);

            if (config != null)
            {
                config.Valor = valor;
                config.UpdatedBy = userId.ToString();
                await _configuracaoEleicaoRepository.UpdateAsync(config, cancellationToken);
            }
            else
            {
                await _configuracaoEleicaoRepository.AddAsync(new ConfiguracaoEleicao
                {
                    EleicaoId = eleicaoId,
                    Chave = chave,
                    Valor = valor,
                    Ativo = true,
                    CreatedBy = userId.ToString()
                }, cancellationToken);
            }
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);

        return await GetConfiguracoesEleicaoAsync(eleicaoId, cancellationToken);
    }

    #region Private Methods

    private async Task EnsureDefaultConfiguracoesAsync(CancellationToken cancellationToken)
    {
        var existingCount = await _configuracaoRepository.CountAsync(cancellationToken: cancellationToken);
        if (existingCount > 0)
            return;

        var defaultConfigs = GetDefaultConfiguracoes();

        foreach (var config in defaultConfigs)
        {
            await _configuracaoRepository.AddAsync(config, cancellationToken);
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);
        _logger.LogInformation("Configuracoes padrao criadas: {Count} itens", defaultConfigs.Count);
    }

    private static List<Configuracao> GetDefaultConfiguracoes()
    {
        return new List<Configuracao>
        {
            // Geral
            new() { Chave = ChavesConfiguracao.NomeSistema, Valor = "CAU Sistema Eleitoral", ValorPadrao = "CAU Sistema Eleitoral", Categoria = CategoriaConfiguracao.Geral, Tipo = "string", Descricao = "Nome do sistema", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.Versao, Valor = "1.0.0", ValorPadrao = "1.0.0", Categoria = CategoriaConfiguracao.Geral, Tipo = "string", Descricao = "Versao do sistema", Editavel = false, Ativo = true },
            new() { Chave = ChavesConfiguracao.LogoUrl, Valor = "", ValorPadrao = "", Categoria = CategoriaConfiguracao.Geral, Tipo = "string", Descricao = "URL do logo", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.FaviconUrl, Valor = "", ValorPadrao = "", Categoria = CategoriaConfiguracao.Geral, Tipo = "string", Descricao = "URL do favicon", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.CorPrimaria, Valor = "#1E40AF", ValorPadrao = "#1E40AF", Categoria = CategoriaConfiguracao.Geral, Tipo = "string", Descricao = "Cor primaria do sistema", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.CorSecundaria, Valor = "#3B82F6", ValorPadrao = "#3B82F6", Categoria = CategoriaConfiguracao.Geral, Tipo = "string", Descricao = "Cor secundaria do sistema", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.ModoManutencao, Valor = "false", ValorPadrao = "false", Categoria = CategoriaConfiguracao.Geral, Tipo = "bool", Descricao = "Sistema em manutencao", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.MensagemManutencao, Valor = "", ValorPadrao = "", Categoria = CategoriaConfiguracao.Geral, Tipo = "string", Descricao = "Mensagem de manutencao", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.TimeZone, Valor = "America/Sao_Paulo", ValorPadrao = "America/Sao_Paulo", Categoria = CategoriaConfiguracao.Geral, Tipo = "string", Descricao = "Fuso horario", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.Locale, Valor = "pt-BR", ValorPadrao = "pt-BR", Categoria = CategoriaConfiguracao.Geral, Tipo = "string", Descricao = "Idioma/locale", Editavel = true, Ativo = true },

            // Email
            new() { Chave = ChavesConfiguracao.SmtpHost, Valor = "", ValorPadrao = "", Categoria = CategoriaConfiguracao.Email, Tipo = "string", Descricao = "Host SMTP", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.SmtpPort, Valor = "587", ValorPadrao = "587", Categoria = CategoriaConfiguracao.Email, Tipo = "int", Descricao = "Porta SMTP", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.SmtpUseSsl, Valor = "true", ValorPadrao = "true", Categoria = CategoriaConfiguracao.Email, Tipo = "bool", Descricao = "Usar SSL", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.SmtpUsername, Valor = "", ValorPadrao = "", Categoria = CategoriaConfiguracao.Email, Tipo = "string", Descricao = "Usuario SMTP", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.SmtpPassword, Valor = "", ValorPadrao = "", Categoria = CategoriaConfiguracao.Email, Tipo = "string", Descricao = "Senha SMTP", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.EmailRemetente, Valor = "noreply@cau.org.br", ValorPadrao = "noreply@cau.org.br", Categoria = CategoriaConfiguracao.Email, Tipo = "string", Descricao = "Email do remetente", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.NomeRemetente, Valor = "CAU Sistema Eleitoral", ValorPadrao = "CAU Sistema Eleitoral", Categoria = CategoriaConfiguracao.Email, Tipo = "string", Descricao = "Nome do remetente", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.EmailHabilitado, Valor = "false", ValorPadrao = "false", Categoria = CategoriaConfiguracao.Email, Tipo = "bool", Descricao = "Email habilitado", Editavel = true, Ativo = true },

            // Seguranca
            new() { Chave = ChavesConfiguracao.TentativasLoginMax, Valor = "5", ValorPadrao = "5", Categoria = CategoriaConfiguracao.Seguranca, Tipo = "int", Descricao = "Tentativas maximas de login", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.TempoBloqueioConta, Valor = "30", ValorPadrao = "30", Categoria = CategoriaConfiguracao.Seguranca, Tipo = "int", Descricao = "Tempo de bloqueio em minutos", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.ExpiracaoSenhaEmDias, Valor = "90", ValorPadrao = "90", Categoria = CategoriaConfiguracao.Seguranca, Tipo = "int", Descricao = "Dias para expiracao de senha", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.TamanhoMinimoSenha, Valor = "8", ValorPadrao = "8", Categoria = CategoriaConfiguracao.Seguranca, Tipo = "int", Descricao = "Tamanho minimo de senha", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.RequerLetraMaiuscula, Valor = "true", ValorPadrao = "true", Categoria = CategoriaConfiguracao.Seguranca, Tipo = "bool", Descricao = "Requer letra maiuscula", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.RequerNumero, Valor = "true", ValorPadrao = "true", Categoria = CategoriaConfiguracao.Seguranca, Tipo = "bool", Descricao = "Requer numero", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.RequerCaractereEspecial, Valor = "true", ValorPadrao = "true", Categoria = CategoriaConfiguracao.Seguranca, Tipo = "bool", Descricao = "Requer caractere especial", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.ExpiracaoTokenEmMinutos, Valor = "60", ValorPadrao = "60", Categoria = CategoriaConfiguracao.Seguranca, Tipo = "int", Descricao = "Expiracao do token em minutos", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.ExpiracaoRefreshTokenEmDias, Valor = "7", ValorPadrao = "7", Categoria = CategoriaConfiguracao.Seguranca, Tipo = "int", Descricao = "Expiracao do refresh token em dias", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.DoisFatoresObrigatorio, Valor = "false", ValorPadrao = "false", Categoria = CategoriaConfiguracao.Seguranca, Tipo = "bool", Descricao = "2FA obrigatorio", Editavel = true, Ativo = true },

            // Votacao
            new() { Chave = ChavesConfiguracao.PermitirVotoBranco, Valor = "true", ValorPadrao = "true", Categoria = CategoriaConfiguracao.Votacao, Tipo = "bool", Descricao = "Permitir voto em branco", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.PermitirVotoNulo, Valor = "true", ValorPadrao = "true", Categoria = CategoriaConfiguracao.Votacao, Tipo = "bool", Descricao = "Permitir voto nulo", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.MostrarResultadoParcial, Valor = "false", ValorPadrao = "false", Categoria = CategoriaConfiguracao.Votacao, Tipo = "bool", Descricao = "Mostrar resultado parcial", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.NotificarVotoRegistrado, Valor = "true", ValorPadrao = "true", Categoria = CategoriaConfiguracao.Votacao, Tipo = "bool", Descricao = "Notificar voto registrado", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.TempoSessaoVotacaoEmMinutos, Valor = "30", ValorPadrao = "30", Categoria = CategoriaConfiguracao.Votacao, Tipo = "int", Descricao = "Tempo de sessao em minutos", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.ConfirmacaoVotoObrigatoria, Valor = "true", ValorPadrao = "true", Categoria = CategoriaConfiguracao.Votacao, Tipo = "bool", Descricao = "Confirmacao de voto obrigatoria", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.MensagemVotacao, Valor = "", ValorPadrao = "", Categoria = CategoriaConfiguracao.Votacao, Tipo = "string", Descricao = "Mensagem na tela de votacao", Editavel = true, Ativo = true },
            new() { Chave = ChavesConfiguracao.MensagemConfirmacao, Valor = "", ValorPadrao = "", Categoria = CategoriaConfiguracao.Votacao, Tipo = "string", Descricao = "Mensagem de confirmacao", Editavel = true, Ativo = true }
        };
    }

    private static ConfiguracaoGeralDto MapToGeralDto(List<Configuracao> configuracoes)
    {
        return new ConfiguracaoGeralDto
        {
            NomeSistema = GetValue(configuracoes, ChavesConfiguracao.NomeSistema, "CAU Sistema Eleitoral"),
            Versao = GetValue(configuracoes, ChavesConfiguracao.Versao, "1.0.0"),
            LogoUrl = GetValue(configuracoes, ChavesConfiguracao.LogoUrl, null),
            FaviconUrl = GetValue(configuracoes, ChavesConfiguracao.FaviconUrl, null),
            CorPrimaria = GetValue(configuracoes, ChavesConfiguracao.CorPrimaria, "#1E40AF"),
            CorSecundaria = GetValue(configuracoes, ChavesConfiguracao.CorSecundaria, "#3B82F6"),
            ModoManutencao = GetBoolValue(configuracoes, ChavesConfiguracao.ModoManutencao, false),
            MensagemManutencao = GetValue(configuracoes, ChavesConfiguracao.MensagemManutencao, null),
            TimeZone = GetValue(configuracoes, ChavesConfiguracao.TimeZone, "America/Sao_Paulo"),
            Locale = GetValue(configuracoes, ChavesConfiguracao.Locale, "pt-BR")
        };
    }

    private static ConfiguracaoEmailDto MapToEmailDto(List<Configuracao> configuracoes)
    {
        return new ConfiguracaoEmailDto
        {
            SmtpHost = GetValue(configuracoes, ChavesConfiguracao.SmtpHost, ""),
            SmtpPort = GetIntValue(configuracoes, ChavesConfiguracao.SmtpPort, 587),
            SmtpUseSsl = GetBoolValue(configuracoes, ChavesConfiguracao.SmtpUseSsl, true),
            SmtpUsername = GetValue(configuracoes, ChavesConfiguracao.SmtpUsername, null),
            SmtpPassword = GetValue(configuracoes, ChavesConfiguracao.SmtpPassword, null),
            EmailRemetente = GetValue(configuracoes, ChavesConfiguracao.EmailRemetente, "noreply@cau.org.br"),
            NomeRemetente = GetValue(configuracoes, ChavesConfiguracao.NomeRemetente, "CAU Sistema Eleitoral"),
            EmailHabilitado = GetBoolValue(configuracoes, ChavesConfiguracao.EmailHabilitado, false)
        };
    }

    private static ConfiguracaoSegurancaDto MapToSegurancaDto(List<Configuracao> configuracoes)
    {
        return new ConfiguracaoSegurancaDto
        {
            TentativasLoginMax = GetIntValue(configuracoes, ChavesConfiguracao.TentativasLoginMax, 5),
            TempoBloqueioConta = GetIntValue(configuracoes, ChavesConfiguracao.TempoBloqueioConta, 30),
            ExpiracaoSenhaEmDias = GetIntValue(configuracoes, ChavesConfiguracao.ExpiracaoSenhaEmDias, 90),
            TamanhoMinimoSenha = GetIntValue(configuracoes, ChavesConfiguracao.TamanhoMinimoSenha, 8),
            RequerLetraMaiuscula = GetBoolValue(configuracoes, ChavesConfiguracao.RequerLetraMaiuscula, true),
            RequerNumero = GetBoolValue(configuracoes, ChavesConfiguracao.RequerNumero, true),
            RequerCaractereEspecial = GetBoolValue(configuracoes, ChavesConfiguracao.RequerCaractereEspecial, true),
            ExpiracaoTokenEmMinutos = GetIntValue(configuracoes, ChavesConfiguracao.ExpiracaoTokenEmMinutos, 60),
            ExpiracaoRefreshTokenEmDias = GetIntValue(configuracoes, ChavesConfiguracao.ExpiracaoRefreshTokenEmDias, 7),
            DoisFatoresObrigatorio = GetBoolValue(configuracoes, ChavesConfiguracao.DoisFatoresObrigatorio, false)
        };
    }

    private static ConfiguracaoVotacaoDto MapToVotacaoDto(List<Configuracao> configuracoes)
    {
        return new ConfiguracaoVotacaoDto
        {
            PermitirVotoBranco = GetBoolValue(configuracoes, ChavesConfiguracao.PermitirVotoBranco, true),
            PermitirVotoNulo = GetBoolValue(configuracoes, ChavesConfiguracao.PermitirVotoNulo, true),
            MostrarResultadoParcial = GetBoolValue(configuracoes, ChavesConfiguracao.MostrarResultadoParcial, false),
            NotificarVotoRegistrado = GetBoolValue(configuracoes, ChavesConfiguracao.NotificarVotoRegistrado, true),
            TempoSessaoVotacaoEmMinutos = GetIntValue(configuracoes, ChavesConfiguracao.TempoSessaoVotacaoEmMinutos, 30),
            ConfirmacaoVotoObrigatoria = GetBoolValue(configuracoes, ChavesConfiguracao.ConfirmacaoVotoObrigatoria, true),
            MensagemVotacao = GetValue(configuracoes, ChavesConfiguracao.MensagemVotacao, null),
            MensagemConfirmacao = GetValue(configuracoes, ChavesConfiguracao.MensagemConfirmacao, null)
        };
    }

    private static string? GetValue(List<Configuracao> configuracoes, string chave, string? defaultValue)
    {
        var config = configuracoes.FirstOrDefault(c => c.Chave == chave);
        return string.IsNullOrEmpty(config?.Valor) ? defaultValue : config.Valor;
    }

    private static bool GetBoolValue(List<Configuracao> configuracoes, string chave, bool defaultValue)
    {
        var value = GetValue(configuracoes, chave, defaultValue.ToString());
        return bool.TryParse(value, out var result) ? result : defaultValue;
    }

    private static int GetIntValue(List<Configuracao> configuracoes, string chave, int defaultValue)
    {
        var value = GetValue(configuracoes, chave, defaultValue.ToString());
        return int.TryParse(value, out var result) ? result : defaultValue;
    }

    private static decimal GetDecimalValue(List<Configuracao> configuracoes, string chave, decimal defaultValue)
    {
        var value = GetValue(configuracoes, chave, defaultValue.ToString());
        return decimal.TryParse(value, out var result) ? result : defaultValue;
    }

    #endregion
}

/// <summary>
/// DTO interno para importacao de configuracoes
/// </summary>
internal record ConfiguracaoImportDto
{
    public string Chave { get; init; } = string.Empty;
    public string Valor { get; init; } = string.Empty;
    public string? Categoria { get; init; }
    public string? Tipo { get; init; }
}
