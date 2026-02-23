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

describe('Admin Eleicoes Service', () => {
    beforeEach(() => { vi.clearAllMocks() })

    describe('getAll', () => {
        it('deve listar todas as eleições', async () => {
            const mockData = [
                { id: '1', nome: 'Eleição CAU 2026', ano: 2026, status: 0 },
                { id: '2', nome: 'Eleição CAU 2024', ano: 2024, status: 3 },
            ]
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockData })

            const { eleicoesService } = await import('@/services/eleicoes')
            const result = await eleicoesService.getAll()

            expect(api.get).toHaveBeenCalledWith('/eleicao')
            expect(result).toHaveLength(2)
        })
    })

    describe('getById', () => {
        it('deve retornar detalhes de uma eleição', async () => {
            const mockEleicao = { id: '1', nome: 'Eleição CAU 2026', ano: 2026, status: 0 }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockEleicao })

            const { eleicoesService } = await import('@/services/eleicoes')
            const result = await eleicoesService.getById('1')

            expect(api.get).toHaveBeenCalledWith('/eleicao/1')
            expect(result.nome).toBe('Eleição CAU 2026')
        })

        it('deve rejeitar para eleição inexistente', async () => {
            vi.mocked(api.get).mockRejectedValueOnce(new Error('Not Found'))

            const { eleicoesService } = await import('@/services/eleicoes')
            await expect(eleicoesService.getById('999')).rejects.toThrow('Not Found')
        })
    })

    describe('getByStatus', () => {
        it('deve filtrar eleições por status', async () => {
            vi.mocked(api.get).mockResolvedValueOnce({ data: [{ id: '1', status: 2 }] })

            const { eleicoesService } = await import('@/services/eleicoes')
            const result = await eleicoesService.getByStatus(2)

            expect(api.get).toHaveBeenCalledWith('/eleicao/status/2')
            expect(result).toHaveLength(1)
        })
    })

    describe('getAtivas', () => {
        it('deve retornar apenas eleições ativas', async () => {
            vi.mocked(api.get).mockResolvedValueOnce({ data: [{ id: '1', status: 1 }] })

            const { eleicoesService } = await import('@/services/eleicoes')
            const result = await eleicoesService.getAtivas()

            expect(api.get).toHaveBeenCalledWith('/eleicao/ativas')
            expect(result).toHaveLength(1)
        })
    })

    describe('create', () => {
        it('deve criar uma nova eleição', async () => {
            const novaEleicao = { nome: 'Eleição 2027', ano: 2027, tipo: 0, dataInicio: '2027-01-01', dataFim: '2027-12-31', modoVotacao: 0 }
            const mockResponse = { id: '3', ...novaEleicao, status: 0 }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockResponse })

            const { eleicoesService } = await import('@/services/eleicoes')
            const result = await eleicoesService.create(novaEleicao as any)

            expect(api.post).toHaveBeenCalledWith('/eleicao', novaEleicao)
            expect(result.id).toBe('3')
        })
    })

    describe('update', () => {
        it('deve atualizar uma eleição existente', async () => {
            const updateData = { nome: 'Eleição CAU 2026 - Atualizada' }
            const mockResponse = { id: '1', ...updateData, status: 0 }
            vi.mocked(api.put).mockResolvedValueOnce({ data: mockResponse })

            const { eleicoesService } = await import('@/services/eleicoes')
            const result = await eleicoesService.update('1', updateData)

            expect(api.put).toHaveBeenCalledWith('/eleicao/1', updateData)
            expect(result.nome).toBe('Eleição CAU 2026 - Atualizada')
        })
    })

    describe('delete', () => {
        it('deve excluir uma eleição', async () => {
            vi.mocked(api.delete).mockResolvedValueOnce({ data: undefined })

            const { eleicoesService } = await import('@/services/eleicoes')
            await eleicoesService.delete('1')

            expect(api.delete).toHaveBeenCalledWith('/eleicao/1')
        })
    })

    describe('iniciar', () => {
        it('deve iniciar uma eleição', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: '1', status: 1 } })

            const { eleicoesService } = await import('@/services/eleicoes')
            const result = await eleicoesService.iniciar('1')

            expect(api.post).toHaveBeenCalledWith('/eleicao/1/iniciar')
            expect(result.status).toBe(1)
        })
    })

    describe('encerrar', () => {
        it('deve encerrar uma eleição', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: '1', status: 3 } })

            const { eleicoesService } = await import('@/services/eleicoes')
            const result = await eleicoesService.encerrar('1')

            expect(api.post).toHaveBeenCalledWith('/eleicao/1/encerrar')
            expect(result.status).toBe(3)
        })
    })

    describe('suspender', () => {
        it('deve suspender uma eleição com motivo', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: '1', status: 4 } })

            const { eleicoesService } = await import('@/services/eleicoes')
            await eleicoesService.suspender('1', 'Problema técnico')

            expect(api.post).toHaveBeenCalledWith('/eleicao/1/suspender', { motivo: 'Problema técnico' })
        })
    })

    describe('cancelar', () => {
        it('deve cancelar uma eleição com motivo', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: '1', status: 5 } })

            const { eleicoesService } = await import('@/services/eleicoes')
            await eleicoesService.cancelar('1', 'Cancelamento judicial')

            expect(api.post).toHaveBeenCalledWith('/eleicao/1/cancelar', { motivo: 'Cancelamento judicial' })
        })
    })
})
