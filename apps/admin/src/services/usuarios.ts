import api from './api'

// Enums
export enum StatusUsuario {
  ATIVO = 0,
  INATIVO = 1,
  PENDENTE = 2,
  BLOQUEADO = 3,
  SUSPENSO = 4,
}

export enum TipoUsuario {
  ADMINISTRADOR = 0,
  COMISSAO = 1,
  FISCAL = 2,
  ANALISTA = 3,
  AUDITOR = 4,
  OPERADOR = 5,
}

// Interfaces
export interface Permissao {
  id: string
  nome: string
  codigo: string
  descricao?: string
  modulo: string
}

export interface Role {
  id: string
  nome: string
  codigo: string
  descricao?: string
  permissoes?: Permissao[]
}

export interface Usuario {
  id: string
  email: string
  nome: string
  nomeCompleto?: string
  cpf?: string
  telefone?: string
  registroCAU?: string
  tipo: TipoUsuario
  status: StatusUsuario
  avatarUrl?: string
  regionalId?: string
  regionalNome?: string
  roles: Role[]
  permissoes?: string[]
  ultimoAcesso?: string
  emailVerificado: boolean
  doisFatoresAtivo: boolean
  createdAt: string
  updatedAt?: string
}

export interface CreateUsuarioRequest {
  email: string
  nome: string
  nomeCompleto?: string
  cpf?: string
  telefone?: string
  registroCAU?: string
  tipo: TipoUsuario
  regionalId?: string
  roles: string[]
  password?: string
  enviarEmailBoasVindas?: boolean
}

export interface UpdateUsuarioRequest {
  nome?: string
  nomeCompleto?: string
  cpf?: string
  telefone?: string
  registroCAU?: string
  tipo?: TipoUsuario
  regionalId?: string
  avatarUrl?: string
}

export interface UpdateSenhaRequest {
  senhaAtual: string
  novaSenha: string
  confirmacaoSenha: string
}

export interface ResetSenhaAdminRequest {
  novaSenha?: string
  enviarEmail?: boolean
}

export interface UsuarioListParams {
  tipo?: TipoUsuario
  status?: StatusUsuario
  regionalId?: string
  role?: string
  search?: string
  page?: number
  pageSize?: number
}

export interface PaginatedResponse<T> {
  data: T[]
  total: number
  page: number
  pageSize: number
  totalPages: number
}

export interface LogAtividade {
  id: string
  usuarioId: string
  acao: string
  descricao: string
  ip?: string
  userAgent?: string
  metadata?: Record<string, unknown>
  createdAt: string
}

