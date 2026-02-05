// Auth Store
export * from './auth'
export { useAuthStore } from './auth'

// Eleicao Store
export * from './eleicao'
export { useEleicaoStore, useCurrentEleicao } from './eleicao'

// Notifications Store
export * from './notifications'
export { useNotificationsStore, useNotify } from './notifications'

// UI Store
export * from './ui'
export {
  useUIStore,
  useModal,
  useTheme,
  useSidebar,
  MODAL_IDS,
} from './ui'

// Chapa Store
export * from './chapaStore'
export {
  useChapaStore,
  useChapas,
  useChapaAtual,
  useChapaActions,
} from './chapaStore'

// Denuncia Store
export * from './denunciaStore'
export {
  useDenunciaStore,
  useDenuncias,
  useDenunciaAtual,
  useDenunciaActions,
  useDenunciaEstatisticas,
} from './denunciaStore'

// Impugnacao Store
export * from './impugnacaoStore'
export {
  useImpugnacaoStore,
  useImpugnacoes,
  useImpugnacaoAtual,
  useImpugnacaoActions,
  useImpugnacaoEstatisticas,
} from './impugnacaoStore'

// Usuario Store
export * from './usuarioStore'
export {
  useUsuarioStore,
  useUsuarios,
  useUsuarioAtual,
  useUsuarioActions,
  useRolesPermissoes,
  useUsuarioEstatisticas,
} from './usuarioStore'

// Configuracao Store
export * from './configuracaoStore'
export {
  useConfiguracaoStore,
  useConfiguracoes,
  useConfigEleicao,
  useConfigNotificacao,
  useConfigSeguranca,
  useConfigIntegracao,
  useConfigAparencia,
  useConfigBackups,
} from './configuracaoStore'
