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

describe('Admin Impugnacoes Service', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('CT-IMP-001 deve listar impugnacoes', async () => {
    vi.mocked(api.get).mockResolvedValueOnce({
      data: { items: [{ id: 'i1', protocolo: 'IMP-2026/0001' }], totalCount: 1, page: 1, pageSize: 20, totalPages: 1 },
    })

    const { impugnacoesService } = await import('@/services/impugnacoes')
    const result = await impugnacoesService.getAll({ eleicaoId: 'e1', page: 1, pageSize: 20 })

    expect(api.get).toHaveBeenCalledWith('/impugnacao', {
      params: { eleicaoId: 'e1', page: 1, pageSize: 20 },
    })
    expect(result.total).toBe(1)
    expect(result.data[0].id).toBe('i1')
  })

  it('CT-IMP-002 deve criar nova impugnacao', async () => {
    vi.mocked(api.post).mockResolvedValueOnce({
      data: { id: 'i2', protocolo: 'IMP-2026/0002' },
    })

    const { impugnacoesService, TipoImpugnacao } = await import('@/services/impugnacoes')
    const payload = {
      eleicaoId: 'e1',
      tipo: TipoImpugnacao.CANDIDATURA,
      fundamentacao: 'Fundamentacao completa da impugnacao.',
      pedido: 'Pedido principal da impugnacao.',
    }
    const created = await impugnacoesService.create(payload)

    expect(api.post).toHaveBeenCalledWith('/impugnacao', payload)
    expect(created.id).toBe('i2')
  })

  it('CT-IMP-003 deve visualizar detalhes da impugnacao', async () => {
    vi.mocked(api.get).mockResolvedValueOnce({ data: { id: 'i3', protocolo: 'IMP-2026/0003' } })

    const { impugnacoesService } = await import('@/services/impugnacoes')
    const result = await impugnacoesService.getById('i3')

    expect(api.get).toHaveBeenCalledWith('/impugnacao/i3')
    expect(result.id).toBe('i3')
  })

  it('CT-IMP-004 deve editar impugnacao', async () => {
    vi.mocked(api.put).mockResolvedValueOnce({
      data: { id: 'i4', pedido: 'Pedido atualizado.' },
    })

    const { impugnacoesService } = await import('@/services/impugnacoes')
    const result = await impugnacoesService.update('i4', { pedido: 'Pedido atualizado.' })

    expect(api.put).toHaveBeenCalledWith('/impugnacao/i4', { pedido: 'Pedido atualizado.' })
    expect(result.id).toBe('i4')
  })
})
