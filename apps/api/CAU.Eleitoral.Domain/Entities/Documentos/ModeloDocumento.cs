using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for document templates (models)
/// </summary>
public class ModeloDocumento : BaseEntity
{
    public string Codigo { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public TipoModeloDocumento Tipo { get; set; }
    public StatusModeloDocumento Status { get; set; }

    public string? Conteudo { get; set; }
    public string? ConteudoHtml { get; set; }
    public string? EstilosCSS { get; set; }

    public string? Cabecalho { get; set; }
    public string? Rodape { get; set; }

    public string? VariaveisDisponiveis { get; set; }
    public string? ExemploPreenchido { get; set; }

    public int? Versao { get; set; }

    public bool Padrao { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }

    public virtual ICollection<TemplateDocumento> Templates { get; set; } = new List<TemplateDocumento>();
}
