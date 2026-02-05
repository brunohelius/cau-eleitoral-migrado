namespace CAU.Eleitoral.Domain.Enums;

public enum TipoJulgamento
{
    Denuncia = 0,
    Impugnacao = 1,
    RecursoDenuncia = 2,
    RecursoImpugnacao = 3,
    RecursoSegundaInstancia = 4,
    JulgamentoFinal = 5
}

public enum StatusJulgamento
{
    Agendado = 0,
    EmAndamento = 1,
    Interrompido = 2,
    Suspenso = 3,
    Concluido = 4,
    Cancelado = 5
}

public enum TipoVotoJulgamento
{
    Procedente = 0,
    Improcedente = 1,
    ParcialmenteProcedente = 2,
    Abstencao = 3,
    Impedido = 4
}

public enum TipoDecisao
{
    Unanime = 0,
    Maioria = 1,
    VotoDesempate = 2
}

public enum TipoSessao
{
    Ordinaria = 0,
    Extraordinaria = 1,
    Especial = 2
}

public enum StatusSessao
{
    Agendada = 0,
    EmAndamento = 1,
    Encerrada = 2,
    Cancelada = 3,
    Adiada = 4
}

public enum TipoMembroComissao
{
    Presidente = 0,
    Relator = 1,
    Revisor = 2,
    Vogal = 3,
    Suplente = 4
}

public enum StatusRecurso
{
    Protocolado = 0,
    EmAnalise = 1,
    Admitido = 2,
    Inadmitido = 3,
    Provido = 4,
    DesprovimidoParcialmente = 5,
    Desprovido = 6,
    NaoConhecido = 7,
    Arquivado = 8
}

public enum TipoRecurso
{
    RecursoOrdinario = 0,
    RecursoExtraordinario = 1,
    EmbargoDeclaracao = 2,
    AgravoPetitorio = 3,
    RecursoRevisao = 4
}

public enum StatusSubstituicao
{
    Pendente = 0,
    Aprovada = 1,
    Rejeitada = 2,
    Cancelada = 3
}

public enum TipoSubstituicao
{
    CorrecaoErroMaterial = 0,
    ComplementacaoFundamentacao = 1,
    RetificacaoDispositivo = 2,
    Esclarecimento = 3
}

public enum TipoAlegacaoJulgamento
{
    MeritoFato = 0,
    MeritoDireito = 1,
    Preliminar = 2,
    Prejudicial = 3,
    Pedido = 4
}

public enum TipoProvaJulgamento
{
    Documental = 0,
    Testemunhal = 1,
    Pericial = 2,
    Audiovisual = 3,
    Digital = 4,
    Indiciaria = 5
}

public enum StatusProvaJulgamento
{
    Apresentada = 0,
    EmAnalise = 1,
    Admitida = 2,
    Inadmitida = 3,
    Impugnada = 4
}

public enum TipoArquivoJulgamento
{
    Peticao = 0,
    Documento = 1,
    Prova = 2,
    Decisao = 3,
    Acordao = 4,
    Certidao = 5,
    Ata = 6,
    Parecer = 7,
    Relatorio = 8,
    Voto = 9,
    Notificacao = 10,
    Intimacao = 11,
    Outros = 12
}

public enum TipoNotificacaoJulgamento
{
    Citacao = 0,
    Intimacao = 1,
    Notificacao = 2,
    Comunicacao = 3,
    Aviso = 4,
    Convocacao = 5
}

public enum StatusNotificacaoJulgamento
{
    Pendente = 0,
    Enviada = 1,
    Recebida = 2,
    NaoRecebida = 3,
    Devolvida = 4,
    Cancelada = 5
}

public enum FormaNotificacao
{
    Postal = 0,
    Email = 1,
    Sistema = 2,
    Edital = 3,
    Pessoal = 4
}

public enum TipoCertidao
{
    CertidaoInteiroTeor = 0,
    CertidaoTransitoJulgado = 1,
    CertidaoObjeto = 2,
    CertidaoNarrativa = 3,
    CertidaoNegativa = 4,
    CertidaoPositiva = 5
}

public enum TipoRelatorioJulgamento
{
    RelatorioVoto = 0,
    RelatorioInstrucao = 1,
    RelatorioConsolidado = 2,
    RelatorioParcial = 3,
    RelatorioFinal = 4
}

public enum TipoVotoPlenario
{
    VotoPresidente = 0,
    VotoRelator = 1,
    VotoRevisor = 2,
    VotoVogal = 3,
    VotoDesempate = 4
}

public enum StatusParecer
{
    EmElaboracao = 0,
    Concluido = 1,
    Homologado = 2,
    Rejeitado = 3
}

public enum TipoParecer
{
    ParecerFavoravel = 0,
    ParecerDesfavoravel = 1,
    ParecerParcialmenteFavoravel = 2,
    ParecerSemMerito = 3
}

public enum TipoEmenda
{
    EmendaAditiva = 0,
    EmendaSuperssiva = 1,
    EmendaModificativa = 2,
    EmendaSubstitutiva = 3
}

public enum StatusEmenda
{
    Proposta = 0,
    EmVotacao = 1,
    Aprovada = 2,
    Rejeitada = 3,
    Prejudicada = 4
}

public enum TipoDiligencia
{
    DiligenciaDocumental = 0,
    DiligenciaPericial = 1,
    DiligenciaTestemunhal = 2,
    DiligenciaInspecao = 3,
    DiligenciaEsclarecimento = 4
}

public enum StatusDiligencia
{
    Determinada = 0,
    EmCumprimento = 1,
    Cumprida = 2,
    NaoCumprida = 3,
    Cancelada = 4,
    Prorrogada = 5
}

public enum TipoSuspensao
{
    SuspensaoPrazo = 0,
    SuspensaoJulgamento = 1,
    SuspensaoProcesso = 2,
    SuspensaoLiminar = 3
}

public enum MotivoSuspensao
{
    AguardandoDiligencia = 0,
    AguardandoProva = 1,
    AguardandoParecer = 2,
    AguardandoDecisaoPrejudicial = 3,
    PedidoParte = 4,
    CasoFortuito = 5,
    ForcaMaior = 6,
    Outros = 7
}

public enum MotivoArquivamento
{
    ImprocedenciaDenuncia = 0,
    Desistencia = 1,
    PerdaObjeto = 2,
    Prescricao = 3,
    FaltaProvas = 4,
    IlegitimidadeParte = 5,
    FaltaInteresseAgir = 6,
    Acordo = 7,
    Outros = 8
}

public enum StatusArquivamento
{
    Solicitado = 0,
    EmAnalise = 1,
    Deferido = 2,
    Indeferido = 3
}
