using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Application.DTOs.Denuncias;

namespace CAU.Eleitoral.Api.Controllers;

/// <summary>
/// Controller para envio publico de denuncias (sem autenticacao)
/// </summary>
[Route("api/public/denuncias")]
public class PublicDenunciaController : BaseController
{
    private readonly IDenunciaService _denunciaService;
    private readonly IEleicaoService _eleicaoService;
    private readonly ILogger<PublicDenunciaController> _logger;

    public PublicDenunciaController(
        IDenunciaService denunciaService,
        IEleicaoService eleicaoService,
        ILogger<PublicDenunciaController> logger)
    {
        _denunciaService = denunciaService;
        _eleicaoService = eleicaoService;
        _logger = logger;
    }

    /// <summary>
    /// Lista as eleicoes disponiveis para denuncia
    /// </summary>
    [HttpGet("eleicoes")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<EleicaoParaDenunciaDto>), StatusCodes.Status200OK)]
    public async Task<ActionResult<IEnumerable<EleicaoParaDenunciaDto>>> GetEleicoesParaDenuncia(CancellationToken cancellationToken)
    {
        try
        {
            var eleicoes = await _eleicaoService.GetAtivasAsync(cancellationToken);
            var result = eleicoes.Select(e => new EleicaoParaDenunciaDto
            {
                Id = e.Id,
                Nome = e.Nome,
                Ano = e.Ano,
                Regional = e.RegionalNome ?? "CAU/BR"
            });
            return Ok(result);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao listar eleicoes para denuncia");
            return InternalError("Erro ao listar eleicoes");
        }
    }

    /// <summary>
    /// Lista os tipos de denuncia disponiveis
    /// </summary>
    [HttpGet("tipos")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(IEnumerable<TipoDenunciaDto>), StatusCodes.Status200OK)]
    public ActionResult<IEnumerable<TipoDenunciaDto>> GetTiposDenuncia()
    {
        var tipos = Enum.GetValues<TipoDenuncia>()
            .Select(t => new TipoDenunciaDto
            {
                Valor = (int)t,
                Nome = GetTipoDenunciaLabel(t),
                Descricao = GetTipoDenunciaDescricao(t)
            })
            .ToList();

        return Ok(tipos);
    }

    /// <summary>
    /// Envia uma denuncia publica (anonima ou identificada)
    /// </summary>
    [HttpPost]
    [AllowAnonymous]
    [ProducesResponseType(typeof(DenunciaPublicaResultDto), StatusCodes.Status201Created)]
    [ProducesResponseType(StatusCodes.Status400BadRequest)]
    public async Task<ActionResult<DenunciaPublicaResultDto>> Create(
        [FromBody] CreateDenunciaPublicaDto dto,
        CancellationToken cancellationToken)
    {
        try
        {
            // Validacoes basicas
            if (dto.EleicaoId == Guid.Empty)
                return BadRequest(new { message = "Eleicao e obrigatoria" });

            if (string.IsNullOrWhiteSpace(dto.Descricao))
                return BadRequest(new { message = "Descricao e obrigatoria" });

            if (dto.Descricao.Length < 50)
                return BadRequest(new { message = "Descricao deve ter pelo menos 50 caracteres" });

            if (dto.Descricao.Length > 5000)
                return BadRequest(new { message = "Descricao deve ter no maximo 5000 caracteres" });

            // Validacao de captcha simples (soma)
            if (!ValidateCaptcha(dto.CaptchaValue, dto.CaptchaExpected))
                return BadRequest(new { message = "Verificacao de seguranca invalida. Por favor, tente novamente." });

            // Validar eleicao existe e esta ativa
            var eleicao = await _eleicaoService.GetByIdAsync(dto.EleicaoId, cancellationToken);
            if (eleicao == null)
                return BadRequest(new { message = "Eleicao nao encontrada" });

            // Criar denuncia via servico
            var createDto = new CreateDenunciaDto
            {
                EleicaoId = dto.EleicaoId,
                Tipo = dto.Tipo,
                Descricao = dto.Descricao,
                Fundamentacao = dto.Fundamentacao
            };

            // Para denuncias anonimas, nao passa userId
            var denuncia = await _denunciaService.CreateAsync(createDto, Guid.Empty, cancellationToken);

            // Retornar resultado com protocolo
            return CreatedAtAction(
                nameof(ConsultarProtocolo),
                new { protocolo = denuncia.Protocolo },
                new DenunciaPublicaResultDto
                {
                    Protocolo = denuncia.Protocolo,
                    DataEnvio = denuncia.CreatedAt,
                    Mensagem = "Denuncia registrada com sucesso. Guarde o numero do protocolo para acompanhamento."
                });
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(new { message = ex.Message });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao criar denuncia publica");
            return InternalError("Erro ao registrar denuncia. Por favor, tente novamente.");
        }
    }

    /// <summary>
    /// Consulta o status de uma denuncia pelo protocolo
    /// </summary>
    [HttpGet("protocolo/{protocolo}")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(ConsultaProtocoloDto), StatusCodes.Status200OK)]
    [ProducesResponseType(StatusCodes.Status404NotFound)]
    public async Task<ActionResult<ConsultaProtocoloDto>> ConsultarProtocolo(
        string protocolo,
        CancellationToken cancellationToken)
    {
        try
        {
            var denuncia = await _denunciaService.GetByProtocoloAsync(protocolo, cancellationToken);
            if (denuncia == null)
                return NotFound(new { message = "Protocolo nao encontrado" });

            return Ok(new ConsultaProtocoloDto
            {
                Protocolo = denuncia.Protocolo,
                Status = denuncia.StatusNome,
                DataEnvio = denuncia.CreatedAt,
                UltimaAtualizacao = denuncia.DataRecebimento ?? denuncia.CreatedAt
            });
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Erro ao consultar protocolo {Protocolo}", protocolo);
            return InternalError("Erro ao consultar protocolo");
        }
    }

    /// <summary>
    /// Gera dados para captcha
    /// </summary>
    [HttpGet("captcha")]
    [AllowAnonymous]
    [ProducesResponseType(typeof(CaptchaDto), StatusCodes.Status200OK)]
    public ActionResult<CaptchaDto> GetCaptcha()
    {
        var random = new Random();
        var num1 = random.Next(1, 20);
        var num2 = random.Next(1, 20);
        var resultado = num1 + num2;

        // Codifica o resultado esperado (simples, para demo)
        // Em producao, usar algo mais seguro como JWT ou hash com salt
        var encodedResult = Convert.ToBase64String(
            System.Text.Encoding.UTF8.GetBytes($"captcha_{resultado}_{DateTime.UtcNow.Ticks}")
        );

        return Ok(new CaptchaDto
        {
            Pergunta = $"Quanto e {num1} + {num2}?",
            Token = encodedResult,
            ExpectedValue = resultado // Em prod, isso seria removido
        });
    }

    private bool ValidateCaptcha(int userValue, int expectedValue)
    {
        // Validacao simples - em producao, validar o token
        return userValue == expectedValue;
    }

    private string GetTipoDenunciaLabel(TipoDenuncia tipo) => tipo switch
    {
        TipoDenuncia.PropagandaIrregular => "Propaganda Irregular",
        TipoDenuncia.AbusoPoder => "Abuso de Poder",
        TipoDenuncia.CaptacaoIlicitaSufragio => "Captacao Ilicita de Sufragio",
        TipoDenuncia.UsoIndevido => "Uso Indevido de Recursos",
        TipoDenuncia.Inelegibilidade => "Inelegibilidade",
        TipoDenuncia.FraudeDocumental => "Fraude Documental",
        TipoDenuncia.Outros => "Outros",
        _ => tipo.ToString()
    };

    private string GetTipoDenunciaDescricao(TipoDenuncia tipo) => tipo switch
    {
        TipoDenuncia.PropagandaIrregular => "Propaganda eleitoral fora das normas estabelecidas pelo CAU",
        TipoDenuncia.AbusoPoder => "Uso indevido de cargo ou funcao para influenciar a eleicao",
        TipoDenuncia.CaptacaoIlicitaSufragio => "Oferta de vantagens para obter votos",
        TipoDenuncia.UsoIndevido => "Utilizacao irregular de recursos publicos ou privados",
        TipoDenuncia.Inelegibilidade => "Candidato que nao atende aos requisitos legais",
        TipoDenuncia.FraudeDocumental => "Falsificacao ou adulteracao de documentos",
        TipoDenuncia.Outros => "Outras irregularidades nao listadas acima",
        _ => ""
    };
}