export const usuariosService = {
  // CRUD Operations
  getAll: async (params?: UsuarioListParams): Promise<PaginatedResponse<Usuario>> => {
    const response = await api.get<PaginatedResponse<Usuario>>('/usuario', { params })
    return response.data
  },

  getById: async (id: string): Promise<Usuario> => {
    const response = await api.get<Usuario>(`/usuario/${id}`)
    return response.data
  },

  getByEmail: async (email: string): Promise<Usuario> => {
    const response = await api.get<Usuario>(`/usuario/email/${email}`)
    return response.data
  },

  getByCpf: async (cpf: string): Promise<Usuario> => {
    const response = await api.get<Usuario>(`/usuario/cpf/${cpf}`)
    return response.data
  },

  getByRegistroCAU: async (registro: string): Promise<Usuario> => {
    const response = await api.get<Usuario>(`/usuario/registro-cau/${registro}`)
    return response.data
  },

  create: async (data: CreateUsuarioRequest): Promise<Usuario> => {
    const response = await api.post<Usuario>('/usuario', data)
    return response.data
  },

  update: async (id: string, data: UpdateUsuarioRequest): Promise<Usuario> => {
    const response = await api.put<Usuario>(`/usuario/${id}`, data)
    return response.data
  },

  delete: async (id: string): Promise<void> => {
    await api.delete(`/usuario/${id}`)
  },

  // Status Operations
  ativar: async (id: string): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/ativar`)
    return response.data
  },

  inativar: async (id: string, motivo?: string): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/inativar`, { motivo })
    return response.data
  },

  bloquear: async (id: string, motivo: string): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/bloquear`, { motivo })
    return response.data
  },

  desbloquear: async (id: string): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/desbloquear`)
    return response.data
  },

  suspender: async (id: string, motivo: string, dataFim?: string): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/suspender`, { motivo, dataFim })
    return response.data
  },

  // Password Operations
  alterarSenha: async (id: string, data: UpdateSenhaRequest): Promise<void> => {
    await api.post(`/usuario/${id}/alterar-senha`, data)
  },

  resetarSenha: async (id: string, data?: ResetSenhaAdminRequest): Promise<{ tempPassword?: string }> => {
    const response = await api.post(`/usuario/${id}/resetar-senha`, data)
    return response.data
  },

  // Roles & Permissions Operations
  getRoles: async (): Promise<Role[]> => {
    const response = await api.get<Role[]>('/usuario/roles')
    return response.data
  },

  getPermissoes: async (): Promise<Permissao[]> => {
    const response = await api.get<Permissao[]>('/usuario/permissoes')
    return response.data
  },

  atribuirRoles: async (id: string, roles: string[]): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/roles`, { roles })
    return response.data
  },

  removerRole: async (id: string, roleId: string): Promise<Usuario> => {
    const response = await api.delete<Usuario>(`/usuario/${id}/roles/${roleId}`)
    return response.data
  },

  atribuirPermissoes: async (id: string, permissoes: string[]): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/permissoes`, { permissoes })
    return response.data
  },

  // Avatar Operations
  uploadAvatar: async (id: string, arquivo: File): Promise<{ avatarUrl: string }> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)

    const response = await api.post<{ avatarUrl: string }>(`/usuario/${id}/avatar`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  removeAvatar: async (id: string): Promise<void> => {
    await api.delete(`/usuario/${id}/avatar`)
  },

  // Two-Factor Auth Operations
  ativar2FA: async (id: string): Promise<{ qrCode: string; secret: string }> => {
    const response = await api.post(`/usuario/${id}/2fa/ativar`)
    return response.data
  },

  confirmar2FA: async (id: string, codigo: string): Promise<void> => {
    await api.post(`/usuario/${id}/2fa/confirmar`, { codigo })
  },

  desativar2FA: async (id: string, codigo: string): Promise<void> => {
    await api.post(`/usuario/${id}/2fa/desativar`, { codigo })
  },

  // Activity Log
  getAtividades: async (id: string, params?: {
    dataInicio?: string
    dataFim?: string
    page?: number
    pageSize?: number
  }): Promise<PaginatedResponse<LogAtividade>> => {
    const response = await api.get<PaginatedResponse<LogAtividade>>(`/usuario/${id}/atividades`, { params })
    return response.data
  },

  // Email Verification
  enviarVerificacaoEmail: async (id: string): Promise<void> => {
    await api.post(`/usuario/${id}/enviar-verificacao`)
  },

  verificarEmail: async (token: string): Promise<void> => {
    await api.post('/usuario/verificar-email', { token })
  },

  // Statistics
  getEstatisticas: async (): Promise<{
    total: number
    ativos: number
    inativos: number
    pendentes: number
    bloqueados: number
    porTipo: Record<string, number>
    porRegional: Record<string, number>
  }> => {
    const response = await api.get('/usuario/estatisticas')
    return response.data
  },

  // Export
  exportar: async (params?: UsuarioListParams & { formato?: 'xlsx' | 'csv' }): Promise<Blob> => {
    const response = await api.get('/usuario/exportar', {
      params,
      responseType: 'blob',
    })
    return response.data
  },

  // Import
  importar: async (arquivo: File, opcoes?: {
    atualizarExistentes?: boolean
    enviarEmailBoasVindas?: boolean
  }): Promise<{
    sucesso: number
    erros: { linha: number; erro: string }[]
  }> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)
    if (opcoes) {
      formData.append('opcoes', JSON.stringify(opcoes))
    }

    const response = await api.post('/usuario/importar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },
}
