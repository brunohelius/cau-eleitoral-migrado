using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Core;

public class RegionalCAU : BaseEntity
{
    public string Sigla { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? Cnpj { get; set; }

    public string? Endereco { get; set; }
    public string? Cidade { get; set; }
    public string UF { get; set; } = string.Empty;
    public string? Cep { get; set; }

    public string? Telefone { get; set; }
    public string? Email { get; set; }
    public string? Site { get; set; }

    public bool Ativo { get; set; } = true;

    public virtual ICollection<Filial> Filiais { get; set; } = new List<Filial>();
    public virtual ICollection<Eleicao> Eleicoes { get; set; } = new List<Eleicao>();
}