// DTOs especificos para denuncias publicas
public record EleicaoParaDenunciaDto
{
    public Guid Id { get; init; }
    public string Nome { get; init; } = string.Empty;
    public int Ano { get; init; }
    public string Regional { get; init; } = string.Empty;
}

public record TipoDenunciaDto
{
    public int Valor { get; init; }
    public string Nome { get; init; } = string.Empty;
    public string Descricao { get; init; } = string.Empty;
}

public record CreateDenunciaPublicaDto
{
    public Guid EleicaoId { get; init; }
    public TipoDenuncia Tipo { get; init; }
    public string Descricao { get; init; } = string.Empty;
    public string? Fundamentacao { get; init; }
    public int CaptchaValue { get; init; }
    public int CaptchaExpected { get; init; }
    public List<ArquivoAnexoDto>? Arquivos { get; init; }
}

public record ArquivoAnexoDto
{
    public string Nome { get; init; } = string.Empty;
    public string Tipo { get; init; } = string.Empty;
    public string Base64Content { get; init; } = string.Empty;
}

public record DenunciaPublicaResultDto
{
    public string Protocolo { get; init; } = string.Empty;
    public DateTime DataEnvio { get; init; }
    public string Mensagem { get; init; } = string.Empty;
}

public record ConsultaProtocoloDto
{
    public string Protocolo { get; init; } = string.Empty;
    public string Status { get; init; } = string.Empty;
    public DateTime DataEnvio { get; init; }
    public DateTime UltimaAtualizacao { get; init; }
}

public record CaptchaDto
{
    public string Pergunta { get; init; } = string.Empty;
    public string Token { get; init; } = string.Empty;
    public int ExpectedValue { get; init; }
}
