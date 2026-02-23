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

// Need to also mock the setToken/setTokenType exports
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

describe('Public Auth Service', () => {
    beforeEach(() => { vi.clearAllMocks() })

    describe('Voter Authentication', () => {
        it('deve solicitar código de verificação', async () => {
            const mockResponse = { verificacaoEnviada: true, canal: 'email' as const, destino: 'a***@cau.org.br' }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockResponse })

            const { authService } = await import('@/services/auth')
            const result = await authService.solicitarCodigoVerificacao({ cpf: '60000000003', registroCAU: 'A000005-SP' })

            expect(api.post).toHaveBeenCalledWith('/auth/eleitor/verificacao', { cpf: '60000000003', registroCAU: 'A000005-SP' })
            expect(result.verificacaoEnviada).toBe(true)
            expect(result.canal).toBe('email')
        })

        it('deve fazer login do eleitor', async () => {
            const mockResponse = {
                token: 'voter-token-123', expiresAt: '2026-02-24T10:00:00Z',
                voter: { id: 'v1', nome: 'João', cpf: '60000000003', registroCAU: 'A000005-SP', podeVotar: true, jaVotou: false, eleicaoId: 'e1', eleicaoNome: 'Eleição 2026' },
            }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockResponse })

            const { authService } = await import('@/services/auth')
            const result = await authService.loginEleitor({ cpf: '60000000003', registroCAU: 'A000005-SP' })

            expect(api.post).toHaveBeenCalledWith('/auth/eleitor/login', { cpf: '60000000003', registroCAU: 'A000005-SP' })
            expect(result.token).toBe('voter-token-123')
            expect(result.voter.podeVotar).toBe(true)
        })

        it('deve rejeitar login com dados inválidos', async () => {
            vi.mocked(api.post).mockRejectedValueOnce(new Error('CPF ou Registro CAU inválido'))

            const { authService } = await import('@/services/auth')
            await expect(authService.loginEleitor({ cpf: '00000000000', registroCAU: 'INVALID' })).rejects.toThrow()
        })

        it('deve fazer logout do eleitor', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: undefined })

            const { authService } = await import('@/services/auth')
            await authService.logoutEleitor()

            expect(api.post).toHaveBeenCalledWith('/auth/eleitor/logout')
        })

        it('deve obter informações do eleitor logado', async () => {
            const mockInfo = { id: 'v1', nome: 'João', cpf: '60000000003', registroCAU: 'A000005-SP', podeVotar: true, jaVotou: false, eleicaoId: 'e1', eleicaoNome: 'Eleição 2026' }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockInfo })

            const { authService } = await import('@/services/auth')
            const result = await authService.getEleitorInfo()

            expect(api.get).toHaveBeenCalledWith('/auth/eleitor/me')
            expect(result.nome).toBe('João')
        })

        it('deve verificar elegibilidade do eleitor', async () => {
            const mockResult = { elegivelVotar: true }
            vi.mocked(api.get).mockResolvedValueOnce({ data: mockResult })

            const { authService } = await import('@/services/auth')
            const result = await authService.verificarElegibilidade('e1')

            expect(api.get).toHaveBeenCalledWith('/auth/eleitor/elegibilidade/e1')
            expect(result.elegivelVotar).toBe(true)
        })
    })

    describe('Candidate Authentication', () => {
        it('deve fazer login do candidato', async () => {
            const mockResponse = {
                token: 'candidate-token', expiresAt: '2026-02-24T10:00:00Z',
                candidate: { id: 'c1', nome: 'Maria', cpf: '45555555551', registroCAU: 'A000018-DF', email: 'maria@email.com', chapaId: 'ch1', chapaNome: 'Chapa 1', chapaNumero: 1, cargo: 'Presidente', tipo: 'Titular', eleicaoId: 'e1', eleicaoNome: 'Eleição 2026', status: 2 },
            }
            vi.mocked(api.post).mockResolvedValueOnce({ data: mockResponse })

            const { authService } = await import('@/services/auth')
            const result = await authService.loginCandidato({ cpf: '45555555551', registroCAU: 'A000018-DF', senha: 'Candidato@123' })

            expect(api.post).toHaveBeenCalledWith('/auth/candidato/login', { cpf: '45555555551', registroCAU: 'A000018-DF', senha: 'Candidato@123' })
            expect(result.token).toBe('candidate-token')
        })

        it('deve registrar novo candidato', async () => {
            const registerData = { nome: 'Nova', cpf: '11111111111', registroCAU: 'A000099-SP', email: 'nova@email.com', senha: 'Senha@123', confirmacaoSenha: 'Senha@123', aceitouTermos: true }
            vi.mocked(api.post).mockResolvedValueOnce({ data: { id: 'c2', message: 'Cadastro realizado', proximosPasso: ['Aguarde aprovação'] } })

            const { authService } = await import('@/services/auth')
            const result = await authService.registerCandidato(registerData)

            expect(result.id).toBe('c2')
            expect(api.post).toHaveBeenCalledWith('/auth/candidato/register', registerData)
        })

        it('deve solicitar recuperação de senha do candidato', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { message: 'Email enviado', emailEnviado: true } })

            const { authService } = await import('@/services/auth')
            const result = await authService.forgotPasswordCandidato({ cpf: '45555555551', registroCAU: 'A000018-DF' })

            expect(result.emailEnviado).toBe(true)
        })

        it('deve redefinir senha do candidato', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { message: 'Senha redefinida' } })

            const { authService } = await import('@/services/auth')
            const result = await authService.resetPasswordCandidato({ token: 'valid-token', novaSenha: 'NovaSenha@123', confirmacaoSenha: 'NovaSenha@123' })

            expect(result.message).toBe('Senha redefinida')
        })

        it('deve verificar token', async () => {
            vi.mocked(api.post).mockResolvedValueOnce({ data: { valido: true, expiraEm: '2026-02-24T10:00:00Z' } })

            const { authService } = await import('@/services/auth')
            const result = await authService.verificarToken('some-token', 'voter')

            expect(result.valido).toBe(true)
            expect(api.post).toHaveBeenCalledWith('/auth/verify-token', { token: 'some-token', tipo: 'voter' })
        })
    })
})
