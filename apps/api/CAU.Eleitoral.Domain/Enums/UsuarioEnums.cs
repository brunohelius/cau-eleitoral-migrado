namespace CAU.Eleitoral.Domain.Enums;

public enum StatusUsuario
{
    Ativo = 0,
    Inativo = 1,
    Bloqueado = 2,
    PendenteCadastro = 3,
    PendenteConfirmacao = 4
}

public enum TipoUsuario
{
    Administrador = 0,
    ComissaoEleitoral = 1,
    Conselheiro = 2,
    Profissional = 3,
    Candidato = 4,
    Eleitor = 5
}

public enum StatusProfissional
{
    Ativo = 0,
    Inativo = 1,
    Suspenso = 2,
    Cancelado = 3,
    EmDebito = 4
}

public enum TipoProfissional
{
    Arquiteto = 0,
    ArquitetoUrbanista = 1
}
