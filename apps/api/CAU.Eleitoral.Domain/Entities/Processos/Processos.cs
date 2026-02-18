using CAU.Eleitoral.Domain.Common;

namespace CAU.Eleitoral.Domain.Entities.Processos;

public class JulgamentoAdmissibilidade : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Domain.Entities.Denuncias.Denuncia? Denuncia { get; set; }
    
    public DateTime DataJulgamento { get; set; }
    public TipoDecisaoAdmissibilidade Decisao { get; set; }
    public string? Motivacao { get; set; }
    public string? Parecer { get; set; }
    public Guid? ComissaoJulgadoraId { get; set; }
    public virtual Domain.Entities.Julgamentos.ComissaoJulgadora? ComissaoJulgadora { get; set; }
    public Guid? MembroComissaoId { get; set; }
    public bool RecursoDisponivel { get; set; } = true;
    public DateTime? PrazoRecurso { get; set; }
}

public enum TipoDecisaoAdmissibilidade
{
    Inadmissivel = 1,
    Admissivel = 2,
    Arquivado = 3
}

public class RecursoJulgamentoAdmissibilidade : BaseEntity
{
    public Guid JulgamentoAdmissibilidadeId { get; set; }
    public virtual JulgamentoAdmissibilidade? JulgamentoAdmissibilidade { get; set; }
    
    public Guid RecorrenteId { get; set; }
    public string? RecorrenteNome { get; set; }
    public string? TextoRecurso { get; set; }
    public DateTime DataInterposicao { get; set; }
    public StatusRecurso Status { get; set; }
    public DateTime? DataAnalise { get; set; }
    public string? Parecer { get; set; }
    public Guid? AnalisadoPorId { get; set; }
}

public class JulgamentoFinal : BaseEntity
{
    public Guid DenunciaId { get; set; }
    public virtual Domain.Entities.Denuncias.Denuncia? Denuncia { get; set; }
    
    public DateTime DataJulgamento { get; set; }
    public TipoDecisaoFinal Decisao { get; set; }
    public string? Motivacao { get; set; }
    public string? Sentenca { get; set; }
    public Guid? ComissaoJulgadoraId { get; set; }
    public virtual Domain.Entities.Julgamentos.ComissaoJulgadora? ComissaoJulgadora { get; set; }
    public bool JulgamentoUnanime { get; set; }
    public int VotosFavoraveis { get; set; }
    public int VotosContrarios { get; set; }
}

public enum TipoDecisaoFinal
{
    Procedente = 1,
    Improcedente = 2,
    ParcialmenteProcedente = 3,
    Arquivado = 4
}

public class RecursoJulgamentoFinal : BaseEntity
{
    public Guid JulgamentoFinalId { get; set; }
    public virtual JulgamentoFinal? JulgamentoFinal { get; set; }
    
    public Guid RecorrenteId { get; set; }
    public string? RecorrenteNome { get; set; }
    public string? TextoRecurso { get; set; }
    public DateTime DataInterposicao { get; set; }
    public StatusRecurso Status { get; set; }
    public DateTime? DataAnalise { get; set; }
    public string? Parecer { get; set; }
    public Guid? AnalisadoPorId { get; set; }
}

public enum StatusRecurso
{
    Pendente = 1,
    Analise = 2,
    Deferido = 3,
    Indeferido = 4,
    Arquivado = 5
}

// ImpugnacaoResultado
public class ImpugnacaoResultado : BaseEntity
{
    public Guid ChapaId { get; set; }
    public virtual Domain.Entities.Chapas.Chapa? Chapa { get; set; }
    
    public Guid? EleicaoId { get; set; }
    public virtual Domain.Entities.Core.Eleicao? Eleicao { get; set; }
    
    public Guid ImpugnanteId { get; set; }
    public string? ImpugnanteNome { get; set; }
    public string Motivo { get; set; } = string.Empty;
    public string? Descricao { get; set; }
    public DateTime DataImpugnacao { get; set; }
    public StatusImpugnacao Status { get; set; }
    public DateTime? DataAnalise { get; set; }
    public string? Parecer { get; set; }
    public Guid? AnalisadoPorId { get; set; }
    public Guid? ComissaoJulgadoraId { get; set; }
    public virtual Domain.Entities.Julgamentos.ComissaoJulgadora? ComissaoJulgadora { get; set; }
}

public enum StatusImpugnacao
{
    Pendente = 1,
    Analise = 2,
    Deferida = 3,
    Indeferida = 4,
    Arquivada = 5
}

public class RecursoImpugnacao : BaseEntity
{
    public Guid ImpugnacaoResultadoId { get; set; }
    public virtual ImpugnacaoResultado? ImpugnacaoResultado { get; set; }
    
    public Guid RecorrenteId { get; set; }
    public string? RecorrenteNome { get; set; }
    public string? TextoRecurso { get; set; }
    public DateTime DataInterposicao { get; set; }
    public StatusRecurso Status { get; set; }
    public DateTime? DataAnalise { get; set; }
    public string? Parecer { get; set; }
    public Guid? AnalisadoPorId { get; set; }
}

// Pedidos
public class Pedido : BaseEntity
{
    public Guid? EleicaoId { get; set; }
    public virtual Domain.Entities.Core.Eleicao? Eleicao { get; set; }
    
    public Guid SolicitanteId { get; set; }
    public string? SolicitanteNome { get; set; }
    public string? SolicitanteEmail { get; set; }
    
    public TipoPedido Tipo { get; set; }
    public string Assunto { get; set; } = string.Empty;
    public string Descricao { get; set; } = string.Empty;
    public StatusPedido Status { get; set; }
    public DateTime DataSolicitacao { get; set; }
    public DateTime? DataResposta { get; set; }
    public string? Resposta { get; set; }
    public Guid? RespondidoPorId { get; set; }
    public string? Observacoes { get; set; }
}

public enum TipoPedido
{
    Informacao = 1,
    Certidao = 2,
    Revisao = 3,
    Esclarecimento = 4,
    Outro = 5
}

public enum StatusPedido
{
    Pendente = 1,
    EmAnalise = 2,
    Respondido = 3,
    Encerrado = 4,
    Cancelado = 5
}

// Email Templates
public class CabecalhoEmail : BaseEntity
{
    public string Nome { get; set; } = string.Empty;
    public string? Assunto { get; set; }
    public string? CabecalhoHtml { get; set; }
    public bool Ativo { get; set; } = true;
    public TipoEmail Tipo { get; set; }
}

public class CorpoEmail : BaseEntity
{
    public Guid CabecalhoEmailId { get; set; }
    public virtual CabecalhoEmail? CabecalhoEmail { get; set; }
    
    public string Nome { get; set; } = string.Empty;
    public string? CorpoHtml { get; set; }
    public bool Ativo { get; set; } = true;
}

public enum TipoEmail
{
    Geral = 1,
    Convocacao = 2,
    Notificacao = 3,
    Resultado = 4,
    Recall = 5
}
