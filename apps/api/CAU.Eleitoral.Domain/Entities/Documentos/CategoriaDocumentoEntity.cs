using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for document categories classification
/// </summary>
public class CategoriaDocumentoEntity : BaseEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public Guid? CategoriaPaiId { get; set; }
    public virtual CategoriaDocumentoEntity? CategoriaPai { get; set; }

    public StatusCategoriaDocumento Status { get; set; }

    public int Ordem { get; set; }
    public bool Padrao { get; set; }

    public virtual ICollection<CategoriaDocumentoEntity> Subcategorias { get; set; } = new List<CategoriaDocumentoEntity>();
    public virtual ICollection<Documento> Documentos { get; set; } = new List<Documento>();
}
