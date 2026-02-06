using System.Security.Cryptography;
using System.Text;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using CAU.Eleitoral.Application.DTOs.Votacao;
using CAU.Eleitoral.Application.Interfaces;
using CAU.Eleitoral.Domain.Entities.Core;
using CAU.Eleitoral.Domain.Entities.Chapas;
using CAU.Eleitoral.Domain.Entities.Usuarios;
using CAU.Eleitoral.Domain.Enums;
using CAU.Eleitoral.Domain.Interfaces.Repositories;

namespace CAU.Eleitoral.Application.Services;

/// <summary>
/// Service responsavel por todas as operacoes de votacao do sistema eleitoral CAU.
/// Implementa validacoes de elegibilidade, registro de votos, emissao de comprovantes,
/// estatisticas e operacoes administrativas.
/// </summary>
public class VotacaoService : IVotacaoService
{
    private readonly IRepository<Voto> _votoRepository;
    private readonly IRepository<Eleitor> _eleitorRepository;
    private readonly IRepository<Eleicao> _eleicaoRepository;
    private readonly IRepository<ChapaEleicao> _chapaRepository;
    private readonly IRepository<Usuario> _usuarioRepository;
    private readonly IRepository<Profissional> _profissionalRepository;
    private readonly IUnitOfWork _unitOfWork;
    private readonly ILogger<VotacaoService> _logger;

    public VotacaoService(
        IRepository<Voto> votoRepository,
        IRepository<Eleitor> eleitorRepository,
        IRepository<Eleicao> eleicaoRepository,
        IRepository<ChapaEleicao> chapaRepository,
        IRepository<Usuario> usuarioRepository,
        IRepository<Profissional> profissionalRepository,
        IUnitOfWork unitOfWork,
        ILogger<VotacaoService> logger)
    {
        _votoRepository = votoRepository;
        _eleitorRepository = eleitorRepository;
        _eleicaoRepository = eleicaoRepository;
        _chapaRepository = chapaRepository;
        _usuarioRepository = usuarioRepository;
        _profissionalRepository = profissionalRepository;
        _unitOfWork = unitOfWork;
        _logger = logger;
    }

    #region Core Voting Operations (Public User)

    /// <summary>
    /// Verifica se o usuario pode votar na eleicao especificada.
    /// Valida: registro como profissional, elegibilidade, eleicao ativa, periodo de votacao.
    /// </summary>
    public async Task<ElegibilidadeVotoDto> VerificarElegibilidadeAsync(
        Guid eleicaoId,
        Guid userId,
        CancellationToken cancellationToken = default)
    {
        _logger.LogInformation("Verificando elegibilidade do usuario {UserId} para eleicao {EleicaoId}", userId, eleicaoId);

        // Buscar eleicao
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken);
        if (eleicao == null)
        {
            return new ElegibilidadeVotoDto
            {
                PodeVotar = false,
                JaVotou = false,
                MotivoInelegibilidade = "Eleicao nao encontrada",
                EleicaoEmAndamento = false
            };
        }

        // Buscar profissional vinculado ao usuario
        var profissional = await _profissionalRepository.FirstOrDefaultAsync(
            p => p.UsuarioId == userId, cancellationToken);

        if (profissional == null)
        {
            return new ElegibilidadeVotoDto
            {
                PodeVotar = false,
                JaVotou = false,
                MotivoInelegibilidade = "Usuario nao e um profissional registrado",
                EleicaoEmAndamento = eleicao.Status == StatusEleicao.EmAndamento,
                DataInicioVotacao = eleicao.DataVotacaoInicio,
                DataFimVotacao = eleicao.DataVotacaoFim
            };
        }

        // Verificar se profissional esta ativo
        if (profissional.Status != StatusProfissional.Ativo)
        {
            return new ElegibilidadeVotoDto
            {
                PodeVotar = false,
                JaVotou = false,
                MotivoInelegibilidade = $"Profissional com status {profissional.Status}. Apenas profissionais ativos podem votar",
                EleicaoEmAndamento = eleicao.Status == StatusEleicao.EmAndamento,
                DataInicioVotacao = eleicao.DataVotacaoInicio,
                DataFimVotacao = eleicao.DataVotacaoFim
            };
        }

        // Verificar se profissional esta apto como eleitor
        if (!profissional.EleitorApto)
        {
            return new ElegibilidadeVotoDto
            {
                PodeVotar = false,
                JaVotou = false,
                MotivoInelegibilidade = profissional.MotivoInaptidao ?? "Profissional nao esta apto para votar",
                EleicaoEmAndamento = eleicao.Status == StatusEleicao.EmAndamento,
                DataInicioVotacao = eleicao.DataVotacaoInicio,
                DataFimVotacao = eleicao.DataVotacaoFim
            };
        }

        // Buscar eleitor na eleicao
        var eleitor = await _eleitorRepository.FirstOrDefaultAsync(
            e => e.EleicaoId == eleicaoId && e.ProfissionalId == profissional.Id, cancellationToken);

        if (eleitor == null)
        {
            return new ElegibilidadeVotoDto
            {
                PodeVotar = false,
                JaVotou = false,
                MotivoInelegibilidade = "Eleitor nao cadastrado para esta eleicao",
                EleicaoEmAndamento = eleicao.Status == StatusEleicao.EmAndamento,
                DataInicioVotacao = eleicao.DataVotacaoInicio,
                DataFimVotacao = eleicao.DataVotacaoFim
            };
        }

