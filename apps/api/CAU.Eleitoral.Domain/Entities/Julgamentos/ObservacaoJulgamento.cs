using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Julgamentos;

public class ObservacaoJulgamento : BaseEntity
{
    public Guid JulgamentoId { get; set; }
    public string TipoJulgamento { get; set; } = string.Empty;

    public int Ordem { get; set; }
    public DateTime DataObservacao { get; set; }

    public string? Autor { get; set; }
    public string? CargoAutor { get; set; }

    public Guid? MembroComissaoId { get; set; }
    public virtual MembroComissaoJulgadora? MembroComissao { get; set; }

    public string? Titulo { get; set; }
    public string Conteudo { get; set; } = string.Empty;
    public string? Categoria { get; set; }

    public bool Interna { get; set; }
    public bool Confidencial { get; set; }

    public Guid? ObservacaoRelacionadaId { get; set; }
    public virtual ObservacaoJulgamento? ObservacaoRelacionada { get; set; }

    public string? ArquivoAnexoUrl { get; set; }

    public virtual ICollection<ObservacaoJulgamento> Respostas { get; set; } = new List<ObservacaoJulgamento>();
}
