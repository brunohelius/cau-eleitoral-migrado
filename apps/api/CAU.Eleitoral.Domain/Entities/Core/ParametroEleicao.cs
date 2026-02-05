using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class ParametroEleicao : BaseEntity
{
    public string Chave { get; set; } = string.Empty;
    public string Valor { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public string? Tipo { get; set; }
    public string? Grupo { get; set; }

    public bool Ativo { get; set; } = true;
    public bool Editavel { get; set; } = true;
}
