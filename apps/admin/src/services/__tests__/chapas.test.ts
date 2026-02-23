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

describe('Admin Chapas Service', () => {
    beforeEach(() => { vi.clearAllMocks() })

    describe('getAll', () => {
        it('deve listar chapas com paginação', async () => {
            const mockResponse = { items: [{ id: '1', nome: 'Chapa 1', numero: 1, status: 0 }], totalCount: 1, page: 1, pageSize: 10, totalPages: 1 }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockResponse })

            const { chapasService } = await import('@/services/chapas')
            const result = await chapasService.getAll({ page: 1, pageSize: 10 })

            expect(api.get).toHaveBeenCalledWith('/chapa', { params: { page: 1, pageSize: 10 } })
            expect(result.data).toHaveLength(1)
            expect(result.total).toBe(1)
        })

        it('deve filtrar chapas por eleição', async () => {
            const mockResponse = { items: [], totalCount: 0, page: 1, pageSize: 10, totalPages: 0 }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockResponse })

            const { chapasService } = await import('@/services/chapas')
            await chapasService.getAll({ eleicaoId: 'e1' })

            expect(api.get).toHaveBeenCalledWith('/chapa', { params: { eleicaoId: 'e1' } })
        })
    })

    describe('getById', () => {
        it('deve retornar detalhes da chapa com membros', async () => {
            const mockChapa = { id: '1', nome: 'Chapa Renovação', numero: 1, membros: [{ id: 'm1', candidatoNome: 'João' }] }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockChapa })

            const { chapasService } = await import('@/services/chapas')
            const result = await chapasService.getById('1')

            expect(api.get).toHaveBeenCalledWith('/chapa/1')
            expect(result.nome).toBe('Chapa Renovação')
            expect(result.membros).toHaveLength(1)
        })
    })

    describe('getByEleicao', () => {
        it('deve listar chapas de uma eleição', async () => {
            vi.mocked(api.get).mockResolvedValueOnce({ data: [{ id: '1' }, { id: '2' }] })

            const { chapasService } = await import('@/services/chapas')
            const result = await chapasService.getByEleicao('e1')

            expect(api.get).toHaveBeenCalledWith('/chapa/eleicao/e1')
            expect(result).toHaveLength(2)
        })
    })

    describe('create', () => {
        it('deve criar uma nova chapa', async () => {
            const novaChapa = { eleicaoId: 'e1', numero: 3, nome: 'Chapa Nova' }
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: 'c3', ...novaChapa, status: 0 } })

            const { chapasService } = await import('@/services/chapas')
            const result = await chapasService.create(novaChapa as any)

            expect(api.post).toHaveBeenCalledWith('/chapa', novaChapa)
            expect(result.id).toBe('c3')
        })
    })

    describe('update', () => {
        it('deve atualizar uma chapa', async () => {
            const updateData = { nome: 'Chapa Atualizada' }
            vi.mocked(api.put).mockResolvedValueOnce({ data: { id: '1', ...updateData } })

            const { chapasService } = await import('@/services/chapas')
            const result = await chapasService.update('1', updateData)

            expect(api.put).toHaveBeenCalledWith('/chapa/1', updateData)
            expect(result.nome).toBe('Chapa Atualizada')
        })
    })

    describe('delete', () => {
        it('deve excluir uma chapa', async () => {
            vi.mocked(api.delete).mockResolvedValueOnce({ data: undefined })

            const { chapasService } = await import('@/services/chapas')
            await chapasService.delete('1')

            expect(api.delete).toHaveBeenCalledWith('/chapa/1')
        })
    })

    describe('Status Operations', () => {
        it('deve aprovar uma chapa', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: '1', status: 2 } })

            const { chapasService } = await import('@/services/chapas')
            const result = await chapasService.aprovar('1')

            expect(api.post).toHaveBeenCalledWith('/chapa/1/aprovar')
            expect(result.status).toBe(2)
        })

        it('deve reprovar uma chapa com motivo', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: '1', status: 3 } })

            const { chapasService } = await import('@/services/chapas')
            await chapasService.reprovar('1', 'Documentação incompleta')

            expect(api.post).toHaveBeenCalledWith('/chapa/1/reprovar', { motivo: 'Documentação incompleta' })
        })

        it('deve suspender uma chapa', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: '1', status: 5 } })

            const { chapasService } = await import('@/services/chapas')
            await chapasService.suspender('1', 'Irregularidade')

            expect(api.post).toHaveBeenCalledWith('/chapa/1/suspender', { motivo: 'Irregularidade' })
        })

        it('deve reativar uma chapa', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: '1', status: 0 } })

            const { chapasService } = await import('@/services/chapas')
            await chapasService.reativar('1')

            expect(api.post).toHaveBeenCalledWith('/chapa/1/reativar')
        })

        it('deve cancelar uma chapa', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: '1', status: 6 } })

            const { chapasService } = await import('@/services/chapas')
            await chapasService.cancelar('1', 'Solicitação do candidato')

            expect(api.post).toHaveBeenCalledWith('/chapa/1/cancelar', { motivo: 'Solicitação do candidato' })
        })
    })

    describe('Membros Operations', () => {
        it('deve listar membros de uma chapa', async () => {
            vi.mocked(api.get).mockResolvedValueOnce({ data: [{ id: 'm1', candidatoNome: 'João' }] })

            const { chapasService } = await import('@/services/chapas')
            const result = await chapasService.getMembros('c1')

            expect(api.get).toHaveBeenCalledWith('/chapa/c1/membros')
            expect(result).toHaveLength(1)
        })

        it('deve adicionar membro à chapa', async () => {
            const membroData = { candidatoId: 'p1', cargo: 0, tipo: 0, ordem: 1 }
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: 'm2', ...membroData } })

            const { chapasService } = await import('@/services/chapas')
            const result = await chapasService.addMembro('c1', membroData)

            expect(api.post).toHaveBeenCalledWith('/chapa/c1/membros', membroData)
            expect(result.id).toBe('m2')
        })

        it('deve remover membro da chapa', async () => {
            vi.mocked(api.delete).mockResolvedValueOnce({ data: undefined })

            const { chapasService } = await import('@/services/chapas')
            await chapasService.removeMembro('c1', 'm1')

            expect(api.delete).toHaveBeenCalledWith('/chapa/c1/membros/m1')
        })
    })

    describe('Documentos Operations', () => {
        it('deve listar documentos da chapa', async () => {
            vi.mocked(api.get).mockResolvedValueOnce({ data: [{ id: 'd1', nome: 'Ata' }] })

            const { chapasService } = await import('@/services/chapas')
            const result = await chapasService.getDocumentos('c1')

            expect(api.get).toHaveBeenCalledWith('/chapa/c1/documentos')
            expect(result).toHaveLength(1)
        })

        it('deve remover documento da chapa', async () => {
            vi.mocked(api.delete).mockResolvedValueOnce({ data: undefined })

            const { chapasService } = await import('@/services/chapas')
            await chapasService.removeDocumento('c1', 'd1')

            expect(api.delete).toHaveBeenCalledWith('/chapa/c1/documentos/d1')
        })
    })

    describe('Estatísticas', () => {
        it('deve retornar estatísticas de chapas por eleição', async () => {
            const mockStats = { total: 5, pendentes: 2, aprovadas: 2, reprovadas: 1, impugnadas: 0 }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockStats })

            const { chapasService } = await import('@/services/chapas')
            const result = await chapasService.getEstatisticas('e1')

            expect(api.get).toHaveBeenCalledWith('/chapa/estatisticas/e1')
            expect(result.total).toBe(5)
            expect(result.aprovadas).toBe(2)
        })
    })
})
