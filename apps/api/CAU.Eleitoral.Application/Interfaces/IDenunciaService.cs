using CAU.Eleitoral.Application.DTOs.Denuncias;
using CAU.Eleitoral.Application.DTOs.Auditoria;
using CAU.Eleitoral.Domain.Enums;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IDenunciaService
{
    // CRUD Operations
    Task<DenunciaDto?> GetByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DenunciaDto?> GetByProtocoloAsync(string protocolo, CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetAllAsync(CancellationToken cancellationToken = default);
    Task<PaginatedResultDto<DenunciaListDto>> GetPaginatedAsync(FiltroDenunciaDto filtro, CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetByStatusAsync(StatusDenuncia status, CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetByChapaAsync(Guid chapaId, CancellationToken cancellationToken = default);
    Task<IEnumerable<DenunciaDto>> GetByDenuncianteAsync(Guid denuncianteId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> CreateAsync(CreateDenunciaDto dto, Guid? userId = null, CancellationToken cancellationToken = default);
    Task<DenunciaDto> UpdateAsync(Guid id, UpdateDenunciaDto dto, CancellationToken cancellationToken = default);
    Task DeleteAsync(Guid id, CancellationToken cancellationToken = default);

    // Phase 1: Admissibilidade (Admissibility)
    Task<DenunciaDto> ReceberAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DenunciaDto> IniciarAnaliseAsync(Guid id, Guid analistaId, CancellationToken cancellationToken = default);
    Task<AnaliseDenunciaDto> ConcluirAnaliseAsync(Guid id, ConcluirAnaliseDto dto, Guid analistaId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RegistrarAdmissibilidadeAsync(Guid id, bool admissivel, string parecer, Guid relatorId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RegistrarAdmissibilidadeAsync(Guid id, RegistrarAdmissibilidadeCompletoDto dto, Guid analistaId, CancellationToken cancellationToken = default);
    Task<RequisitosFormaisDto> ValidarRequisitosFormaisAsync(Guid id, CancellationToken cancellationToken = default);

    // Phase 2: Defesa (Defense)
    Task<DenunciaDto> AbrirPrazoDefesaAsync(Guid id, DateTime prazoDefesa, CancellationToken cancellationToken = default);
    Task<DenunciaDto> SolicitarDefesaAsync(Guid id, int prazoEmDias, CancellationToken cancellationToken = default);
    Task<DenunciaDto> SolicitarDefesaAsync(Guid id, SolicitarDefesaCompletoDto dto, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RegistrarDefesaAsync(Guid id, string defesa, Guid autorId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RegistrarDefesaAsync(Guid id, RegistrarDefesaDto dto, Guid autorId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> ApresentarDefesaAsync(Guid id, CreateDefesaDto dto, Guid autorId, CancellationToken cancellationToken = default);

    // Phase 3: Julgamento (Judgment)
    Task<DenunciaDto> EncaminharParaJulgamentoAsync(Guid id, CancellationToken cancellationToken = default);
    Task<DenunciaDto> AtribuirRelatorAsync(Guid id, Guid relatorId, string? justificativa = null, CancellationToken cancellationToken = default);
    Task<DenunciaDto> AtribuirRelatorAsync(Guid id, AtribuirRelatorDto dto, CancellationToken cancellationToken = default);
    Task<ImpedimentoResultadoDto> VerificarImpedimentoRelatorAsync(Guid denunciaId, Guid relatorId, CancellationToken cancellationToken = default);
    Task<ParecerResultadoDto> RegistrarParecerAsync(Guid id, RegistrarParecerDto dto, Guid pareceristaId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RegistrarJulgamentoAsync(Guid id, StatusDenuncia decisao, string fundamentacao, CancellationToken cancellationToken = default);
    Task<DenunciaDto> JulgarAsync(Guid id, JulgarDenunciaDto dto, Guid julgadorId, CancellationToken cancellationToken = default);
    Task<DenunciaDto> JulgarAsync(Guid id, JulgarDenunciaCompletoDto dto, Guid julgadorId, CancellationToken cancellationToken = default);

    // Phase 4: Recurso (Appeal)
    Task<DenunciaDto> AbrirPrazoRecursoAsync(Guid id, DateTime prazoRecurso, CancellationToken cancellationToken = default);
    Task<DenunciaDto> AbrirPrazoRecursoAsync(Guid id, int prazoEmDias, CancellationToken cancellationToken = default);
    Task<DenunciaDto> RegistrarRecursoAsync(Guid id, string recurso, Guid autorId, CancellationToken cancellationToken = default);
    Task<RecursoResultadoDto> InterporRecursoAsync(Guid id, InterporRecursoDto dto, Guid autorId, CancellationToken cancellationToken = default);
    Task<ContraRazoesResultadoDto> RegistrarContraRazoesAsync(Guid recursoId, ContraRazoesDto dto, CancellationToken cancellationToken = default);
    Task<RecursoResultadoDto> JulgarRecursoAsync(Guid recursoId, JulgarRecursoDto dto, Guid julgadorId, CancellationToken cancellationToken = default);
    Task<IEnumerable<RecursoResultadoDto>> GetRecursosAsync(Guid denunciaId, CancellationToken cancellationToken = default);

    // Phase 5: Arquivamento (Archive)
    Task<DenunciaDto> ArquivarAsync(Guid id, string motivo, Guid? userId = null, CancellationToken cancellationToken = default);
    Task<DenunciaDto> ArquivarAsync(Guid id, ArquivarDenunciaDto dto, Guid? userId = null, CancellationToken cancellationToken = default);
    Task<DenunciaDto> ReabrirAsync(Guid id, string motivo, Guid userId, CancellationToken cancellationToken = default);

    // Provas
    Task<ProvaDenunciaDto> AddProvaAsync(Guid denunciaId, CreateProvaDenunciaDto dto, CancellationToken cancellationToken = default);
    Task RemoveProvaAsync(Guid denunciaId, Guid provaId, CancellationToken cancellationToken = default);
    Task<IEnumerable<ProvaDenunciaDto>> GetProvasAsync(Guid denunciaId, CancellationToken cancellationToken = default);

    // Defesas
    Task<DefesaDenunciaDto> AddDefesaAsync(Guid denunciaId, CreateDefesaDto dto, Guid autorId, CancellationToken cancellationToken = default);
    Task<IEnumerable<DefesaDenunciaDto>> GetDefesasAsync(Guid denunciaId, CancellationToken cancellationToken = default);

    // Pareceres
    Task<IEnumerable<ParecerResultadoDto>> GetPareceresAsync(Guid denunciaId, CancellationToken cancellationToken = default);

    // Historico
    Task<IEnumerable<HistoricoDenunciaDto>> GetHistoricoAsync(Guid denunciaId, CancellationToken cancellationToken = default);

    // Statistics
    Task<int> CountByEleicaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<int> CountByStatusAsync(StatusDenuncia status, CancellationToken cancellationToken = default);
    Task<DenunciaEstatisticasDto> GetEstatisticasAsync(Guid? eleicaoId = null, CancellationToken cancellationToken = default);
}
