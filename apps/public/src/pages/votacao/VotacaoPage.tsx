import { Navigate, useParams } from 'react-router-dom'
import { useVoterStore } from '@/stores/voter'

export function VotacaoPage() {
  const { eleicaoId } = useParams<{ eleicaoId: string }>()
  const { isAuthenticated, voter } = useVoterStore()

  if (!eleicaoId) {
    return <Navigate to="/votacao" replace />
  }

  if (!isAuthenticated) {
    return <Navigate to="/votacao" replace />
  }

  const jaVotouNestaEleicao = voter?.eleicaoId === eleicaoId && voter?.jaVotou
  if (jaVotouNestaEleicao) {
    return <Navigate to={`/eleitor/votacao/${eleicaoId}/comprovante`} replace />
  }

  return <Navigate to={`/eleitor/votacao/${eleicaoId}/cedula`} replace />
}
