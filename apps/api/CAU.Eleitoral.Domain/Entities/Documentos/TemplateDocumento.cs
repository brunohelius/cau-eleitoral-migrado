using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Documentos;

/// <summary>
/// Entity for document templates (specific instances)
/// </summary>
public class TemplateDocumento : BaseEntity
{
    public Guid? ModeloId { get; set; }
    public virtual ModeloDocumento? Modelo { get; set; }

    public Guid? EleicaoId { get; set; }
    public virtual Eleicao? Eleicao { get; set; }

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

    public string? Variaveis { get; set; }

    public int? Versao { get; set; }
    public bool Personalizado { get; set; }

    public string? ArquivoUrl { get; set; }
    public string? ArquivoNome { get; set; }

    public string? Observacoes { get; set; }
}
