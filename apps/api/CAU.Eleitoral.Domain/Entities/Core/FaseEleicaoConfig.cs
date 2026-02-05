using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Domain.Entities.Core;

/// <summary>
/// Lookup table para fases da eleição com configurações adicionais
/// </summary>
public class FaseEleicaoConfig : BaseEntity
{
    public FaseEleicao Fase { get; set; }
    public string Codigo { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Descricao { get; set; }

    public int Ordem { get; set; }
    public bool Obrigatoria { get; set; }
    public int? DuracaoMinimaDias { get; set; }
    public int? DuracaoMaximaDias { get; set; }

    public FaseEleicao? FaseAnterior { get; set; }
    public FaseEleicao? FasePosterior { get; set; }

    public bool PermiteRetrocesso { get; set; }
    public bool RequerAprovacao { get; set; }
    public bool NotificarInicio { get; set; }
    public bool NotificarFim { get; set; }

    public bool Ativo { get; set; } = true;
}
