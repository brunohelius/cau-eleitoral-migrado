import { beforeEach, describe, expect, it, vi } from 'vitest'

vi.mock('axios', () => {
  const mockAxios = {
    create: vi.fn(() => mockAxios),
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    patch: vi.fn(),
    interceptors: { request: { use: vi.fn() }, response: { use: vi.fn() } },
    defaults: { headers: { common: {} } },
  }
  return { default: mockAxios, __esModule: true }
})

const api = (await import('@/services/api')).default

describe('Admin Denuncias Service', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('CT-DEN-001 deve listar denuncias com filtros', async () => {
    vi.mocked(api.get).mockResolvedValueOnce({
      data: { items: [{ id: 'd1', protocolo: 'DEN-2026/0001' }], totalCount: 1, page: 1, pageSize: 10, totalPages: 1 },
    })

    const { denunciasService } = await import('@/services/denuncias')
    const result = await denunciasService.getAll({ status: 0 as any, eleicaoId: 'e1', page: 1, pageSize: 10 })

    expect(api.get).toHaveBeenCalledWith('/denuncia', {
      params: { status: 0, eleicaoId: 'e1', page: 1, pageSize: 10 },
    })
    expect(result.total).toBe(1)
    expect(result.data[0].id).toBe('d1')
  })

  it('CT-DEN-002 deve criar nova denuncia', async () => {
    vi.mocked(api.post).mockResolvedValueOnce({
      data: { id: 'd2', protocolo: 'DEN-2026/0002', titulo: 'Nova denúncia' },
    })

    const { denunciasService, TipoDenuncia } = await import('@/services/denuncias')
    const payload = {
      eleicaoId: 'e1',
      tipo: TipoDenuncia.ABUSO_PODER,
      titulo: 'Nova denúncia',
      descricao: 'Descrição com detalhes do fato denunciado.',
    }
    const created = await denunciasService.create(payload)

    expect(api.post).toHaveBeenCalledWith('/denuncia', payload)
    expect(created.id).toBe('d2')
  })

  it('CT-DEN-003 deve consultar denuncia por protocolo', async () => {
    vi.mocked(api.get).mockResolvedValueOnce({ data: { id: 'd3', protocolo: 'DEN-2026/0999' } })

    const { denunciasService } = await import('@/services/denuncias')
    const result = await denunciasService.getByProtocolo('DEN-2026/0999')

    expect(api.get).toHaveBeenCalledWith('/denuncia/protocolo/DEN-2026/0999')
    expect(result.protocolo).toBe('DEN-2026/0999')
  })

  it('CT-DEN-004 deve visualizar detalhes da denuncia', async () => {
    vi.mocked(api.get).mockResolvedValueOnce({ data: { id: 'd4', titulo: 'Detalhe da denúncia' } })

    const { denunciasService } = await import('@/services/denuncias')
    const result = await denunciasService.getById('d4')

    expect(api.get).toHaveBeenCalledWith('/denuncia/d4')
    expect(result.id).toBe('d4')
  })

  it('CT-DEN-005 deve iniciar analise da denuncia', async () => {
    vi.mocked(api.post).mockResolvedValueOnce({ data: { id: 'd5', status: 1 } })

    const { denunciasService } = await import('@/services/denuncias')
    const result = await denunciasService.iniciarAnalise('d5')

    expect(api.post).toHaveBeenCalledWith('/denuncia/d5/analisar', {})
    expect(result.status).toBe(1)
  })

  it('CT-DEN-006 deve concluir analise com parecer', async () => {
    vi.mocked(api.post).mockResolvedValueOnce({ data: { id: 'd6', status: 2 } })
    vi.mocked(api.get).mockResolvedValueOnce({ data: { id: 'd6', status: 2 } })

    const { denunciasService, StatusDenuncia } = await import('@/services/denuncias')
    const result = await denunciasService.emitirParecer('d6', {
      parecer: 'Analise concluida com recomendacao.',
      decisao: StatusDenuncia.ADMISSIBILIDADE_ACEITA,
    })

    expect(api.post).toHaveBeenCalledWith('/denuncia/d6/concluir-analise', {
      parecer: 'Analise concluida com recomendacao.',
      recomendacao: true,
    })
    expect(api.get).toHaveBeenCalledWith('/denuncia/d6')
    expect(result.id).toBe('d6')
  })

  it('CT-DEN-007 deve aceitar admissibilidade', async () => {
    vi.mocked(api.post).mockResolvedValueOnce({ data: { id: 'd7', status: 2 } })

    const { denunciasService } = await import('@/services/denuncias')
    const result = await denunciasService.aceitarAdmissibilidade('d7', 'Requisitos atendidos')

    expect(api.post).toHaveBeenCalledWith('/denuncia/d7/aceitar-admissibilidade', {
      parecer: 'Requisitos atendidos',
    })
    expect(result.id).toBe('d7')
  })

  it('CT-DEN-008 deve rejeitar admissibilidade', async () => {
    vi.mocked(api.post).mockResolvedValueOnce({ data: { id: 'd8', status: 3 } })

    const { denunciasService } = await import('@/services/denuncias')
    const result = await denunciasService.rejeitarAdmissibilidade('d8', 'Falta de provas')

    expect(api.post).toHaveBeenCalledWith('/denuncia/d8/rejeitar-admissibilidade', {
      parecer: 'Falta de provas',
    })
    expect(result.id).toBe('d8')
  })

  it('CT-DEN-009 deve enviar para julgamento e registrar decisao', async () => {
    vi.mocked(api.post).mockResolvedValueOnce({ data: { id: 'd9', status: 7 } })

    const { denunciasService, StatusDenuncia } = await import('@/services/denuncias')
    const result = await denunciasService.julgar('d9', {
      decisao: StatusDenuncia.PROCEDENTE,
      fundamentacao: 'Encaminhada para julgamento e decidida.',
    })

    expect(api.post).toHaveBeenCalledWith('/denuncia/d9/julgar', {
      resultado: StatusDenuncia.PROCEDENTE,
      decisao: 'Encaminhada para julgamento e decidida.',
      fundamentacao: 'Encaminhada para julgamento e decidida.',
    })
    expect(result.id).toBe('d9')
  })

  it('CT-DEN-010 deve listar denuncias por eleicao', async () => {
    vi.mocked(api.get).mockResolvedValueOnce({
      data: { items: [{ id: 'd10' }], totalCount: 1, page: 1, pageSize: 10, totalPages: 1 },
    })

    const { denunciasService } = await import('@/services/denuncias')
    const result = await denunciasService.getByEleicao('e10')

    expect(api.get).toHaveBeenCalledWith('/denuncia/eleicao/e10', { params: undefined })
    expect(result.data).toHaveLength(1)
  })

  it('CT-DEN-011 deve listar denuncias por chapa', async () => {
    vi.mocked(api.get).mockResolvedValueOnce({ data: [{ id: 'd11' }] })

    const { denunciasService } = await import('@/services/denuncias')
    const result = await denunciasService.getByChapa('c11')

    expect(api.get).toHaveBeenCalledWith('/denuncia/chapa/c11')
    expect(result).toHaveLength(1)
  })

  it('CT-DEN-012 deve listar minhas denuncias como denunciante', async () => {
    vi.mocked(api.get).mockResolvedValueOnce({ data: [{ id: 'd12', denuncianteNome: 'Usuário Atual' }] })

    const { denunciasService } = await import('@/services/denuncias')
    const result = await denunciasService.getMinhas()

    expect(api.get).toHaveBeenCalledWith('/denuncia/minhas')
    expect(result[0].id).toBe('d12')
  })
})
