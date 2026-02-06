import api, { mapPagedResponse } from './api'

// Enums matching backend (CAU.Eleitoral.Domain.Enums)
export enum StatusUsuario {
  ATIVO = 0,
  INATIVO = 1,
  BLOQUEADO = 2,
  PENDENTE_CADASTRO = 3,
  PENDENTE_CONFIRMACAO = 4,
}

export enum TipoUsuario {
  ADMINISTRADOR = 0,
  COMISSAO_ELEITORAL = 1,
  CONSELHEIRO = 2,
  PROFISSIONAL = 3,
  CANDIDATO = 4,
  ELEITOR = 5,
}

// DTO shapes
export interface Role {
  id: string
  nome: string
  descricao?: string | null
  ativo?: boolean
  totalUsuarios?: number
  permissoes?: string[]
}

export interface ProfissionalResumo {
  id: string
  usuarioId?: string | null
  registroCAU: string
  nome: string
  cpf: string
  email?: string | null
  telefone?: string | null
  tipo: number
  tipoNome?: string
  status: number
  statusNome?: string
  regionalId?: string | null
  regionalNome?: string | null
  eleitorApto?: boolean
  dataRegistro?: string | null
  dataUltimaAtualizacao?: string | null
}

export interface LogAcesso {
  id: string
  usuarioId: string
  ipAddress?: string | null
  userAgent?: string | null
  acao?: string | null
  sucesso: boolean
  detalhes?: string | null
  dataAcesso: string
}

export interface Usuario {
  id: string
  email: string
  nome: string
  nomeCompleto?: string | null
  cpf?: string | null
  telefone?: string | null
  status: StatusUsuario
  statusNome?: string
  tipo: TipoUsuario
  tipoNome?: string
  ultimoAcesso?: string | null
  emailConfirmado: boolean
  doisFatoresHabilitado: boolean
  roles: string[]
  createdAt: string
  updatedAt?: string | null
}

export interface UsuarioDetail extends Usuario {
  rolesDetail: Role[]
  ultimaTrocaSenha?: string | null
  tentativasLogin: number
  bloqueadoAte?: string | null
  profissional?: ProfissionalResumo | null
  ultimosAcessos?: LogAcesso[]
}

export interface CreateUsuarioRequest {
  email: string
  nome: string
  nomeCompleto?: string
  cpf?: string
  telefone?: string
  password: string
  tipo: TipoUsuario
  roles?: string[]
  enviarEmailConfirmacao?: boolean
}

export interface UpdateUsuarioRequest {
  nome?: string
  nomeCompleto?: string
  telefone?: string
  tipo?: TipoUsuario
  roles?: string[]
}

export interface UsuarioListParams {
  tipo?: TipoUsuario
  status?: StatusUsuario
  search?: string
  role?: string
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

export const usuariosService = {
  getAll: async (params?: UsuarioListParams): Promise<PaginatedResponse<Usuario>> => {
    const response = await api.get('/usuario/paged', { params })
    return mapPagedResponse<Usuario>(response.data)
  },

  getById: async (id: string): Promise<Usuario> => {
    const response = await api.get<Usuario>(`/usuario/${id}`)
    return response.data
  },

  getByIdDetailed: async (id: string): Promise<UsuarioDetail> => {
    const response = await api.get<UsuarioDetail>(`/usuario/${id}/detail`)
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

  ativar: async (id: string): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/activate`)
    return response.data
  },

  inativar: async (id: string): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/deactivate`)
    return response.data
  },

  bloquear: async (id: string): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/block`)
    return response.data
  },

  desbloquear: async (id: string): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/unblock`)
    return response.data
  },

  resetarSenha: async (id: string, newPassword: string): Promise<Usuario> => {
    const response = await api.post<Usuario>(`/usuario/${id}/reset-password`, { newPassword })
    return response.data
  },

  getRoles: async (): Promise<Role[]> => {
    const response = await api.get<Role[]>('/usuario/roles')
    return response.data
  },
}
