using CAU.Eleitoral.Domain.Common;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Entities.Core;

namespace CAU.Eleitoral.Domain.Entities.Usuarios;

public class Profissional : BaseEntity
{
    public Guid? UsuarioId { get; set; }
    public virtual Usuario? Usuario { get; set; }

    public string RegistroCAU { get; set; } = string.Empty;
    public string Nome { get; set; } = string.Empty;
    public string? NomeCompleto { get; set; }
    public string Cpf { get; set; } = string.Empty;
    public string? Rg { get; set; }
    public string? Email { get; set; }
    public string? Telefone { get; set; }
    public string? Celular { get; set; }

    public DateTime? DataNascimento { get; set; }
    public string? Nacionalidade { get; set; }
    public string? Naturalidade { get; set; }

    public string? Endereco { get; set; }
    public string? Numero { get; set; }
    public string? Complemento { get; set; }
    public string? Bairro { get; set; }
    public string? Cidade { get; set; }
    public string? UF { get; set; }
    public string? Cep { get; set; }

    public TipoProfissional Tipo { get; set; }
    public StatusProfissional Status { get; set; }

    public DateTime? DataRegistro { get; set; }
    public DateTime? DataUltimaAtualizacao { get; set; }

    public Guid? RegionalId { get; set; }
    public virtual RegionalCAU? Regional { get; set; }

    public Guid? FilialId { get; set; }
    public virtual Filial? Filial { get; set; }

    public bool EleitorApto { get; set; }
    public string? MotivoInaptidao { get; set; }

    public virtual Conselheiro? Conselheiro { get; set; }
}
