using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Comissoes;

public class ComissaoEleitoral : BaseEntity
{
    public Guid CalendarioId { get; set; }
    public virtual Domain.Entities.Core.Calendario Calendario { get; set; } = null!;

    public Guid? FilialId { get; set; }
    public virtual Domain.Entities.Core.Filial? Filial { get; set; }

    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string? Sigla { get; set; }
    public string? Portaria { get; set; }
    public DateTime? DataPortaria { get; set; }
    public DateTime? DataInicio { get; set; }
    public DateTime? DataFim { get; set; }

    public bool Ativa { get; set; } = true;
    public string? MotivoEncerramento { get; set; }

    public virtual ICollection<MembroComissao> Membros { get; set; } = new List<MembroComissao>();
    public virtual ICollection<ComissaoDocumento> Documentos { get; set; } = new List<ComissaoDocumento>();
}

public class MembroComissao : BaseEntity
{
    public Guid ComissaoEleitoralId { get; set; }
    public virtual ComissaoEleitoral ComissaoEleitoral { get; set; } = null!;

    public Guid ProfissionalId { get; set; }
    public virtual Domain.Entities.Usuarios.Profissional Profissional { get; set; } = null!;

    public Guid? ConselheiroId { get; set; }
    public virtual Domain.Entities.Usuarios.Conselheiro? Conselheiro { get; set; }

    public TipoParticipacaoMembro TipoParticipacao { get; set; }
    public DateTime DataInicio { get; set; }
    public DateTime? DataFim { get; set; }
    public bool Ativo { get; set; } = true;

    public bool RespostaDeclaracao { get; set; }
    public DateTime? DataRespostaDeclaracao { get; set; }

    public Guid? MembroSubstitutoId { get; set; }
    public virtual MembroComissao? MembroSubstituto { get; set; }

    public virtual ICollection<MembroComissaoSituacao> HistoricoSituacoes { get; set; } = new List<MembroComissaoSituacao>();
    public virtual ICollection<MembroComissaoDocumento> Documentos { get; set; } = new List<MembroComissaoDocumento>();
}

public enum TipoParticipacaoMembro
{
    Titular = 1,
    Suplente = 2,
    Representante = 3,
    Observador = 4
}

public class MembroComissaoSituacao : BaseEntity
{
    public Guid MembroComissaoId { get; set; }
    public virtual MembroComissao MembroComissao { get; set; } = null!;

    public SituacaoMembroComissao Situacao { get; set; }
    public DateTime Data { get; set; }
    public string? Motivo { get; set; }
    public Guid? UsuarioId { get; set; }
}

public enum SituacaoMembroComissao
{
    Pendente = 1,
    Ativo = 2,
    Inativo = 3,
    Afastado = 4,
    Substituido = 5,
    Excluido = 6
}

public class ComissaoDocumento : BaseEntity
{
    public Guid ComissaoEleitoralId { get; set; }
    public virtual ComissaoEleitoral ComissaoEleitoral { get; set; } = null!;

    public string Tipo { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string NomeArquivo { get; set; } = string.Empty;
    public string CaminhoArquivo { get; set; } = string.Empty;
    public long TamanhoArquivo { get; set; }
    public string? ContentType { get; set; }
}

public class MembroComissaoDocumento : BaseEntity
{
    public Guid MembroComissaoId { get; set; }
    public virtual MembroComissao MembroComissao { get; set; } = null!;

    public string Tipo { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string NomeArquivo { get; set; } = string.Empty;
    public string CaminhoArquivo { get; set; } = string.Empty;
    public long TamanhoArquivo { get; set; }
    public string? ContentType { get; set; }
}