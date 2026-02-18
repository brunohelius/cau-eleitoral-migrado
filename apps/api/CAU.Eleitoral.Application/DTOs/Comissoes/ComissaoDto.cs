using CAU.Eleitoral.Domain.Entities.Comissoes;

namespace CAU.Eleitoral.Application.DTOs.Comissoes;

public class ComissaoEleitoralDto
{
    public Guid Id { get; set; }
    public Guid CalendarioId { get; set; }
    public string? CalendarioNome { get; set; }
    public Guid? FilialId { get; set; }
    public string? FilialNome { get; set; }
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string? Sigla { get; set; }
    public string? Portaria { get; set; }
    public DateTime? DataPortaria { get; set; }
    public DateTime? DataInicio { get; set; }
    public DateTime? DataFim { get; set; }
    public bool Ativa { get; set; }
    public string? MotivoEncerramento { get; set; }
    public DateTime CreatedAt { get; set; }
    public List<MembroComissaoDto> Membros { get; set; } = new();
    public int TotalMembros => Membros?.Count ?? 0;
    public int MembrosAtivos => Membros?.Count(m => m.Ativo) ?? 0;
}

public class MembroComissaoDto
{
    public Guid Id { get; set; }
    public Guid ComissaoEleitoralId { get; set; }
    public Guid ProfissionalId { get; set; }
    public string? ProfissionalNome { get; set; }
    public string? ProfissionalRegistroCAU { get; set; }
    public string? ProfissionalCPF { get; set; }
    public Guid? ConselheiroId { get; set; }
    public string? ConselheiroNome { get; set; }
    public TipoParticipacaoMembro TipoParticipacao { get; set; }
    public string TipoParticipacaoNome => TipoParticipacao.ToString();
    public DateTime DataInicio { get; set; }
    public DateTime? DataFim { get; set; }
    public bool Ativo { get; set; }
    public bool RespostaDeclaracao { get; set; }
    public DateTime? DataRespostaDeclaracao { get; set; }
    public Guid? MembroSubstitutoId { get; set; }
    public string? MembroSubstitutoNome { get; set; }
    public SituacaoMembroComissao? SituacaoAtual { get; set; }
    public string? SituacaoAtualNome => SituacaoAtual?.ToString();
    public DateTime CreatedAt { get; set; }
}

public class MembroComissaoSituacaoDto
{
    public Guid Id { get; set; }
    public Guid MembroComissaoId { get; set; }
    public SituacaoMembroComissao Situacao { get; set; }
    public string SituacaoNome => Situacao.ToString();
    public DateTime Data { get; set; }
    public string? Motivo { get; set; }
    public Guid? UsuarioId { get; set; }
    public string? UsuarioNome { get; set; }
}

public class ComissaoDocumentoDto
{
    public Guid Id { get; set; }
    public Guid ComissaoEleitoralId { get; set; }
    public string Tipo { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string NomeArquivo { get; set; } = string.Empty;
    public string CaminhoArquivo { get; set; } = string.Empty;
    public long TamanhoArquivo { get; set; }
    public string? ContentType { get; set; }
    public DateTime CreatedAt { get; set; }
}

public class MembroComissaoDocumentoDto
{
    public Guid Id { get; set; }
    public Guid MembroComissaoId { get; set; }
    public string Tipo { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string NomeArquivo { get; set; } = string.Empty;
    public string CaminhoArquivo { get; set; } = string.Empty;
    public long TamanhoArquivo { get; set; }
    public string? ContentType { get; set; }
    public DateTime CreatedAt { get; set; }
}

// DTOs para criação e atualização
public class CreateComissaoEleitoralDto
{
    public Guid CalendarioId { get; set; }
    public Guid? FilialId { get; set; }
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string? Sigla { get; set; }
    public string? Portaria { get; set; }
    public DateTime? DataPortaria { get; set; }
    public DateTime? DataInicio { get; set; }
    public DateTime? DataFim { get; set; }
}

public class UpdateComissaoEleitoralDto
{
    public string? Nome { get; set; }
    public string? Descricao { get; set; }
    public string? Sigla { get; set; }
    public string? Portaria { get; set; }
    public DateTime? DataPortaria { get; set; }
    public DateTime? DataInicio { get; set; }
    public DateTime? DataFim { get; set; }
    public bool? Ativa { get; set; }
    public string? MotivoEncerramento { get; set; }
}

public class CreateMembroComissaoDto
{
    public Guid ComissaoEleitoralId { get; set; }
    public Guid ProfissionalId { get; set; }
    public Guid? ConselheiroId { get; set; }
    public TipoParticipacaoMembro TipoParticipacao { get; set; }
    public DateTime DataInicio { get; set; }
    public DateTime? DataFim { get; set; }
    public Guid? MembroSubstitutoId { get; set; }
}

public class UpdateMembroComissaoDto
{
    public TipoParticipacaoMembro? TipoParticipacao { get; set; }
    public DateTime? DataInicio { get; set; }
    public DateTime? DataFim { get; set; }
    public bool? Ativo { get; set; }
    public Guid? MembroSubstitutoId { get; set; }
}

public class CreateMembroComissaoSituacaoDto
{
    public Guid MembroComissaoId { get; set; }
    public SituacaoMembroComissao Situacao { get; set; }
    public string? Motivo { get; set; }
}

public class CreateComissaoDocumentoDto
{
    public Guid ComissaoEleitoralId { get; set; }
    public string Tipo { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public IFormFile? Arquivo { get; set; }
}

public class CreateMembroComissaoDocumentoDto
{
    public Guid MembroComissaoId { get; set; }
    public string Tipo { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public IFormFile? Arquivo { get; set; }
}

// Filtros
public class ComissaoEleitoralFilter
{
    public Guid? CalendarioId { get; set; }
    public Guid? FilialId { get; set; }
    public bool? Ativa { get; set; }
    public string? Nome { get; set; }
    public int PageNumber { get; set; } = 1;
    public int PageSize { get; set; } = 20;
}

public class MembroComissaoFilter
{
    public Guid? ComissaoEleitoralId { get; set; }
    public Guid? ProfissionalId { get; set; }
    public bool? Ativo { get; set; }
    public TipoParticipacaoMembro? TipoParticipacao { get; set; }
    public int PageNumber { get; set; } = 1;
    public int PageSize { get; set; } = 20;
}