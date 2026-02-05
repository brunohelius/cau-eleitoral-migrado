namespace CAU.Eleitoral.Domain.Enums;

public enum TipoDenuncia
{
    PropagandaIrregular = 0,
    AbusoPoder = 1,
    CaptacaoIlicitaSufragio = 2,
    UsoIndevido = 3,
    Inelegibilidade = 4,
    FraudeDocumental = 5,
    Outros = 99
}

public enum StatusDenuncia
{
    Recebida = 0,
    EmAnalise = 1,
    AdmissibilidadeAceita = 2,
    AdmissibilidadeRejeitada = 3,
    AguardandoDefesa = 4,
    DefesaApresentada = 5,
    AguardandoJulgamento = 6,
    Julgada = 7,
    Procedente = 8,
    Improcedente = 9,
    ParcialmenteProcedente = 10,
    Arquivada = 11,
    AguardandoRecurso = 12,
    RecursoApresentado = 13,
    RecursoJulgado = 14
}

public enum StatusDefesa
{
    AguardandoDefesa = 0,
    Apresentada = 1,
    NaoApresentada = 2,
    Intempestiva = 3
}

public enum TipoProva
{
    Documento = 0,
    Foto = 1,
    Video = 2,
    Audio = 3,
    PrintScreen = 4,
    Testemunho = 5,
    Pericia = 6,
    Outros = 99
}

public enum TipoArquivoDenuncia
{
    Documento = 0,
    Imagem = 1,
    Video = 2,
    Audio = 3,
    Planilha = 4,
    Comprovante = 5,
    Outros = 99
}

public enum StatusAnaliseDenuncia
{
    EmAndamento = 0,
    Concluida = 1,
    Pendente = 2,
    Cancelada = 3
}

public enum TipoAlegacaoDenuncia
{
    PropagandaIrregular = 0,
    AbusoPoder = 1,
    CaptacaoIlicitaSufragio = 2,
    UsoIndevido = 3,
    Inelegibilidade = 4,
    FraudeDocumental = 5,
    ViolacaoNorma = 6,
    Outros = 99
}

public enum StatusEncaminhamento
{
    Enviado = 0,
    Recebido = 1,
    EmAnalise = 2,
    Concluido = 3,
    Devolvido = 4
}

public enum TipoEncaminhamento
{
    ComissaoEleitoral = 0,
    ConselhoEtica = 1,
    OuvidoriaGeral = 2,
    MinisterioPublico = 3,
    ComissaoJulgadora = 4,
    Outros = 99
}

public enum TipoParecerDenuncia
{
    Tecnico = 0,
    Juridico = 1,
    ComissaoEleitoral = 2,
    Relator = 3,
    Outros = 99
}

public enum StatusParecerDenuncia
{
    Elaboracao = 0,
    EmRevisao = 1,
    Aprovado = 2,
    Rejeitado = 3,
    Publicado = 4
}

public enum TipoDespachoDenuncia
{
    Ordinario = 0,
    Urgente = 1,
    Liminar = 2,
    Interlocutorio = 3,
    Saneador = 4
}

public enum StatusDespacho
{
    Rascunho = 0,
    Assinado = 1,
    Publicado = 2,
    Cumprido = 3,
    Cancelado = 4
}

public enum TipoVistaDenuncia
{
    ParteInteressada = 0,
    Denunciante = 1,
    Denunciado = 2,
    Advogado = 3,
    MinisterioPublico = 4
}

public enum StatusVista
{
    Solicitada = 0,
    Concedida = 1,
    EmAndamento = 2,
    Encerrada = 3,
    Negada = 4
}

public enum TipoAnexoDenuncia
{
    DocumentoComplementar = 0,
    ParecerTecnico = 1,
    ProvaAdicional = 2,
    DecisaoAnterior = 3,
    LegislacaoAplicavel = 4,
    Outros = 99
}
