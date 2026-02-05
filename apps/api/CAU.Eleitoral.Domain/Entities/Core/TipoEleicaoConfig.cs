using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Lookup table para tipos de eleição com configurações adicionais
/// </summary>
public class TipoEleicaoConfig : BaseEntity
{
    public TipoEleicao Tipo { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public bool PermiteVotoOnline { get; set; }
    public bool PermiteVotoPresencial { get; set; }
    public bool RequerAprovacao { get; set; }

    public int? DuracaoMinimaInscricaoDias { get; set; }
    public int? DuracaoMinimaVotacaoDias { get; set; }
    public int? PrazoMaximoApuracaoDias { get; set; }

    public bool Ativo { get; set; } = true;
    public int Ordem { get; set; }
}
