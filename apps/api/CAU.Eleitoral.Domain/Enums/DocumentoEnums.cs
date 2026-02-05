namespace CAU.Eleitoral.Domain.Enums;

public enum TipoDocumento
{
    Edital = 0,
    Resolucao = 1,
    Normativa = 2,
    Portaria = 3,
    Deliberacao = 4,
    Ato = 5,
    Comunicado = 6,
    Aviso = 7,
    Convocacao = 8,
    Termo = 9,
    TermoPosse = 10,
    Diploma = 11,
    Certificado = 12,
    Declaracao = 13,
    Ata = 14,
    AtaApuracao = 15,
    BoletimUrna = 16,
    MapaVotacao = 17,
    Resultado = 18,
    Relatorio = 19,
    Modelo = 20,
    Template = 21,
    Outros = 99
}

public enum CategoriaDocumento
{
    Institucional = 0,
    Eleitoral = 1,
    Administrativo = 2,
    Legal = 3,
    Tecnico = 4
}

public enum StatusDocumento
{
    Rascunho = 0,
    EmRevisao = 1,
    Aprovado = 2,
    Publicado = 3,
    Revogado = 4,
    Arquivado = 5
}

public enum TipoPublicacao
{
    DiarioOficial = 0,
    SiteInstitucional = 1,
    Email = 2,
    Edital = 3,
    Aviso = 4
}

public enum StatusPublicacao
{
    Agendada = 0,
    Publicada = 1,
    Cancelada = 2
}

// Enums for CategoriaDocumento entity
public enum StatusCategoriaDocumento
{
    Ativa = 0,
    Inativa = 1
}

// Enums for PublicacaoOficial
public enum TipoPublicacaoOficial
{
    DiarioOficialUniao = 0,
    DiarioOficialEstado = 1,
    DiarioOficialMunicipio = 2,
    SiteInstitucional = 3,
    ImprensaOficial = 4,
    BoletimInterno = 5
}

public enum StatusPublicacaoOficial
{
    Pendente = 0,
    Agendada = 1,
    Publicada = 2,
    Confirmada = 3,
    Cancelada = 4,
    Erro = 5
}

// Enums for Termo
public enum TipoTermo
{
    TermoPosse = 0,
    TermoCompromisso = 1,
    TermoAceite = 2,
    TermoRenuncia = 3,
    TermoDesistencia = 4,
    TermoSubstituicao = 5,
    TermoResponsabilidade = 6
}

public enum StatusTermo
{
    Pendente = 0,
    Assinado = 1,
    Recusado = 2,
    Cancelado = 3,
    Expirado = 4
}

// Enums for Convocacao
public enum TipoConvocacao
{
    Reuniao = 0,
    Sessao = 1,
    Audiencia = 2,
    Assembleia = 3,
    Votacao = 4,
    Posse = 5
}

public enum StatusConvocacao
{
    Agendada = 0,
    Enviada = 1,
    Confirmada = 2,
    Realizada = 3,
    Cancelada = 4,
    Adiada = 5
}

// Enums for Ata
public enum TipoAta
{
    Reuniao = 0,
    Sessao = 1,
    Assembleia = 2,
    Apuracao = 3,
    Julgamento = 4,
    Posse = 5
}

public enum StatusAta
{
    EmElaboracao = 0,
    AguardandoAprovacao = 1,
    Aprovada = 2,
    Retificada = 3,
    Arquivada = 4
}

// Enums for Apuracao
public enum StatusApuracao
{
    NaoIniciada = 0,
    EmAndamento = 1,
    Pausada = 2,
    Concluida = 3,
    Homologada = 4,
    Contestada = 5,
    Anulada = 6
}

public enum TipoApuracao
{
    Parcial = 0,
    Final = 1,
    Retificada = 2
}

// Enums for Resultado
public enum TipoResultado
{
    Parcial = 0,
    Final = 1,
    Provisorio = 2,
    Definitivo = 3
}

public enum StatusResultado
{
    EmApuracao = 0,
    Apurado = 1,
    Publicado = 2,
    Homologado = 3,
    Impugnado = 4,
    Retificado = 5
}

// Enums for Relatorio
public enum TipoRelatorio
{
    Votacao = 0,
    Apuracao = 1,
    Participacao = 2,
    Comparecimento = 3,
    Abstencao = 4,
    Estatistico = 5,
    Consolidado = 6,
    Auditoria = 7
}

public enum StatusRelatorio
{
    EmGeracao = 0,
    Gerado = 1,
    Erro = 2
}

// Enums for Grafico
public enum TipoGrafico
{
    Pizza = 0,
    Barras = 1,
    Linhas = 2,
    Area = 3,
    Rosca = 4,
    Radar = 5
}

// Enums for Exportacao/Importacao
public enum FormatoExportacao
{
    PDF = 0,
    Excel = 1,
    CSV = 2,
    JSON = 3,
    XML = 4,
    ZIP = 5
}

public enum StatusExportacao
{
    Pendente = 0,
    EmProcessamento = 1,
    Concluida = 2,
    Erro = 3,
    Expirada = 4
}

public enum TipoExportacao
{
    Eleitores = 0,
    Votos = 1,
    Chapas = 2,
    Resultados = 3,
    Documentos = 4,
    Completa = 5
}

public enum StatusImportacao
{
    Pendente = 0,
    Validando = 1,
    EmProcessamento = 2,
    Concluida = 3,
    ConcluídaComErros = 4,
    Erro = 5,
    Cancelada = 6
}

public enum TipoImportacao
{
    Eleitores = 0,
    Profissionais = 1,
    Chapas = 2,
    Documentos = 3
}

// Enums for Assinatura Digital
public enum TipoAssinatura
{
    Simples = 0,
    Avancada = 1,
    Qualificada = 2,
    ICP_Brasil = 3
}

public enum StatusAssinatura
{
    Pendente = 0,
    Assinada = 1,
    Recusada = 2,
    Expirada = 3,
    Invalida = 4,
    Revogada = 5
}

// Enums for Certificado Digital
public enum TipoCertificadoDigital
{
    A1 = 0,
    A3 = 1,
    S1 = 2,
    S3 = 3,
    T3 = 4,
    T4 = 5
}

public enum StatusCertificadoDigital
{
    Valido = 0,
    Expirado = 1,
    Revogado = 2,
    Suspenso = 3
}

// Enums for CarimboTempo
public enum StatusCarimboTempo
{
    Valido = 0,
    Invalido = 1,
    Expirado = 2
}

// Enums for Modelo/Template
public enum TipoModeloDocumento
{
    Edital = 0,
    Ata = 1,
    Termo = 2,
    Comunicado = 3,
    Resolucao = 4,
    Portaria = 5,
    Diploma = 6,
    Certificado = 7,
    Declaracao = 8,
    Relatorio = 9
}

public enum StatusModeloDocumento
{
    Ativo = 0,
    Inativo = 1,
    EmRevisao = 2
}

// Enums for Diploma/Certificado
public enum StatusDiploma
{
    Gerado = 0,
    Assinado = 1,
    Entregue = 2,
    Cancelado = 3
}

public enum StatusCertificado
{
    Gerado = 0,
    Assinado = 1,
    Entregue = 2,
    Cancelado = 3
}

// Enums for Declaracao
public enum TipoDeclaracao
{
    Participacao = 0,
    Comparecimento = 1,
    QuitacaoEleitoral = 2,
    Elegibilidade = 3,
    Inelegibilidade = 4,
    Regularidade = 5
}
