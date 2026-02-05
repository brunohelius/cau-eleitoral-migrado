import { create } from 'zustand'

export type VotacaoStep = 'verificacao' | 'cedula' | 'confirmacao' | 'comprovante'

export interface ChapaOpcao {
  id: string
  numero: number
  nome: string
  sigla?: string
  logoUrl?: string
}

export interface CedulaInfo {
  eleicaoId: string
  eleicaoNome: string
  eleitorId: string
  eleitorNome: string
  chapas: ChapaOpcao[]
  instrucoes: string[]
  tempoMaximoMinutos: number
  iniciadaEm: string
  expiraEm: string
}

export interface VotoSelecionado {
  tipo: 'chapa' | 'branco' | 'nulo'
  chapaId?: string
  chapaNumero?: number
  chapaNome?: string
}

export interface ComprovanteInfo {
  id: string
  protocolo: string
  eleicaoId: string
  eleicaoNome: string
  dataHoraVoto: string
  hashComprovante: string
  qrCode?: string
  mensagem: string
}

interface VotacaoState {
  // Current step
  step: VotacaoStep

  // Cedula (ballot)
  cedula: CedulaInfo | null

  // Selected vote
  votoSelecionado: VotoSelecionado | null

  // Comprovante (receipt)
  comprovante: ComprovanteInfo | null

  // Session timer
  tempoRestante: number | null // in seconds
  timerAtivo: boolean

  // State
  isLoading: boolean
  isSubmitting: boolean
  error: string | null

  // Confirmation dialog
  showConfirmacao: boolean

  // Actions
  setStep: (step: VotacaoStep) => void
  setCedula: (cedula: CedulaInfo) => void
  selecionarChapa: (chapa: ChapaOpcao) => void
  selecionarBranco: () => void
  selecionarNulo: () => void
  limparSelecao: () => void
  setComprovante: (comprovante: ComprovanteInfo) => void
  setTempoRestante: (tempo: number) => void
  decrementarTempo: () => void
  setTimerAtivo: (ativo: boolean) => void
  setLoading: (loading: boolean) => void
  setSubmitting: (submitting: boolean) => void
  setError: (error: string | null) => void
  setShowConfirmacao: (show: boolean) => void
  resetVotacao: () => void

  // Computed
  isSessionExpired: () => boolean
  getTempoFormatado: () => string
}

const initialState = {
  step: 'verificacao' as VotacaoStep,
  cedula: null,
  votoSelecionado: null,
  comprovante: null,
  tempoRestante: null,
  timerAtivo: false,
  isLoading: false,
  isSubmitting: false,
  error: null,
  showConfirmacao: false,
}

export const useVotacaoStore = create<VotacaoState>()((set, get) => ({
  ...initialState,

  setStep: (step) =>
    set({ step, error: null }),

  setCedula: (cedula) => {
    // Calculate initial time remaining
    const expiraEm = new Date(cedula.expiraEm)
    const agora = new Date()
    const tempoRestante = Math.max(0, Math.floor((expiraEm.getTime() - agora.getTime()) / 1000))

    set({
      cedula,
      step: 'cedula',
      tempoRestante,
      timerAtivo: true,
      error: null,
    })
  },

  selecionarChapa: (chapa) =>
    set({
      votoSelecionado: {
        tipo: 'chapa',
        chapaId: chapa.id,
        chapaNumero: chapa.numero,
        chapaNome: chapa.nome,
      },
    }),

  selecionarBranco: () =>
    set({
      votoSelecionado: {
        tipo: 'branco',
      },
    }),

  selecionarNulo: () =>
    set({
      votoSelecionado: {
        tipo: 'nulo',
      },
    }),

  limparSelecao: () =>
    set({ votoSelecionado: null }),

  setComprovante: (comprovante) =>
    set({
      comprovante,
      step: 'comprovante',
      timerAtivo: false,
      isSubmitting: false,
    }),

  setTempoRestante: (tempo) =>
    set({ tempoRestante: Math.max(0, tempo) }),

  decrementarTempo: () =>
    set((state) => ({
      tempoRestante: state.tempoRestante !== null
        ? Math.max(0, state.tempoRestante - 1)
        : null,
    })),

  setTimerAtivo: (ativo) =>
    set({ timerAtivo: ativo }),

  setLoading: (loading) =>
    set({ isLoading: loading }),

  setSubmitting: (submitting) =>
    set({ isSubmitting: submitting }),

  setError: (error) =>
    set({ error, isLoading: false, isSubmitting: false }),

  setShowConfirmacao: (show) =>
    set({ showConfirmacao: show }),

  resetVotacao: () =>
    set(initialState),

  isSessionExpired: () => {
    const { tempoRestante } = get()
    return tempoRestante !== null && tempoRestante <= 0
  },

  getTempoFormatado: () => {
    const { tempoRestante } = get()
    if (tempoRestante === null) return '--:--'

    const minutos = Math.floor(tempoRestante / 60)
    const segundos = tempoRestante % 60

    return `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`
  },
}))

// Helper hooks
export const useVotacaoStep = () => {
  const step = useVotacaoStore((state) => state.step)
  const setStep = useVotacaoStore((state) => state.setStep)

  return { step, setStep }
}

export const useVotoSelecionado = () => {
  const votoSelecionado = useVotacaoStore((state) => state.votoSelecionado)
  const selecionarChapa = useVotacaoStore((state) => state.selecionarChapa)
  const selecionarBranco = useVotacaoStore((state) => state.selecionarBranco)
  const selecionarNulo = useVotacaoStore((state) => state.selecionarNulo)
  const limparSelecao = useVotacaoStore((state) => state.limparSelecao)

  return {
    voto: votoSelecionado,
    hasSelection: votoSelecionado !== null,
    selecionarChapa,
    selecionarBranco,
    selecionarNulo,
    limpar: limparSelecao,
  }
}

export const useVotacaoTimer = () => {
  const tempoRestante = useVotacaoStore((state) => state.tempoRestante)
  const timerAtivo = useVotacaoStore((state) => state.timerAtivo)
  const decrementarTempo = useVotacaoStore((state) => state.decrementarTempo)
  const getTempoFormatado = useVotacaoStore((state) => state.getTempoFormatado)
  const isSessionExpired = useVotacaoStore((state) => state.isSessionExpired)

  return {
    tempoRestante,
    timerAtivo,
    tempoFormatado: getTempoFormatado(),
    isExpired: isSessionExpired(),
    decrementar: decrementarTempo,
  }
}
