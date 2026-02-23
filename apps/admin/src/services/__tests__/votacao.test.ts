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

const api = (await import('@/services/api')).default

describe('Admin Votacao Service', () => {
    beforeEach(() => { vi.clearAllMocks() })

    describe('getAll', () => {
        it('deve listar eleições com status de votação', async () => {
            const mockData = [
                { id: '1', nome: 'Eleição 2026', ano: 2026, status: 2, statusVotacao: 'em_andamento', totalEleitores: 1000, totalVotos: 500, participacao: 50, votosBrancos: 10, votosNulos: 5 },
            ]
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockData })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.getAll()

            expect(api.get).toHaveBeenCalledWith('/votacao')
            expect(result).toHaveLength(1)
            expect(result[0].statusVotacao).toBe('em_andamento')
            expect(result[0].participacao).toBe(50)
        })
    })

    describe('getEstatisticas', () => {
        it('deve retornar estatísticas de votação de uma eleição', async () => {
            const mockStats = {
                eleicaoId: '1', eleicaoNome: 'Eleição 2026', totalEleitores: 1000,
                totalVotos: 500, participacao: 50, votosBrancos: 10, votosNulos: 5,
                votosValidos: 485, status: 'em_andamento',
                votosPorRegiao: [{ uf: 'SP', regional: 'São Paulo', totalEleitores: 200, totalVotos: 100, participacao: 50 }],
                votosPorHora: [{ hora: '08:00', quantidade: 50 }],
            }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockStats })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.getEstatisticas('1')

            expect(api.get).toHaveBeenCalledWith('/votacao/estatisticas/1')
            expect(result.totalVotos).toBe(500)
            expect(result.votosPorRegiao).toHaveLength(1)
        })
    })

    describe('iniciarVotacao', () => {
        it('deve abrir votação para uma eleição', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: undefined })

            const { votacaoService } = await import('@/services/votacao')
            await votacaoService.iniciarVotacao('1')

            expect(api.post).toHaveBeenCalledWith('/votacao/abrir/1')
        })
    })

    describe('encerrarVotacao', () => {
        it('deve encerrar votação de uma eleição', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: undefined })

            const { votacaoService } = await import('@/services/votacao')
            await votacaoService.encerrarVotacao('1')

            expect(api.post).toHaveBeenCalledWith('/votacao/fechar/1')
        })
    })

    describe('getResultados', () => {
        it('deve retornar resultados da apuração', async () => {
            const mockResult = {
                eleicaoId: '1', eleicaoNome: 'Eleição 2026', status: 'finalizada',
                totalEleitores: 1000, totalVotos: 500, votosValidos: 485, votosBrancos: 10,
                votosNulos: 5, participacao: 50, percentualApurado: 100,
                chapas: [{ id: 'c1', numero: 1, nome: 'Chapa 1', votos: 300, percentual: 61.85, posicao: 1, eleita: true }],
                vencedora: { id: 'c1', numero: 1, nome: 'Chapa 1', votos: 300, percentual: 61.85, posicao: 1, eleita: true },
                ultimaAtualizacao: '2026-02-23T10:00:00Z',
            }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockResult })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.getResultados('1')

            expect(api.get).toHaveBeenCalledWith('/apuracao/1')
            expect(result.vencedora?.nome).toBe('Chapa 1')
            expect(result.percentualApurado).toBe(100)
        })
    })

    describe('apurar', () => {
        it('deve realizar apuração de votos', async () => {
            const mockResult = {
                eleicaoId: '1', eleicaoNome: 'Eleição 2026', status: 'em_andamento',
                totalEleitores: 1000, totalVotos: 500, votosValidos: 485,
                votosBrancos: 10, votosNulos: 5, participacao: 50, percentualApurado: 50,
                chapas: [], ultimaAtualizacao: '2026-02-23T10:00:00Z',
            }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockResult })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.apurar('1')

            expect(api.post).toHaveBeenCalledWith('/apuracao/1/apurar')
            expect(result.status).toBe('em_andamento')
        })
    })

    describe('publicarResultados', () => {
        it('deve publicar resultados da eleição', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: undefined })

            const { votacaoService } = await import('@/services/votacao')
            await votacaoService.publicarResultados('1')

            expect(api.post).toHaveBeenCalledWith('/apuracao/1/publicar')
        })
    })

    describe('exportarResultados', () => {
        it('deve exportar resultados em PDF', async () => {
            const mockBlob = new Blob(['pdf-content'], { type: 'application/pdf' })
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockBlob })

            const { votacaoService } = await import('@/services/votacao')
            const result = await votacaoService.exportarResultados('1', 'pdf')

            expect(api.get).toHaveBeenCalledWith('/apuracao/1/exportar', {
                params: { formato: 'pdf' },
                responseType: 'blob',
            })
            expect(result).toBeInstanceOf(Blob)
        })

        it('deve exportar resultados em Excel', async () => {
            const mockBlob = new Blob(['excel-content'])
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockBlob })

            const { votacaoService } = await import('@/services/votacao')
            await votacaoService.exportarResultados('1', 'excel')

            expect(api.get).toHaveBeenCalledWith('/apuracao/1/exportar', {
                params: { formato: 'excel' },
                responseType: 'blob',
            })
        })

        it('deve exportar resultados em CSV', async () => {
            const mockBlob = new Blob(['csv-content'])
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockBlob })

            const { votacaoService } = await import('@/services/votacao')
            await votacaoService.exportarResultados('1', 'csv')

            expect(api.get).toHaveBeenCalledWith('/apuracao/1/exportar', {
                params: { formato: 'csv' },
                responseType: 'blob',
            })
        })
    })
})
