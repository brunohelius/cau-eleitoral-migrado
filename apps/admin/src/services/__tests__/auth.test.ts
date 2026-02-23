import { describe, it, expect, vi, beforeEach } from 'vitest'
import axios from 'axios'

// Mock axios
vi.mock('axios', () => {
    const mockAxios = {
        create: vi.fn(() => mockAxios),
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        delete: vi.fn(),
        patch: vi.fn(),
        interceptors: {
            request: { use: vi.fn() },
            response: { use: vi.fn() },
        },
        defaults: { headers: { common: {} } },
    }
    return { default: mockAxios, __esModule: true }
})

// Import after mock
const api = (await import('@/services/api')).default

describe('Admin Auth Service', () => {
    beforeEach(() => {
        vi.clearAllMocks()
    })

    describe('login', () => {
        it('deve fazer login com credenciais válidas', async () => {
            const mockResponse = {
                data: {
                    accessToken: 'jwt-token-123',
                    refreshToken: 'refresh-token-456',
                    expiresAt: '2026-02-24T10:00:00Z',
                    user: {
                        id: '1',
                        email: 'admin@cau.org.br',
                        nome: 'Admin',
                        nomeCompleto: 'Administrador do Sistema',
                        roles: ['Admin'],
                        permissions: ['all'],
                    },
                },
            }
            vi.mocked(api.post).mockResolvedValueOnce(mockResponse)

            const { authService } = await import('@/services/auth')
            const result = await authService.login({
                email: 'admin@cau.org.br',
                password: 'Admin@123',
            })

            expect(api.post).toHaveBeenCalledWith('/auth/login', {
                email: 'admin@cau.org.br',
                password: 'Admin@123',
            })
            expect(result.accessToken).toBe('jwt-token-123')
            expect(result.user.email).toBe('admin@cau.org.br')
        })

        it('deve incluir rememberMe no request', async () => {
            const mockResponse = {
                data: { accessToken: 'token', refreshToken: 'refresh', expiresAt: '', user: { id: '1', email: '', nome: '', roles: [], permissions: [] } },
            }
            vi.mocked(api.post).mockResolvedValueOnce(mockResponse)

            const { authService } = await import('@/services/auth')
            await authService.login({
                email: 'admin@cau.org.br',
                password: 'Admin@123',
                rememberMe: true,
            })

            expect(api.post).toHaveBeenCalledWith('/auth/login', {
                email: 'admin@cau.org.br',
                password: 'Admin@123',
                rememberMe: true,
            })
        })

        it('deve rejeitar com erro para credenciais inválidas', async () => {
            vi.mocked(api.post).mockRejectedValueOnce(new Error('Unauthorized'))

            const { authService } = await import('@/services/auth')
            await expect(
                authService.login({ email: 'wrong@email.com', password: 'wrong' })
            ).rejects.toThrow('Unauthorized')
        })
    })

    describe('logout', () => {
        it('deve chamar endpoint de logout', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: undefined })

            const { authService } = await import('@/services/auth')
            await authService.logout()

            expect(api.post).toHaveBeenCalledWith('/auth/logout')
        })
    })

    describe('register', () => {
        it('deve registrar novo usuário com sucesso', async () => {
            const mockResponse = {
                data: { userId: 'new-user-123', message: 'Usuário criado com sucesso' },
            }
            vi.mocked(api.post).mockResolvedValueOnce(mockResponse)

            const { authService } = await import('@/services/auth')
            const result = await authService.register({
                email: 'novo@cau.org.br',
                nome: 'Novo Usuário',
                password: 'Senha@123',
                confirmPassword: 'Senha@123',
            })

            expect(result.userId).toBe('new-user-123')
            expect(api.post).toHaveBeenCalledWith('/auth/register', expect.objectContaining({
                email: 'novo@cau.org.br',
            }))
        })
    })

    describe('forgotPassword', () => {
        it('deve solicitar recuperação de senha', async () => {
            const mockResponse = {
                data: { message: 'Email enviado com sucesso' },
            }
            vi.mocked(api.post).mockResolvedValueOnce(mockResponse)

            const { authService } = await import('@/services/auth')
            const result = await authService.forgotPassword('admin@cau.org.br')

            expect(result.message).toBe('Email enviado com sucesso')
            expect(api.post).toHaveBeenCalledWith('/auth/forgot-password', { email: 'admin@cau.org.br' })
        })
    })

    describe('resetPassword', () => {
        it('deve redefinir senha com token válido', async () => {
            const mockResponse = {
                data: { message: 'Senha redefinida com sucesso' },
            }
            vi.mocked(api.post).mockResolvedValueOnce(mockResponse)

            const { authService } = await import('@/services/auth')
            const result = await authService.resetPassword('valid-token', 'NovaSenha@123', 'NovaSenha@123')

            expect(result.message).toBe('Senha redefinida com sucesso')
            expect(api.post).toHaveBeenCalledWith('/auth/reset-password', {
                token: 'valid-token',
                newPassword: 'NovaSenha@123',
                confirmPassword: 'NovaSenha@123',
            })
        })
    })

    describe('getMe', () => {
        it('deve obter dados do usuário logado', async () => {
            const mockUser = {
                data: {
                    id: '1',
                    email: 'admin@cau.org.br',
                    nome: 'Admin',
                    roles: ['Admin'],
                    permissions: ['all'],
                },
            }
            vi.mocked(api.get).mockResolvedValueOnce(mockUser)

            const { authService } = await import('@/services/auth')
            const result = await authService.getMe()

            expect(result.email).toBe('admin@cau.org.br')
            expect(api.get).toHaveBeenCalledWith('/auth/me')
        })
    })

    describe('refreshToken', () => {
        it('deve renovar token de acesso', async () => {
            const mockResponse = {
                data: {
                    accessToken: 'new-token',
                    refreshToken: 'new-refresh',
                    expiresAt: '2026-02-25T10:00:00Z',
                    user: { id: '1', email: 'admin@cau.org.br', nome: 'Admin', roles: [], permissions: [] },
                },
            }
            vi.mocked(api.post).mockResolvedValueOnce(mockResponse)

            const { authService } = await import('@/services/auth')
            const result = await authService.refreshToken('old-refresh-token')

            expect(result.accessToken).toBe('new-token')
            expect(api.post).toHaveBeenCalledWith('/auth/refresh-token', { refreshToken: 'old-refresh-token' })
        })
    })
})
