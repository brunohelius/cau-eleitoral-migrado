import { describe, it, expect, beforeEach, vi } from 'vitest'
import { useAuthStore } from '@/stores/auth'

describe('Admin Auth Store', () => {
    beforeEach(() => {
        // Reset store to initial state
        useAuthStore.setState({
            user: null,
            accessToken: null,
            refreshToken: null,
            isAuthenticated: false,
        })
    })

    describe('estado inicial', () => {
        it('deve iniciar com estado vazio e não autenticado', () => {
            const state = useAuthStore.getState()
            expect(state.user).toBeNull()
            expect(state.accessToken).toBeNull()
            expect(state.refreshToken).toBeNull()
            expect(state.isAuthenticated).toBe(false)
        })
    })

    describe('setAuth', () => {
        it('deve definir usuário e tokens corretamente', () => {
            const user = {
                id: '1',
                email: 'admin@cau.org.br',
                nome: 'Admin',
                roles: ['Admin'],
                permissions: ['all'],
            }

            useAuthStore.getState().setAuth(user, 'jwt-token', 'refresh-token')

            const state = useAuthStore.getState()
            expect(state.user).toEqual(user)
            expect(state.accessToken).toBe('jwt-token')
            expect(state.refreshToken).toBe('refresh-token')
            expect(state.isAuthenticated).toBe(true)
        })

        it('deve sobrescrever dados anteriores', () => {
            const user1 = { id: '1', email: 'user1@cau.org.br', nome: 'User 1', roles: [], permissions: [] }
            const user2 = { id: '2', email: 'user2@cau.org.br', nome: 'User 2', roles: ['Admin'], permissions: [] }

            useAuthStore.getState().setAuth(user1, 'token1', 'refresh1')
            useAuthStore.getState().setAuth(user2, 'token2', 'refresh2')

            const state = useAuthStore.getState()
            expect(state.user?.email).toBe('user2@cau.org.br')
            expect(state.accessToken).toBe('token2')
        })
    })

    describe('logout', () => {
        it('deve limpar todos os dados de autenticação', () => {
            const user = { id: '1', email: 'admin@cau.org.br', nome: 'Admin', roles: [], permissions: [] }
            useAuthStore.getState().setAuth(user, 'token', 'refresh')

            // Verificar que está autenticado
            expect(useAuthStore.getState().isAuthenticated).toBe(true)

            // Fazer logout
            useAuthStore.getState().logout()

            const state = useAuthStore.getState()
            expect(state.user).toBeNull()
            expect(state.accessToken).toBeNull()
            expect(state.refreshToken).toBeNull()
            expect(state.isAuthenticated).toBe(false)
        })
    })

    describe('updateUser', () => {
        it('deve atualizar parcialmente os dados do usuário', () => {
            const user = { id: '1', email: 'admin@cau.org.br', nome: 'Admin', roles: ['Admin'], permissions: ['all'] }
            useAuthStore.getState().setAuth(user, 'token', 'refresh')

            useAuthStore.getState().updateUser({ nome: 'Admin Atualizado', nomeCompleto: 'Administrador Completo' })

            const state = useAuthStore.getState()
            expect(state.user?.nome).toBe('Admin Atualizado')
            expect(state.user?.nomeCompleto).toBe('Administrador Completo')
            expect(state.user?.email).toBe('admin@cau.org.br') // não alterado
            expect(state.user?.roles).toEqual(['Admin']) // não alterado
        })

        it('deve não fazer nada se não há usuário logado', () => {
            useAuthStore.getState().updateUser({ nome: 'Teste' })

            const state = useAuthStore.getState()
            expect(state.user).toBeNull()
        })

        it('deve manter isAuthenticated após atualização', () => {
            const user = { id: '1', email: 'admin@cau.org.br', nome: 'Admin', roles: [], permissions: [] }
            useAuthStore.getState().setAuth(user, 'token', 'refresh')

            useAuthStore.getState().updateUser({ nome: 'Novo Nome' })

            expect(useAuthStore.getState().isAuthenticated).toBe(true)
        })
    })
})