        // Verificar se eleitor esta apto
        if (!eleitor.Apto)
        {
            return new ElegibilidadeVotoDto
            {
                PodeVotar = false,
                JaVotou = false,
                MotivoInelegibilidade = eleitor.MotivoInaptidao ?? "Eleitor nao esta apto para votar nesta eleicao",
                EleicaoEmAndamento = eleicao.Status == StatusEleicao.EmAndamento,
                DataInicioVotacao = eleicao.DataVotacaoInicio,
                DataFimVotacao = eleicao.DataVotacaoFim
            };
        }

        // Verificar se ja votou
        if (eleitor.Votou)
        {
            return new ElegibilidadeVotoDto
            {
                PodeVotar = false,
                JaVotou = true,
                MotivoInelegibilidade = "Voce ja votou nesta eleicao",
                EleicaoEmAndamento = eleicao.Status == StatusEleicao.EmAndamento,
                DataInicioVotacao = eleicao.DataVotacaoInicio,
                DataFimVotacao = eleicao.DataVotacaoFim
            };
        }

        // Verificar se eleicao esta aberta para votacao
        var eleicaoAberta = await EleicaoAbertaParaVotacaoAsync(eleicaoId, cancellationToken);
        if (!eleicaoAberta)
        {
            var agora = DateTime.UtcNow;
            string motivo;

            if (eleicao.Status != StatusEleicao.EmAndamento)
                motivo = $"Eleicao com status {eleicao.Status}. Votacao nao esta aberta";
            else if (eleicao.FaseAtual != FaseEleicao.Votacao)
                motivo = $"Eleicao em fase {eleicao.FaseAtual}. Aguarde a fase de votacao";
            else if (eleicao.DataVotacaoInicio.HasValue && agora < eleicao.DataVotacaoInicio.Value)
                motivo = $"Votacao inicia em {eleicao.DataVotacaoInicio.Value:dd/MM/yyyy HH:mm}";
            else if (eleicao.DataVotacaoFim.HasValue && agora > eleicao.DataVotacaoFim.Value)
                motivo = "Periodo de votacao encerrado";
            else
                motivo = "Eleicao nao esta aberta para votacao";

            return new ElegibilidadeVotoDto
            {
                PodeVotar = false,
                JaVotou = false,
                MotivoInelegibilidade = motivo,
                EleicaoEmAndamento = eleicao.Status == StatusEleicao.EmAndamento,
                DataInicioVotacao = eleicao.DataVotacaoInicio,
                DataFimVotacao = eleicao.DataVotacaoFim
            };
        }

