import { Routes, Route, Navigate } from 'react-router-dom'
import { Toaster } from '@/components/ui/toaster'
import { useAuthStore } from '@/stores/auth'

// Layouts
import { DashboardLayout } from '@/components/layout/DashboardLayout'
import { AuthLayout } from '@/components/layout/AuthLayout'

// Auth Pages
import { LoginPage } from '@/pages/auth/LoginPage'
import { ForgotPasswordPage } from '@/pages/auth/ForgotPasswordPage'
import { ResetPasswordPage } from '@/pages/auth/ResetPasswordPage'

// Dashboard
import { DashboardPage } from '@/pages/dashboard/DashboardPage'

// Eleicoes
import { EleicoesPage } from '@/pages/eleicoes/EleicoesPage'
import { EleicaoDetailPage } from '@/pages/eleicoes/EleicaoDetailPage'
import { EleicaoFormPage } from '@/pages/eleicoes/EleicaoFormPage'
import { EleicaoCalendarioPage } from '@/pages/eleicoes/EleicaoCalendarioPage'
import { EleicaoApuracaoPage } from '@/pages/eleicoes/EleicaoApuracaoPage'

// Chapas
import { ChapasPage } from '@/pages/chapas/ChapasPage'
import { ChapaDetailPage } from '@/pages/chapas/ChapaDetailPage'
import { ChapaFormPage } from '@/pages/chapas/ChapaFormPage'
import { ChapaMembrosPage } from '@/pages/chapas/ChapaMembrosPage'
import { ChapaDocumentosPage } from '@/pages/chapas/ChapaDocumentosPage'

// Denuncias
import { DenunciasPage } from '@/pages/denuncias/DenunciasPage'
import { DenunciaDetailPage } from '@/pages/denuncias/DenunciaDetailPage'
import { DenunciaFormPage } from '@/pages/denuncias/DenunciaFormPage'
import { DenunciaJulgamentoPage } from '@/pages/denuncias/DenunciaJulgamentoPage'

// Impugnacoes
import { ImpugnacoesPage } from '@/pages/impugnacoes/ImpugnacoesPage'
import { ImpugnacaoDetailPage } from '@/pages/impugnacoes/ImpugnacaoDetailPage'
import { ImpugnacaoFormPage } from '@/pages/impugnacoes/ImpugnacaoFormPage'

// Julgamentos
import { JulgamentosPage } from '@/pages/julgamentos/JulgamentosPage'
import { JulgamentoDetailPage } from '@/pages/julgamentos/JulgamentoDetailPage'
import { SessaoJulgamentoPage } from '@/pages/julgamentos/SessaoJulgamentoPage'

// Usuarios
import { UsuariosPage } from '@/pages/usuarios/UsuariosPage'
import { UsuarioDetailPage } from '@/pages/usuarios/UsuarioDetailPage'
import { UsuarioFormPage } from '@/pages/usuarios/UsuarioFormPage'

// Relatorios
import { RelatoriosPage } from '@/pages/relatorios/RelatoriosPage'
import { RelatorioEleicaoPage } from '@/pages/relatorios/RelatorioEleicaoPage'
import { RelatorioVotacaoPage } from '@/pages/relatorios/RelatorioVotacaoPage'

// Auditoria
import { AuditoriaPage } from '@/pages/auditoria/AuditoriaPage'

// Configuracoes
import { ConfiguracoesPage } from '@/pages/configuracoes/ConfiguracoesPage'

// Protected Route Component
function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useAuthStore()

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />
  }

  return <>{children}</>
}

function App() {
  return (
    <>
      <Routes>
        {/* Auth Routes */}
        <Route element={<AuthLayout />}>
          <Route path="/login" element={<LoginPage />} />
          <Route path="/forgot-password" element={<ForgotPasswordPage />} />
          <Route path="/reset-password" element={<ResetPasswordPage />} />
        </Route>

        {/* Protected Routes */}
        <Route
          element={
            <ProtectedRoute>
              <DashboardLayout />
            </ProtectedRoute>
          }
        >
          <Route path="/" element={<Navigate to="/dashboard" replace />} />
          <Route path="/dashboard" element={<DashboardPage />} />

          {/* Eleicoes */}
          <Route path="/eleicoes" element={<EleicoesPage />} />
          <Route path="/eleicoes/nova" element={<EleicaoFormPage />} />
          <Route path="/eleicoes/:id" element={<EleicaoDetailPage />} />
          <Route path="/eleicoes/:id/editar" element={<EleicaoFormPage />} />
          <Route path="/eleicoes/:id/calendario" element={<EleicaoCalendarioPage />} />
          <Route path="/eleicoes/:id/apuracao" element={<EleicaoApuracaoPage />} />

          {/* Chapas */}
          <Route path="/chapas" element={<ChapasPage />} />
          <Route path="/chapas/nova" element={<ChapaFormPage />} />
          <Route path="/chapas/:id" element={<ChapaDetailPage />} />
          <Route path="/chapas/:id/editar" element={<ChapaFormPage />} />
          <Route path="/chapas/:id/membros" element={<ChapaMembrosPage />} />
          <Route path="/chapas/:id/documentos" element={<ChapaDocumentosPage />} />

          {/* Denuncias */}
          <Route path="/denuncias" element={<DenunciasPage />} />
          <Route path="/denuncias/nova" element={<DenunciaFormPage />} />
          <Route path="/denuncias/:id" element={<DenunciaDetailPage />} />
          <Route path="/denuncias/:id/julgamento" element={<DenunciaJulgamentoPage />} />

          {/* Impugnacoes */}
          <Route path="/impugnacoes" element={<ImpugnacoesPage />} />
          <Route path="/impugnacoes/nova" element={<ImpugnacaoFormPage />} />
          <Route path="/impugnacoes/:id" element={<ImpugnacaoDetailPage />} />

          {/* Julgamentos */}
          <Route path="/julgamentos" element={<JulgamentosPage />} />
          <Route path="/julgamentos/sessao" element={<SessaoJulgamentoPage />} />
          <Route path="/julgamentos/:id" element={<JulgamentoDetailPage />} />

          {/* Usuarios */}
          <Route path="/usuarios" element={<UsuariosPage />} />
          <Route path="/usuarios/novo" element={<UsuarioFormPage />} />
          <Route path="/usuarios/:id" element={<UsuarioDetailPage />} />
          <Route path="/usuarios/:id/editar" element={<UsuarioFormPage />} />

          {/* Relatorios */}
          <Route path="/relatorios" element={<RelatoriosPage />} />
          <Route path="/relatorios/eleicao" element={<RelatorioEleicaoPage />} />
          <Route path="/relatorios/votacao" element={<RelatorioVotacaoPage />} />

          {/* Auditoria */}
          <Route path="/auditoria" element={<AuditoriaPage />} />

          {/* Configuracoes */}
          <Route path="/configuracoes" element={<ConfiguracoesPage />} />
        </Route>

        {/* 404 */}
        <Route path="*" element={<Navigate to="/dashboard" replace />} />
      </Routes>
      <Toaster />
    </>
  )
}

export default App
