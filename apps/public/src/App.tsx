import { Routes, Route } from 'react-router-dom'

// Layouts
import { PublicLayout } from '@/components/layout/PublicLayout'
import { VoterLayout } from '@/components/layout/VoterLayout'
import { CandidateLayout } from '@/components/layout/CandidateLayout'

// Public Pages
import { HomePage } from '@/pages/home/HomePage'
import { EleicoesPublicPage } from '@/pages/eleicoes/EleicoesPublicPage'
import { EleicaoDetailPage } from '@/pages/eleicoes/EleicaoDetailPage'
import { EleicaoChapasPage } from '@/pages/eleicoes/EleicaoChapasPage'
import { EleicaoResultadosPage } from '@/pages/eleicoes/EleicaoResultadosPage'
import { CalendarioPage } from '@/pages/calendario/CalendarioPage'
import { DocumentosPage } from '@/pages/documentos/DocumentosPage'
import { FaqPage } from '@/pages/faq/FaqPage'

// Legacy Votacao Page (kept for backwards compatibility)
import { VotacaoPage } from '@/pages/votacao/VotacaoPage'

// Voter Area Pages
import { VotacaoLoginPage } from '@/pages/votacao/VotacaoLoginPage'
import { VotacaoEleicaoPage } from '@/pages/votacao/VotacaoEleicaoPage'
import { VotacaoCedulaPage } from '@/pages/votacao/VotacaoCedulaPage'
import { VotacaoConfirmacaoPage } from '@/pages/votacao/VotacaoConfirmacaoPage'
import { VotacaoComprovantePage } from '@/pages/votacao/VotacaoComprovantePage'
import { PerfilPage } from '@/pages/perfil/PerfilPage'
import { MeusVotosPage } from '@/pages/perfil/MeusVotosPage'
import { NotificacoesPage } from '@/pages/notificacoes/NotificacoesPage'

// Candidate Area Pages
import { CandidatoLoginPage } from '@/pages/candidato/CandidatoLoginPage'
import { CandidatoChapaPage } from '@/pages/candidato/CandidatoChapaPage'
import { CandidatoDocumentosPage } from '@/pages/candidato/CandidatoDocumentosPage'
import { CandidatoPlataformaPage } from '@/pages/candidato/CandidatoPlataformaPage'
import { CandidatoDenunciasPage } from '@/pages/candidato/CandidatoDenunciasPage'
import { CandidatoDefesaPage } from '@/pages/candidato/CandidatoDefesaPage'
import { CandidatoRecursosPage } from '@/pages/candidato/CandidatoRecursosPage'
import { CandidatoHistoricoPage } from '@/pages/candidato/CandidatoHistoricoPage'
import { CandidatoPerfilPage } from '@/pages/candidato/CandidatoPerfilPage'

function App() {
  return (
    <Routes>
      {/* Public Routes */}
      <Route element={<PublicLayout />}>
        {/* Home */}
        <Route path="/" element={<HomePage />} />

        {/* Elections */}
        <Route path="/eleicoes" element={<EleicoesPublicPage />} />
        <Route path="/eleicoes/:id" element={<EleicaoDetailPage />} />
        <Route path="/eleicoes/:id/chapas" element={<EleicaoChapasPage />} />
        <Route path="/eleicoes/:id/resultados" element={<EleicaoResultadosPage />} />

        {/* Calendar */}
        <Route path="/calendario" element={<CalendarioPage />} />

        {/* Documents */}
        <Route path="/documentos" element={<DocumentosPage />} />

        {/* FAQ */}
        <Route path="/faq" element={<FaqPage />} />

        {/* Legacy Voting (redirect to login) */}
        <Route path="/votacao" element={<VotacaoLoginPage />} />
        <Route path="/votacao/:eleicaoId" element={<VotacaoPage />} />

        {/* Candidate Login */}
        <Route path="/candidato/login" element={<CandidatoLoginPage />} />
      </Route>

      {/* Voter Area Routes (Protected) */}
      <Route path="/eleitor" element={<VoterLayout />}>
        <Route index element={<VotacaoEleicaoPage />} />
        <Route path="votacao" element={<VotacaoEleicaoPage />} />
        <Route path="votacao/:eleicaoId/cedula" element={<VotacaoCedulaPage />} />
        <Route path="votacao/:eleicaoId/confirmacao" element={<VotacaoConfirmacaoPage />} />
        <Route path="votacao/:eleicaoId/comprovante" element={<VotacaoComprovantePage />} />
        <Route path="meus-votos" element={<MeusVotosPage />} />
        <Route path="notificacoes" element={<NotificacoesPage />} />
        <Route path="perfil" element={<PerfilPage />} />
      </Route>

      {/* Candidate Area Routes (Protected) */}
      <Route path="/candidato" element={<CandidateLayout />}>
        <Route index element={<CandidatoChapaPage />} />
        <Route path="documentos" element={<CandidatoDocumentosPage />} />
        <Route path="plataforma" element={<CandidatoPlataformaPage />} />
        <Route path="denuncias" element={<CandidatoDenunciasPage />} />
        <Route path="defesas" element={<CandidatoDefesaPage />} />
        <Route path="recursos" element={<CandidatoRecursosPage />} />
        <Route path="historico" element={<CandidatoHistoricoPage />} />
        <Route path="perfil" element={<CandidatoPerfilPage />} />
      </Route>
    </Routes>
  )
}

export default App
