using CAU.Eleitoral.Application.DTOs.Relatorios;

namespace CAU.Eleitoral.Application.Interfaces;

public interface IRelatorioService
{
    // Report Generation
    Task<RelatorioDto> GerarRelatorioAsync(GerarRelatorioDto dto, Guid solicitanteId, CancellationToken cancellationToken = default);
    Task<RelatorioDto?> GetRelatorioByIdAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<RelatorioDto>> GetRelatoriosAsync(FiltroRelatorioDto? filtro = null, CancellationToken cancellationToken = default);
    Task<IEnumerable<RelatorioDto>> GetRelatoriosBySolicitanteAsync(Guid solicitanteId, CancellationToken cancellationToken = default);
    Task DeleteRelatorioAsync(Guid id, CancellationToken cancellationToken = default);
    Task<Stream> DownloadRelatorioAsync(Guid id, CancellationToken cancellationToken = default);
    Task<byte[]> DownloadRelatorioBytesAsync(Guid id, CancellationToken cancellationToken = default);

    // Specific Reports
    Task<RelatorioResultadoEleicaoDto> GerarRelatorioResultadoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<RelatorioEstatisticasDto> GerarRelatorioEstatisticasAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<byte[]> GerarRelatorioListaEleitoresAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default);
    Task<byte[]> GerarRelatorioListaChapasAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default);
    Task<byte[]> GerarRelatorioAtaApuracaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<byte[]> GerarRelatorioBoletimUrnaAsync(Guid eleicaoId, CancellationToken cancellationToken = default);
    Task<byte[]> GerarRelatorioDenunciasAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default);
    Task<byte[]> GerarRelatorioImpugnacoesAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default);
    Task<byte[]> GerarRelatorioAuditoriaAsync(DateTime dataInicio, DateTime dataFim, FormatoRelatorio formato, CancellationToken cancellationToken = default);
    Task<byte[]> GerarRelatorioAcessosAsync(DateTime dataInicio, DateTime dataFim, FormatoRelatorio formato, CancellationToken cancellationToken = default);
    Task<byte[]> GerarRelatorioCalendarioAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default);
    Task<byte[]> GerarRelatorioComprovantesAsync(Guid eleicaoId, FormatoRelatorio formato, CancellationToken cancellationToken = default);

    // Report Status
    Task<StatusRelatorio> GetStatusRelatorioAsync(Guid id, CancellationToken cancellationToken = default);
    Task<IEnumerable<RelatorioDto>> GetRelatoriosEmProcessamentoAsync(CancellationToken cancellationToken = default);
    Task CancelarRelatorioAsync(Guid id, CancellationToken cancellationToken = default);

    // Cleanup
    Task LimparRelatoriosExpiradosAsync(CancellationToken cancellationToken = default);
}
