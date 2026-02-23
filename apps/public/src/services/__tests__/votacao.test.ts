import { describe, it, expect, vi, beforeEach } from 'vitest'

vi.mock('axios', () => {
    const mockAxios = {
        create: vi.fn(() => mockAxios),
        get: vi.fn(), post: vi.fn(), put: vi.fn(), delete: vi.fn(), patch: vi.fn(),
        interceptors: { request: { use: vi.fn() }, response: { use: vi.fn() } },
        defaults: { headers: { common: {} } },
    }
    return { default: mockAxios, __esModule: true }
})

vi.mock('@/services/api', async () => {
    const actual = await vi.importActual('@/services/api')
    return {
        ...actual as any,
        default: (await import('axios')).default,
        setToken: vi.fn(),
        setTokenType: vi.fn(),
    }
})

const api = (await import('@/services/api')).default

describe('Public Votacao Service', () => {
    beforeEach(() => { vi.clearAllMocks() })

    describe('getStatus', () => {
        it('deve retornar status da votação', async () => {
            const mockStatus = { eleicaoId: 'e1', votacaoAberta: true, inicioVotacao: '2026-02-23T08:00:00Z', fimVotacao: '2026-02-23T17:00:00Z', horarioServidor: '2026-02-23T10:00:00Z', tempoRestante: 25200 }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockStatus })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.getStatus('e1')

            expect(api.get).toHaveBeenCalledWith('/votacao/status/e1')
            expect(result.votacaoAberta).toBe(true)
            expect(result.tempoRestante).toBe(25200)
        })
    })

    describe('verificarVoto', () => {
        it('deve verificar se eleitor já votou', async () => {
            vi.mocked(api.get).mockResolvedValueOnce({ data: { jaVotou: false, comprovanteDisponivel: false } })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.verificarVoto('e1')

            expect(api.get).toHaveBeenCalledWith('/votacao/e1/verificar')
            expect(result.jaVotou).toBe(false)
        })

        it('deve retornar true se já votou', async () => {
            vi.mocked(api.get).mockResolvedValueOnce({ data: { jaVotou: true, dataVoto: '2026-02-23T09:00:00Z', comprovanteDisponivel: true } })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.verificarVoto('e1')

            expect(result.jaVotou).toBe(true)
            expect(result.comprovanteDisponivel).toBe(true)
        })
    })

    describe('iniciarVotacao', () => {
        it('deve obter cédula de votação', async () => {
            const mockCedula = {
                eleicaoId: 'e1', eleicaoNome: 'Eleição 2026', eleitorId: 'v1', eleitorNome: 'João',
                chapas: [{ id: 'c1', numero: 1, nome: 'Chapa 1', presidente: 'Maria' }],
                instrucoes: ['Selecione uma chapa'], tempoMaximoMinutos: 5, iniciadaEm: '2026-02-23T10:00:00Z', expiraEm: '2026-02-23T10:05:00Z',
            }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockCedula })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.iniciarVotacao('e1')

            expect(api.post).toHaveBeenCalledWith('/votacao/e1/iniciar')
            expect(result.chapas).toHaveLength(1)
            expect(result.tempoMaximoMinutos).toBe(5)
        })
    })

    describe('votar', () => {
        it('deve registrar voto em chapa e retornar comprovante', async () => {
            const mockComprovante = { id: 'v1', protocolo: 'PROT-001', eleicaoId: 'e1', eleicaoNome: 'Eleição 2026', dataHoraVoto: '2026-02-23T10:01:00Z', hashComprovante: 'abc123', mensagem: 'Voto registrado' }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockComprovante })

            const { votacaoService, TipoVoto } = await import('@/services/votacao')
            const result = await votacaoService.votar({ eleicaoId: 'e1', chapaId: 'c1', tipoVoto: TipoVoto.CHAPA })

            expect(api.post).toHaveBeenCalledWith('/votacao/votar', { eleicaoId: 'e1', chapaId: 'c1', tipoVoto: 0 })
            expect(result.protocolo).toBe('PROT-001')
            expect(result.hashComprovante).toBeTruthy()
        })
    })

    describe('votarChapa', () => {
        it('deve votar em chapa usando método de conveniência', async () => {
            const mockComprovante = { id: 'v1', protocolo: 'PROT-002', eleicaoId: 'e1', eleicaoNome: 'Eleição 2026', dataHoraVoto: '2026-02-23T10:01:00Z', hashComprovante: 'def456', mensagem: 'Voto registrado' }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockComprovante })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.votarChapa('e1', 'c1')

            expect(result.protocolo).toBe('PROT-002')
        })
    })

    describe('votarBranco', () => {
        it('deve registrar voto em branco', async () => {
            const mockComprovante = { id: 'v2', protocolo: 'PROT-003', eleicaoId: 'e1', eleicaoNome: 'Eleição 2026', dataHoraVoto: '2026-02-23T10:01:00Z', hashComprovante: 'ghi789', mensagem: 'Voto em branco registrado' }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockComprovante })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.votarBranco('e1')

            expect(api.post).toHaveBeenCalledWith('/votacao/votar', { eleicaoId: 'e1', tipoVoto: 1 })
            expect(result.mensagem).toContain('branco')
        })
    })

    describe('votarNulo', () => {
        it('deve registrar voto nulo', async () => {
            const mockComprovante = { id: 'v3', protocolo: 'PROT-004', eleicaoId: 'e1', eleicaoNome: 'Eleição 2026', dataHoraVoto: '2026-02-23T10:01:00Z', hashComprovante: 'jkl012', mensagem: 'Voto nulo registrado' }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockComprovante })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.votarNulo('e1')

            expect(api.post).toHaveBeenCalledWith('/votacao/votar', { eleicaoId: 'e1', tipoVoto: 2 })
            expect(result.protocolo).toBe('PROT-004')
        })
    })

    describe('getComprovante', () => {
        it('deve obter comprovante de voto', async () => {
            const mockComprovante = { id: 'v1', protocolo: 'PROT-001', eleicaoId: 'e1', eleicaoNome: 'Eleição 2026', dataHoraVoto: '2026-02-23T10:01:00Z', hashComprovante: 'abc123', qrCode: 'data:image/png;base64,...', mensagem: 'OK' }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockComprovante })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.getComprovante('e1')

            expect(api.get).toHaveBeenCalledWith('/votacao/comprovante/e1')
            expect(result.qrCode).toBeTruthy()
        })
    })

    describe('getHistoricoVotos', () => {
        it('deve retornar histórico de votos', async () => {
            const mockHistorico = [
                { eleicaoId: 'e1', eleicaoNome: 'Eleição 2026', dataVoto: '2026-02-23T10:01:00Z', protocolo: 'PROT-001' },
                { eleicaoId: 'e2', eleicaoNome: 'Eleição 2024', dataVoto: '2024-10-15T09:30:00Z', protocolo: 'PROT-099' },
            ]
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockHistorico })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.getHistoricoVotos()

            expect(api.get).toHaveBeenCalledWith('/votacao/historico')
            expect(result).toHaveLength(2)
        })
    })

    describe('verificarElegibilidade', () => {
        it('deve verificar elegibilidade com eleicaoId', async () => {
            vi.mocked(api.get).mockResolvedValueOnce({ data: { elegivel: true, jaVotou: false, eleicaoId: 'e1', eleicaoNome: 'Eleição 2026' } })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.verificarElegibilidade('e1')

            expect(api.get).toHaveBeenCalledWith('/votacao/elegibilidade/e1')
            expect(result.elegivel).toBe(true)
        })

        it('deve verificar elegibilidade sem eleicaoId', async () => {
            vi.mocked(api.get).mockResolvedValueOnce({ data: { elegivel: true, jaVotou: false } })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.verificarElegibilidade()

            expect(api.get).toHaveBeenCalledWith('/votacao/elegibilidade')
            expect(result.elegivel).toBe(true)
        })

        it('deve retornar não elegível com motivo', async () => {
            vi.mocked(api.get).mockResolvedValueOnce({ data: { elegivel: false, jaVotou: true, motivo: 'Já votou nesta eleição', comprovante: { protocolo: 'PROT-001' } } })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.verificarElegibilidade('e1')

            expect(result.elegivel).toBe(false)
            expect(result.motivo).toBe('Já votou nesta eleição')
        })
    })

    describe('getChapasVotacao', () => {
        it('deve listar chapas disponíveis para votação', async () => {
            const mockChapas = [
                { id: 'c1', numero: 1, nome: 'Chapa Renovação', presidente: 'Maria' },
                { id: 'c2', numero: 2, nome: 'Chapa Progresso', presidente: 'Pedro' },
            ]
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockChapas })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.getChapasVotacao('e1')

            expect(api.get).toHaveBeenCalledWith('/votacao/eleicao/e1/chapas')
            expect(result).toHaveLength(2)
        })
    })

    describe('registrarVoto', () => {
        it('deve registrar voto com tipo string convertido', async () => {
            const mockComprovante = { id: 'v1', protocolo: 'PROT-005', eleicaoId: 'e1', eleicaoNome: 'Eleição 2026', dataHoraVoto: '2026-02-23T10:01:00Z', hashComprovante: 'xyz', mensagem: 'OK' }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockComprovante })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.registrarVoto({ eleicaoId: 'e1', chapaId: 'c1', tipoVoto: 'chapa' })

            expect(api.post).toHaveBeenCalledWith('/votacao/votar', { eleicaoId: 'e1', chapaId: 'c1', tipoVoto: 0 })
            expect(result.protocolo).toBe('PROT-005')
        })

        it('deve registrar voto branco com tipo string', async () => {
            const mockComprovante = { id: 'v2', protocolo: 'PROT-006', eleicaoId: 'e1', eleicaoNome: 'Eleição 2026', dataHoraVoto: '2026-02-23T10:01:00Z', hashComprovante: 'xyz2', mensagem: 'OK' }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockComprovante })

            const { votacaoService } = await import('@/services/votacao')
            await votacaoService.registrarVoto({ eleicaoId: 'e1', tipoVoto: 'branco' })

            expect(api.post).toHaveBeenCalledWith('/votacao/votar', { eleicaoId: 'e1', chapaId: undefined, tipoVoto: 1 })
        })
    })

    describe('cancelarSessao', () => {
        it('deve cancelar sessão de votação', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: undefined })

            const { votacaoService } = await import('@/services/votacao')
            await votacaoService.cancelarSessao('e1')

            expect(api.post).toHaveBeenCalledWith('/votacao/e1/cancelar-sessao')
        })
    })

    describe('validarComprovante', () => {
        it('deve validar comprovante de voto', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { valido: true, dataVoto: '2026-02-23T10:01:00Z', eleicaoNome: 'Eleição 2026', mensagem: 'Comprovante válido' } })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.validarComprovante('PROT-001', 'abc123')

            expect(api.post).toHaveBeenCalledWith('/votacao/validar-comprovante', { protocolo: 'PROT-001', hash: 'abc123' })
            expect(result.valido).toBe(true)
        })
    })
})
