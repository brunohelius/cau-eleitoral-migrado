namespace CAU.Eleitoral.Domain.Enums;

public enum TipoVoto
{
    Chapa = 0,
    Branco = 1,
    Nulo = 2,
    Anulado = 3
}

public enum StatusVoto
{
    Registrado = 0,
    Confirmado = 1,
    Anulado = 2
}

public enum ModoVotacao
{
    Presencial = 0,
    Online = 1,
    Hibrido = 2
}