        // Eleitor pode votar
        return new ElegibilidadeVotoDto
        {
            PodeVotar = true,
            JaVotou = false,
            MotivoInelegibilidade = null,
            EleicaoEmAndamento = true,
            DataInicioVotacao = eleicao.DataVotacaoInicio,
            DataFimVotacao = eleicao.DataVotacaoFim
        };
    }

    /// <summary>
    /// Verifica se o usuario ja votou na eleicao e retorna detalhes do status.
    /// </summary>
    public async Task<StatusVotoDto> VerificarStatusVotoAsync(
        Guid eleicaoId,
        Guid userId,
        CancellationToken cancellationToken = default)
    {
        var profissional = await _profissionalRepository.FirstOrDefaultAsync(
            p => p.UsuarioId == userId, cancellationToken);

        if (profissional == null)
        {
            return new StatusVotoDto { Votou = false };
        }

        var eleitor = await _eleitorRepository.FirstOrDefaultAsync(
            e => e.EleicaoId == eleicaoId && e.ProfissionalId == profissional.Id, cancellationToken);

        if (eleitor == null || !eleitor.Votou)
        {
            return new StatusVotoDto { Votou = false };
        }

        return new StatusVotoDto
        {
            Votou = true,
            DataVoto = eleitor.DataVoto,
            HashComprovante = eleitor.ComprovanteVotacao
        };
    }

    /// <summary>
    /// Obtem a cedula de votacao com as chapas disponiveis para o usuario.
    /// Valida elegibilidade antes de retornar a cedula.
    /// </summary>
    public async Task<CedulaVotacaoDto> ObterCedulaAsync(
        Guid eleicaoId,
        Guid userId,
        CancellationToken cancellationToken = default)
    {
        // Verificar elegibilidade primeiro
        var elegibilidade = await VerificarElegibilidadeAsync(eleicaoId, userId, cancellationToken);
        if (!elegibilidade.PodeVotar)
        {
            throw new InvalidOperationException(elegibilidade.MotivoInelegibilidade ?? "Eleitor nao pode votar");
        }

        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        // Buscar chapas com status Deferida ou Registrada
        var chapas = await _chapaRepository.Query()
            .Include(c => c.Membros)
                .ThenInclude(m => m.Profissional)
            .Where(c => c.EleicaoId == eleicaoId &&
                       (c.Status == StatusChapa.Deferida || c.Status == StatusChapa.Registrada))
            .OrderBy(c => c.OrdemSorteio)
            .ThenBy(c => c.Numero)
            .ToListAsync(cancellationToken);

        var opcoes = chapas.Select((c, index) => new OpcaoVotoCedulaDto
        {
            ChapaId = c.Id,
            Numero = int.TryParse(c.Numero, out var num) ? num : index + 1,
            Nome = c.Nome,
            Sigla = c.Sigla,
            Lema = c.Slogan,
            Membros = c.Membros?
                .Where(m => m.Status == StatusMembroChapa.Confirmado)
                .OrderBy(m => m.Tipo)
                .ThenBy(m => m.Ordem)
                .Select(m => new MembroChapaResumoDto
                {
                    Nome = m.Profissional?.Nome ?? m.Nome,
                    Cargo = m.Cargo ?? GetCargoDescricao(m.Tipo)
                })
                .ToList() ?? new List<MembroChapaResumoDto>()
        }).ToList();

        return new CedulaVotacaoDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            Instrucoes = "Selecione uma chapa para votar ou escolha voto em branco/nulo",
            Opcoes = opcoes,
            PermiteBranco = true,
            PermiteNulo = true
        };
    }

    /// <summary>
    /// Registra o voto do usuario.
    /// Valida: elegibilidade, chapa valida, previne voto duplicado.
    /// </summary>
    public async Task<ComprovanteVotoDto> RegistrarVotoAsync(
        RegistrarVotoDto dto,
        Guid userId,
        CancellationToken cancellationToken = default)
    {
        _logger.LogInformation("Registrando voto do usuario {UserId} na eleicao {EleicaoId}", userId, dto.EleicaoId);

        // Verificar elegibilidade
        var elegibilidade = await VerificarElegibilidadeAsync(dto.EleicaoId, userId, cancellationToken);
        if (!elegibilidade.PodeVotar)
        {
            throw new InvalidOperationException(elegibilidade.MotivoInelegibilidade ?? "Eleitor nao pode votar");
        }

        // Buscar eleicao
        var eleicao = await _eleicaoRepository.GetByIdAsync(dto.EleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {dto.EleicaoId} nao encontrada");

        // Buscar profissional e eleitor
        var profissional = await _profissionalRepository.FirstOrDefaultAsync(
            p => p.UsuarioId == userId, cancellationToken)
            ?? throw new InvalidOperationException("Usuario nao e um profissional registrado");

        var eleitor = await _eleitorRepository.FirstOrDefaultAsync(
            e => e.EleicaoId == dto.EleicaoId && e.ProfissionalId == profissional.Id, cancellationToken)
            ?? throw new InvalidOperationException("Eleitor nao encontrado");

        // Validar tipo de voto e chapa
        if (dto.TipoVoto == TipoVoto.Chapa)
        {
            if (!dto.ChapaId.HasValue)
            {
                throw new InvalidOperationException("Para voto em chapa, e necessario informar o ID da chapa");
            }

            var chapa = await _chapaRepository.GetByIdAsync(dto.ChapaId.Value, cancellationToken)
                ?? throw new KeyNotFoundException($"Chapa {dto.ChapaId} nao encontrada");

            // Validar status da chapa
            if (chapa.Status != StatusChapa.Deferida && chapa.Status != StatusChapa.Registrada)
            {
                throw new InvalidOperationException($"Chapa com status {chapa.Status} nao pode receber votos");
            }

            if (chapa.EleicaoId != dto.EleicaoId)
            {
                throw new InvalidOperationException("Chapa nao pertence a esta eleicao");
            }
        }

        // Gerar hashes unicos
        var hashEleitor = GerarHashEleitor(profissional.Id, dto.EleicaoId);
        var hashVoto = GerarHashVoto(dto.EleicaoId, dto.ChapaId, DateTime.UtcNow);

        // Criar voto
        var voto = new Voto
        {
            EleicaoId = dto.EleicaoId,
            ChapaId = dto.TipoVoto == TipoVoto.Chapa ? dto.ChapaId : null,
            Tipo = dto.TipoVoto,
            Status = StatusVoto.Confirmado,
            Modo = ModoVotacao.Online,
            HashEleitor = hashEleitor,
            HashVoto = hashVoto,
            DataVoto = DateTime.UtcNow,
            Comprovante = GerarComprovante(hashVoto)
        };

        await _votoRepository.AddAsync(voto, cancellationToken);

        // Atualizar status do eleitor
        eleitor.Votou = true;
        eleitor.DataVoto = DateTime.UtcNow;
        eleitor.ComprovanteVotacao = voto.Comprovante;

        await _eleitorRepository.UpdateAsync(eleitor, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Voto registrado com sucesso. Eleicao: {EleicaoId}, Hash: {HashVoto}",
            dto.EleicaoId, hashVoto);

        return new ComprovanteVotoDto
        {
            Id = voto.Id,
            Protocolo = voto.Comprovante ?? $"COMP-{voto.DataVoto:yyyyMMdd}-{hashVoto[..8]}",
            EleicaoId = dto.EleicaoId,
            EleicaoNome = eleicao.Nome,
            DataHoraVoto = voto.DataVoto,
            HashComprovante = hashVoto,
            Mensagem = "Voto registrado com sucesso. Guarde seu comprovante."
        };
    }

    /// <summary>
    /// Obtem o comprovante de voto do usuario para uma eleicao especifica.
    /// </summary>
    public async Task<ComprovanteVotoDto?> ObterComprovanteAsync(
        Guid eleicaoId,
        Guid userId,
        CancellationToken cancellationToken = default)
    {
        var profissional = await _profissionalRepository.FirstOrDefaultAsync(
            p => p.UsuarioId == userId, cancellationToken);

        if (profissional == null) return null;

        var eleitor = await _eleitorRepository.Query()
            .Include(e => e.Eleicao)
            .FirstOrDefaultAsync(e => e.EleicaoId == eleicaoId &&
                                      e.ProfissionalId == profissional.Id &&
                                      e.Votou, cancellationToken);

        if (eleitor == null) return null;

        return new ComprovanteVotoDto
        {
            Id = eleitor.Id,
            Protocolo = eleitor.ComprovanteVotacao ?? "",
            EleicaoId = eleicaoId,
            EleicaoNome = eleitor.Eleicao?.Nome ?? "",
            DataHoraVoto = eleitor.DataVoto ?? DateTime.MinValue,
            HashComprovante = eleitor.ComprovanteVotacao ?? "",
            Mensagem = "Comprovante de voto"
        };
    }

    /// <summary>
    /// Lista eleicoes disponiveis para votacao do usuario.
    /// </summary>
    public async Task<IEnumerable<EleicaoVotacaoDto>> GetEleicoesDisponiveisAsync(
        Guid userId,
        CancellationToken cancellationToken = default)
    {
        var profissional = await _profissionalRepository.FirstOrDefaultAsync(
            p => p.UsuarioId == userId, cancellationToken);

        if (profissional == null)
        {
            return Enumerable.Empty<EleicaoVotacaoDto>();
        }

        // Buscar eleicoes onde o profissional esta cadastrado como eleitor
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Eleicao)
                .ThenInclude(el => el!.Chapas)
            .Where(e => e.ProfissionalId == profissional.Id)
            .ToListAsync(cancellationToken);

        var agora = DateTime.UtcNow;

        return eleitores
            .Where(e => e.Eleicao != null)
            .Select(e => new EleicaoVotacaoDto
            {
                Id = e.Eleicao!.Id,
                Nome = e.Eleicao.Nome,
                Descricao = e.Eleicao.Descricao,
                DataInicioVotacao = e.Eleicao.DataVotacaoInicio ?? e.Eleicao.DataInicio,
                DataFimVotacao = e.Eleicao.DataVotacaoFim ?? e.Eleicao.DataFim,
                EmAndamento = e.Eleicao.Status == StatusEleicao.EmAndamento &&
                              e.Eleicao.FaseAtual == FaseEleicao.Votacao &&
                              (!e.Eleicao.DataVotacaoInicio.HasValue || agora >= e.Eleicao.DataVotacaoInicio.Value) &&
                              (!e.Eleicao.DataVotacaoFim.HasValue || agora <= e.Eleicao.DataVotacaoFim.Value),
                JaVotou = e.Votou,
                TotalChapas = e.Eleicao.Chapas?
                    .Count(c => c.Status == StatusChapa.Deferida || c.Status == StatusChapa.Registrada) ?? 0
            })
            .OrderByDescending(e => e.EmAndamento)
            .ThenByDescending(e => e.DataInicioVotacao)
            .ToList();
    }

    /// <summary>
    /// Obtem historico de votos do usuario em todas as eleicoes.
    /// </summary>
    public async Task<IEnumerable<HistoricoVotoDto>> GetHistoricoAsync(
        Guid userId,
        CancellationToken cancellationToken = default)
    {
        var profissional = await _profissionalRepository.FirstOrDefaultAsync(
            p => p.UsuarioId == userId, cancellationToken);

        if (profissional == null)
        {
            return Enumerable.Empty<HistoricoVotoDto>();
        }

        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Eleicao)
            .Where(e => e.ProfissionalId == profissional.Id && e.Votou)
            .OrderByDescending(e => e.DataVoto)
            .ToListAsync(cancellationToken);

        return eleitores
            .Where(e => e.Eleicao != null)
            .Select(e => new HistoricoVotoDto
            {
                EleicaoId = e.Eleicao!.Id,
                EleicaoNome = e.Eleicao.Nome,
                AnoEleicao = e.Eleicao.Ano,
                DataVoto = e.DataVoto ?? DateTime.MinValue,
                HashComprovante = e.ComprovanteVotacao ?? ""
            })
            .ToList();
    }

    #endregion

    #region Administrative Operations

    /// <summary>
    /// Obtem estatisticas detalhadas de votacao de uma eleicao.
    /// </summary>
    public async Task<EstatisticasVotacaoDto> GetEstatisticasAsync(
        Guid eleicaoId,
        CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        var totalEleitores = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId, cancellationToken);

        var totalEleitoresAptos = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Apto, cancellationToken);

        var totalVotantes = await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Votou, cancellationToken);

        var totalVotos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId, cancellationToken);

        var votosValidos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Chapa && v.Status == StatusVoto.Confirmado,
            cancellationToken);

        var votosBrancos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Branco, cancellationToken);

        var votosNulos = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == TipoVoto.Nulo, cancellationToken);

        var votosAnulados = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId &&
                 (v.Tipo == TipoVoto.Anulado || v.Status == StatusVoto.Anulado), cancellationToken);

        var votosPresenciais = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Modo == ModoVotacao.Presencial, cancellationToken);

        var votosOnline = await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Modo == ModoVotacao.Online, cancellationToken);

        var percentualComparecimento = totalEleitoresAptos > 0
            ? (double)totalVotantes / totalEleitoresAptos * 100
            : 0;

        return new EstatisticasVotacaoDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            TotalEleitores = totalEleitores,
            TotalEleitoresAptos = totalEleitoresAptos,
            TotalVotantes = totalVotantes,
            TotalVotos = totalVotos,
            VotosValidos = votosValidos,
            VotosBrancos = votosBrancos,
            VotosNulos = votosNulos,
            VotosAnulados = votosAnulados,
            TotalAbstencoes = totalEleitoresAptos - totalVotantes,
            PercentualComparecimento = Math.Round(percentualComparecimento, 2),
            PercentualAbstencao = Math.Round(100 - percentualComparecimento, 2),
            PercentualParticipacao = totalEleitoresAptos > 0
                ? Math.Round((decimal)totalVotantes / totalEleitoresAptos * 100, 2)
                : 0,
            VotosPresenciais = votosPresenciais,
            VotosOnline = votosOnline,
            UltimaAtualizacao = DateTime.UtcNow
        };
    }

    /// <summary>
    /// Lista eleitores que votaram em uma eleicao (paginado).
    /// </summary>
    public async Task<PagedResultDto<EleitorVotouDto>> GetEleitoresQueVotaramAsync(
        Guid eleicaoId,
        int page,
        int pageSize,
        CancellationToken cancellationToken = default)
    {
        var query = _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Where(e => e.EleicaoId == eleicaoId && e.Votou);

        var totalCount = await query.CountAsync(cancellationToken);

        var eleitores = await query
            .OrderBy(e => e.DataVoto)
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .ToListAsync(cancellationToken);

        var items = eleitores.Select(e => new EleitorVotouDto
        {
            EleitorId = e.Id,
            Nome = e.Profissional?.Nome ?? "",
            RegistroCAU = e.Profissional?.RegistroCAU,
            DataVoto = e.DataVoto ?? DateTime.MinValue
        });

        return new PagedResultDto<EleitorVotouDto>
        {
            Items = items,
            TotalCount = totalCount,
            Page = page,
            PageSize = pageSize
        };
    }

    /// <summary>
    /// Anula um voto (operacao administrativa restrita).
    /// </summary>
    public async Task AnularVotoAsync(
        Guid votoId,
        string motivo,
        Guid adminUserId,
        CancellationToken cancellationToken = default)
    {
        _logger.LogWarning("Anulacao de voto solicitada. VotoId: {VotoId}, AdminId: {AdminId}, Motivo: {Motivo}",
            votoId, adminUserId, motivo);

        var voto = await _votoRepository.GetByIdAsync(votoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Voto {votoId} nao encontrado");

        if (voto.Status == StatusVoto.Anulado)
        {
            throw new InvalidOperationException("Este voto ja foi anulado");
        }

        // Registrar anulacao
        voto.Status = StatusVoto.Anulado;
        voto.Tipo = TipoVoto.Anulado;

        await _votoRepository.UpdateAsync(voto, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogWarning("Voto anulado com sucesso. VotoId: {VotoId}", votoId);
    }

    #endregion

    #region Legacy/Extended Operations

    /// <summary>
    /// Registra um voto usando os DTOs legados (compatibilidade).
    /// </summary>
    public async Task<ComprovanteVotoLegadoDto> VotarAsync(
        RegistrarVotoLegadoDto dto,
        CancellationToken cancellationToken = default)
    {
        // Validate eleitor
        var validacao = await ValidarEleitorAsync(
            new ValidarVotoDto { EleicaoId = dto.EleicaoId, EleitorId = dto.EleitorId },
            cancellationToken);

        if (!validacao.PodeVotar)
            throw new InvalidOperationException(validacao.MotivoImpedimento ?? "Eleitor nao pode votar");

        // Get eleicao
        var eleicao = await _eleicaoRepository.GetByIdAsync(dto.EleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {dto.EleicaoId} nao encontrada");

        // Get eleitor
        var eleitor = await _eleitorRepository.FirstOrDefaultAsync(
            e => e.EleicaoId == dto.EleicaoId && e.ProfissionalId == dto.EleitorId, cancellationToken)
            ?? throw new KeyNotFoundException("Eleitor nao encontrado");

        // Validate chapa if voting for one
        if (dto.Tipo == TipoVoto.Chapa && dto.ChapaId.HasValue)
        {
            var chapa = await _chapaRepository.GetByIdAsync(dto.ChapaId.Value, cancellationToken)
                ?? throw new KeyNotFoundException($"Chapa {dto.ChapaId} nao encontrada");

            if (chapa.Status != StatusChapa.Deferida && chapa.Status != StatusChapa.Registrada)
            {
                throw new InvalidOperationException($"Chapa com status {chapa.Status} nao pode receber votos");
            }
        }

        // Generate hashes
        var hashEleitor = GerarHashEleitor(dto.EleitorId, dto.EleicaoId);
        var hashVoto = GerarHashVoto(dto.EleicaoId, dto.ChapaId, DateTime.UtcNow);

        // Get chapa name if applicable
        string? chapaNome = null;
        if (dto.ChapaId.HasValue)
        {
            var chapa = await _chapaRepository.GetByIdAsync(dto.ChapaId.Value, cancellationToken);
            chapaNome = chapa?.Nome;
        }

        // Create vote
        var voto = new Voto
        {
            EleicaoId = dto.EleicaoId,
            ChapaId = dto.ChapaId,
            Tipo = dto.Tipo,
            Status = StatusVoto.Confirmado,
            Modo = dto.Modo,
            HashEleitor = hashEleitor,
            HashVoto = hashVoto,
            DataVoto = DateTime.UtcNow,
            IpAddress = dto.IpAddress,
            UserAgent = dto.UserAgent,
            Comprovante = GerarComprovante(hashVoto)
        };

        await _votoRepository.AddAsync(voto, cancellationToken);

        // Update eleitor status
        eleitor.Votou = true;
        eleitor.DataVoto = DateTime.UtcNow;
        eleitor.ComprovanteVotacao = voto.Comprovante;

        await _eleitorRepository.UpdateAsync(eleitor, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Voto registrado na eleicao {EleicaoId}. Hash: {HashVoto}", dto.EleicaoId, hashVoto);

        return new ComprovanteVotoLegadoDto
        {
            Comprovante = voto.Comprovante ?? "",
            HashVoto = hashVoto,
            DataVoto = voto.DataVoto,
            EleicaoNome = eleicao.Nome,
            ChapaNome = chapaNome,
            TipoVoto = dto.Tipo,
            Mensagem = "Voto registrado com sucesso"
        };
    }

    /// <summary>
    /// Valida se um eleitor pode votar (usando DTOs legados).
    /// </summary>
    public async Task<ValidacaoVotoResultDto> ValidarEleitorAsync(
        ValidarVotoDto dto,
        CancellationToken cancellationToken = default)
    {
        // Check if eleicao is open
        var eleicaoAberta = await EleicaoAbertaParaVotacaoAsync(dto.EleicaoId, cancellationToken);

        // Check if eleitor exists and is eligible
        var eleitor = await _eleitorRepository.FirstOrDefaultAsync(
            e => e.EleicaoId == dto.EleicaoId && e.ProfissionalId == dto.EleitorId, cancellationToken);

        if (eleitor == null)
        {
            return new ValidacaoVotoResultDto
            {
                PodeVotar = false,
                JaVotou = false,
                EleitorApto = false,
                EleicaoAberta = eleicaoAberta,
                MotivoImpedimento = "Eleitor nao cadastrado para esta eleicao"
            };
        }

        if (!eleitor.Apto)
        {
            return new ValidacaoVotoResultDto
            {
                PodeVotar = false,
                JaVotou = eleitor.Votou,
                EleitorApto = false,
                EleicaoAberta = eleicaoAberta,
                MotivoImpedimento = eleitor.MotivoInaptidao ?? "Eleitor nao esta apto para votar"
            };
        }

        if (eleitor.Votou)
        {
            return new ValidacaoVotoResultDto
            {
                PodeVotar = false,
                JaVotou = true,
                EleitorApto = true,
                EleicaoAberta = eleicaoAberta,
                MotivoImpedimento = "Eleitor ja votou nesta eleicao"
            };
        }

        if (!eleicaoAberta)
        {
            return new ValidacaoVotoResultDto
            {
                PodeVotar = false,
                JaVotou = false,
                EleitorApto = true,
                EleicaoAberta = false,
                MotivoImpedimento = "Eleicao nao esta aberta para votacao"
            };
        }

        return new ValidacaoVotoResultDto
        {
            PodeVotar = true,
            JaVotou = false,
            EleitorApto = true,
            EleicaoAberta = true,
            MotivoImpedimento = null
        };
    }

    /// <summary>
    /// Obtem a cedula eleitoral (DTOs legados).
    /// </summary>
    public async Task<CedulaEleitoralDto> ObterCedulaEleitoralAsync(
        Guid eleicaoId,
        Guid eleitorId,
        CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        var eleitor = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .FirstOrDefaultAsync(e => e.EleicaoId == eleicaoId && e.ProfissionalId == eleitorId, cancellationToken)
            ?? throw new KeyNotFoundException("Eleitor nao encontrado");

        var chapas = await _chapaRepository.Query()
            .Include(c => c.Membros)
            .ThenInclude(m => m.Profissional)
            .Where(c => c.EleicaoId == eleicaoId &&
                       (c.Status == StatusChapa.Deferida || c.Status == StatusChapa.Registrada))
            .OrderBy(c => c.Numero)
            .ToListAsync(cancellationToken);

        var opcoes = chapas.Select((c, index) => new OpcaoVotoDto
        {
            ChapaId = c.Id,
            Numero = int.TryParse(c.Numero, out var num) ? num : index + 1,
            Nome = c.Nome,
            Sigla = c.Sigla,
            PresidenteNome = c.Membros?.FirstOrDefault(m => m.Tipo == TipoMembroChapa.Presidente)?.Profissional?.Nome,
            Ordem = index + 1
        }).ToList();

        return new CedulaEleitoralDto
        {
            EleicaoId = eleicaoId,
            EleicaoNome = eleicao.Nome,
            EleitorId = eleitor.Id,
            EleitorNome = eleitor.Profissional?.Nome ?? "",
            Opcoes = opcoes,
            PermiteVotoBranco = true,
            ValidoAte = DateTime.UtcNow.AddMinutes(30)
        };
    }

    /// <summary>
    /// Obtem comprovante pelo hash do voto.
    /// </summary>
    public async Task<ComprovanteVotoLegadoDto?> ObterComprovantePorHashAsync(
        string hashVoto,
        CancellationToken cancellationToken = default)
    {
        var voto = await _votoRepository.Query()
            .Include(v => v.Eleicao)
            .Include(v => v.Chapa)
            .FirstOrDefaultAsync(v => v.HashVoto == hashVoto, cancellationToken);

        if (voto == null) return null;

        return new ComprovanteVotoLegadoDto
        {
            Comprovante = voto.Comprovante ?? "",
            HashVoto = voto.HashVoto,
            DataVoto = voto.DataVoto,
            EleicaoNome = voto.Eleicao?.Nome ?? "",
            ChapaNome = voto.Chapa?.Nome,
            TipoVoto = voto.Tipo,
            Mensagem = "Comprovante de voto"
        };
    }

    /// <summary>
    /// Verifica se um voto existe pelo hash.
    /// </summary>
    public async Task<bool> VerificarVotoAsync(string hashVoto, CancellationToken cancellationToken = default)
    {
        return await _votoRepository.AnyAsync(v => v.HashVoto == hashVoto, cancellationToken);
    }

    #endregion

    #region Eleitor Management

    public async Task<EleitorDto?> GetEleitorByIdAsync(Guid id, CancellationToken cancellationToken = default)
    {
        var eleitor = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .FirstOrDefaultAsync(e => e.Id == id, cancellationToken);

        return eleitor == null ? null : MapEleitorToDto(eleitor);
    }

    public async Task<EleitorDto?> GetEleitorByProfissionalAsync(
        Guid eleicaoId,
        Guid profissionalId,
        CancellationToken cancellationToken = default)
    {
        var eleitor = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .FirstOrDefaultAsync(e => e.EleicaoId == eleicaoId && e.ProfissionalId == profissionalId, cancellationToken);

        return eleitor == null ? null : MapEleitorToDto(eleitor);
    }

    public async Task<IEnumerable<EleitorDto>> GetEleitoresByEleicaoAsync(
        Guid eleicaoId,
        CancellationToken cancellationToken = default)
    {
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .Where(e => e.EleicaoId == eleicaoId)
            .OrderBy(e => e.Profissional!.Nome)
            .ToListAsync(cancellationToken);

        return eleitores.Select(MapEleitorToDto);
    }

    public async Task<IEnumerable<EleitorDto>> GetEleitoresAptosAsync(
        Guid eleicaoId,
        CancellationToken cancellationToken = default)
    {
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .Where(e => e.EleicaoId == eleicaoId && e.Apto)
            .OrderBy(e => e.Profissional!.Nome)
            .ToListAsync(cancellationToken);

        return eleitores.Select(MapEleitorToDto);
    }

    public async Task<IEnumerable<EleitorDto>> GetEleitoresQueVotaramListaAsync(
        Guid eleicaoId,
        CancellationToken cancellationToken = default)
    {
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .Where(e => e.EleicaoId == eleicaoId && e.Votou)
            .OrderBy(e => e.Profissional!.Nome)
            .ToListAsync(cancellationToken);

        return eleitores.Select(MapEleitorToDto);
    }

    public async Task<IEnumerable<EleitorDto>> GetEleitoresQueNaoVotaramAsync(
        Guid eleicaoId,
        CancellationToken cancellationToken = default)
    {
        var eleitores = await _eleitorRepository.Query()
            .Include(e => e.Profissional)
            .Include(e => e.Secao)
            .Where(e => e.EleicaoId == eleicaoId && e.Apto && !e.Votou)
            .OrderBy(e => e.Profissional!.Nome)
            .ToListAsync(cancellationToken);

        return eleitores.Select(MapEleitorToDto);
    }

    public async Task<EleitorDto> RegistrarEleitorAsync(
        Guid eleicaoId,
        Guid profissionalId,
        CancellationToken cancellationToken = default)
    {
        // Check if already registered
        var existente = await _eleitorRepository.AnyAsync(
            e => e.EleicaoId == eleicaoId && e.ProfissionalId == profissionalId, cancellationToken);

        if (existente)
        {
            throw new InvalidOperationException("Profissional ja cadastrado como eleitor nesta eleicao");
        }

        var eleitor = new Eleitor
        {
            EleicaoId = eleicaoId,
            ProfissionalId = profissionalId,
            Apto = true,
            Votou = false
        };

        await _eleitorRepository.AddAsync(eleitor, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Eleitor registrado: {EleitorId} na eleicao {EleicaoId}", eleitor.Id, eleicaoId);

        return await GetEleitorByIdAsync(eleitor.Id, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar eleitor");
    }

    public async Task<EleitorDto> AtualizarAptidaoEleitorAsync(
        Guid eleitorId,
        bool apto,
        string? motivo,
        CancellationToken cancellationToken = default)
    {
        var eleitor = await _eleitorRepository.GetByIdAsync(eleitorId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleitor {eleitorId} nao encontrado");

        eleitor.Apto = apto;
        eleitor.MotivoInaptidao = apto ? null : motivo;

        await _eleitorRepository.UpdateAsync(eleitor, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Aptidao do eleitor {EleitorId} atualizada: {Apto}", eleitorId, apto);

        return await GetEleitorByIdAsync(eleitorId, cancellationToken)
            ?? throw new InvalidOperationException("Erro ao recuperar eleitor");
    }

    #endregion

    #region Statistics

    public async Task<int> CountVotosAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _votoRepository.CountAsync(v => v.EleicaoId == eleicaoId, cancellationToken);
    }

    public async Task<int> CountVotosByChapaAsync(
        Guid eleicaoId,
        Guid chapaId,
        CancellationToken cancellationToken = default)
    {
        return await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.ChapaId == chapaId && v.Status == StatusVoto.Confirmado,
            cancellationToken);
    }

    public async Task<int> CountVotosByTipoAsync(
        Guid eleicaoId,
        TipoVoto tipo,
        CancellationToken cancellationToken = default)
    {
        return await _votoRepository.CountAsync(
            v => v.EleicaoId == eleicaoId && v.Tipo == tipo, cancellationToken);
    }

    public async Task<int> CountEleitoresAptosAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Apto, cancellationToken);
    }

    public async Task<int> CountEleitoresQueVotaramAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        return await _eleitorRepository.CountAsync(
            e => e.EleicaoId == eleicaoId && e.Votou, cancellationToken);
    }

    public async Task<double> GetPercentualComparecimentoAsync(
        Guid eleicaoId,
        CancellationToken cancellationToken = default)
    {
        var totalAptos = await CountEleitoresAptosAsync(eleicaoId, cancellationToken);
        var totalVotaram = await CountEleitoresQueVotaramAsync(eleicaoId, cancellationToken);

        return totalAptos > 0 ? Math.Round((double)totalVotaram / totalAptos * 100, 2) : 0;
    }

    #endregion

    #region Election Status Control

    public async Task<bool> EleicaoAbertaParaVotacaoAsync(
        Guid eleicaoId,
        CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken);
        if (eleicao == null) return false;

        if (eleicao.Status != StatusEleicao.EmAndamento) return false;
        if (eleicao.FaseAtual != FaseEleicao.Votacao) return false;

        var agora = DateTime.UtcNow;
        if (eleicao.DataVotacaoInicio.HasValue && agora < eleicao.DataVotacaoInicio.Value) return false;
        if (eleicao.DataVotacaoFim.HasValue && agora > eleicao.DataVotacaoFim.Value) return false;

        return true;
    }

    public async Task AbrirVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        if (eleicao.Status != StatusEleicao.EmAndamento && eleicao.Status != StatusEleicao.Agendada)
        {
            throw new InvalidOperationException($"Eleicao com status {eleicao.Status} nao pode ter votacao aberta");
        }

        eleicao.Status = StatusEleicao.EmAndamento;
        eleicao.FaseAtual = FaseEleicao.Votacao;
        eleicao.DataVotacaoInicio = DateTime.UtcNow;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Votacao aberta para eleicao {EleicaoId}", eleicaoId);
    }

    public async Task FecharVotacaoAsync(Guid eleicaoId, CancellationToken cancellationToken = default)
    {
        var eleicao = await _eleicaoRepository.GetByIdAsync(eleicaoId, cancellationToken)
            ?? throw new KeyNotFoundException($"Eleicao {eleicaoId} nao encontrada");

        if (eleicao.FaseAtual != FaseEleicao.Votacao)
        {
            throw new InvalidOperationException($"Eleicao em fase {eleicao.FaseAtual} nao pode ter votacao fechada");
        }

        eleicao.FaseAtual = FaseEleicao.Apuracao;
        eleicao.DataVotacaoFim = DateTime.UtcNow;

        await _eleicaoRepository.UpdateAsync(eleicao, cancellationToken);
        await _unitOfWork.SaveChangesAsync(cancellationToken);

        _logger.LogInformation("Votacao fechada para eleicao {EleicaoId}", eleicaoId);
    }

    #endregion

    #region Import/Export

    public async Task<int> ImportarEleitoresAsync(
        Guid eleicaoId,
        IEnumerable<Guid> profissionalIds,
        CancellationToken cancellationToken = default)
    {
        var count = 0;
        foreach (var profissionalId in profissionalIds)
        {
            var existe = await _eleitorRepository.AnyAsync(
                e => e.EleicaoId == eleicaoId && e.ProfissionalId == profissionalId, cancellationToken);

            if (!existe)
            {
                var eleitor = new Eleitor
                {
                    EleicaoId = eleicaoId,
                    ProfissionalId = profissionalId,
                    Apto = true,
                    Votou = false
                };
                await _eleitorRepository.AddAsync(eleitor, cancellationToken);
                count++;
            }
        }

        await _unitOfWork.SaveChangesAsync(cancellationToken);
        _logger.LogInformation("{Count} eleitores importados para eleicao {EleicaoId}", count, eleicaoId);

        return count;
    }

    public async Task<IEnumerable<EleitorDto>> ExportarEleitoresAsync(
        Guid eleicaoId,
        CancellationToken cancellationToken = default)
    {
        return await GetEleitoresByEleicaoAsync(eleicaoId, cancellationToken);
    }

    #endregion

    #region Private Helpers

    private static string GerarHashEleitor(Guid eleitorId, Guid eleicaoId)
    {
        var input = $"{eleitorId}:{eleicaoId}:{DateTime.UtcNow.Ticks}";
        using var sha256 = SHA256.Create();
        var bytes = sha256.ComputeHash(Encoding.UTF8.GetBytes(input));
        return Convert.ToBase64String(bytes);
    }

    private static string GerarHashVoto(Guid eleicaoId, Guid? chapaId, DateTime dataVoto)
    {
        var input = $"{eleicaoId}:{chapaId}:{dataVoto.Ticks}:{Guid.NewGuid()}";
        using var sha256 = SHA256.Create();
        var bytes = sha256.ComputeHash(Encoding.UTF8.GetBytes(input));
        return Convert.ToBase64String(bytes)[..20].ToUpper();
    }

    private static string GerarComprovante(string hashVoto)
    {
        return $"COMP-{DateTime.UtcNow:yyyyMMdd}-{hashVoto[..8]}";
    }

    private static EleitorDto MapEleitorToDto(Eleitor eleitor)
    {
        return new EleitorDto
        {
            Id = eleitor.Id,
            EleicaoId = eleitor.EleicaoId,
            ProfissionalId = eleitor.ProfissionalId,
            ProfissionalNome = eleitor.Profissional?.Nome ?? "",
            NumeroInscricao = eleitor.NumeroInscricao,
            Apto = eleitor.Apto,
            MotivoInaptidao = eleitor.MotivoInaptidao,
            Votou = eleitor.Votou,
            DataVoto = eleitor.DataVoto,
            SecaoId = eleitor.SecaoId,
            SecaoNome = eleitor.Secao?.Local ?? eleitor.Secao?.Numero
        };
    }

    private static string GetCargoDescricao(TipoMembroChapa tipo)
    {
        return tipo switch
        {
            TipoMembroChapa.Presidente => "Presidente",
            TipoMembroChapa.VicePresidente => "Vice-Presidente",
            TipoMembroChapa.PrimeiroSecretario => "1o Secretario",
            TipoMembroChapa.SegundoSecretario => "2o Secretario",
            TipoMembroChapa.PrimeiroTesoureiro => "1o Tesoureiro",
            TipoMembroChapa.SegundoTesoureiro => "2o Tesoureiro",
            TipoMembroChapa.ConselheiroTitular => "Conselheiro Titular",
            TipoMembroChapa.ConselheiroSuplente => "Conselheiro Suplente",
            TipoMembroChapa.Delegado => "Delegado",
            TipoMembroChapa.DelegadoSuplente => "Delegado Suplente",
            _ => tipo.ToString()
        };
    }

    #endregion
}
