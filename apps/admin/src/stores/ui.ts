import { create } from 'zustand'
import { persist } from 'zustand/middleware'

export type Theme = 'light' | 'dark' | 'system'

export interface ModalState {
  isOpen: boolean
  data?: unknown
}

export interface BreadcrumbItem {
  label: string
  href?: string
  icon?: string
}

interface UIState {
  // Sidebar
  sidebarOpen: boolean
  sidebarCollapsed: boolean

  // Theme
  theme: Theme

  // Modals registry
  modals: Record<string, ModalState>

  // Page state
  pageTitle: string
  breadcrumbs: BreadcrumbItem[]

  // Loading overlay
  globalLoading: boolean
  globalLoadingMessage?: string

  // Command palette
  commandPaletteOpen: boolean

  // Mobile
  isMobile: boolean
  mobileMenuOpen: boolean

  // Actions - Sidebar
  toggleSidebar: () => void
  setSidebarOpen: (open: boolean) => void
  toggleSidebarCollapse: () => void
  setSidebarCollapsed: (collapsed: boolean) => void

  // Actions - Theme
  setTheme: (theme: Theme) => void

  // Actions - Modals
  openModal: (modalId: string, data?: unknown) => void
  closeModal: (modalId: string) => void
  toggleModal: (modalId: string, data?: unknown) => void
  isModalOpen: (modalId: string) => boolean
  getModalData: <T>(modalId: string) => T | undefined
  closeAllModals: () => void

  // Actions - Page
  setPageTitle: (title: string) => void
  setBreadcrumbs: (breadcrumbs: BreadcrumbItem[]) => void

  // Actions - Global Loading
  setGlobalLoading: (loading: boolean, message?: string) => void

  // Actions - Command Palette
  toggleCommandPalette: () => void
  setCommandPaletteOpen: (open: boolean) => void

  // Actions - Mobile
  setIsMobile: (isMobile: boolean) => void
  toggleMobileMenu: () => void
  setMobileMenuOpen: (open: boolean) => void
}

export const useUIStore = create<UIState>()(
  persist(
    (set, get) => ({
      // Initial state
      sidebarOpen: true,
      sidebarCollapsed: false,
      theme: 'system',
      modals: {},
      pageTitle: '',
      breadcrumbs: [],
      globalLoading: false,
      globalLoadingMessage: undefined,
      commandPaletteOpen: false,
      isMobile: false,
      mobileMenuOpen: false,

      // Sidebar actions
      toggleSidebar: () =>
        set((state) => ({
          sidebarOpen: !state.sidebarOpen,
        })),

      setSidebarOpen: (open) =>
        set({
          sidebarOpen: open,
        }),

      toggleSidebarCollapse: () =>
        set((state) => ({
          sidebarCollapsed: !state.sidebarCollapsed,
        })),

      setSidebarCollapsed: (collapsed) =>
        set({
          sidebarCollapsed: collapsed,
        }),

      // Theme actions
      setTheme: (theme) => {
        set({ theme })

        // Apply theme to document
        const root = document.documentElement
        root.classList.remove('light', 'dark')

        if (theme === 'system') {
          const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches
            ? 'dark'
            : 'light'
          root.classList.add(systemTheme)
        } else {
          root.classList.add(theme)
        }
      },

      // Modal actions
      openModal: (modalId, data) =>
        set((state) => ({
          modals: {
            ...state.modals,
            [modalId]: { isOpen: true, data },
          },
        })),

      closeModal: (modalId) =>
        set((state) => ({
          modals: {
            ...state.modals,
            [modalId]: { isOpen: false, data: undefined },
          },
        })),

      toggleModal: (modalId, data) =>
        set((state) => {
          const currentState = state.modals[modalId]
          return {
            modals: {
              ...state.modals,
              [modalId]: {
                isOpen: !currentState?.isOpen,
                data: !currentState?.isOpen ? data : undefined,
              },
            },
          }
        }),

      isModalOpen: (modalId) => get().modals[modalId]?.isOpen ?? false,

      getModalData: <T>(modalId: string) => get().modals[modalId]?.data as T | undefined,

      closeAllModals: () =>
        set((state) => ({
          modals: Object.fromEntries(
            Object.keys(state.modals).map((key) => [
              key,
              { isOpen: false, data: undefined },
            ])
          ),
        })),

      // Page actions
      setPageTitle: (title) =>
        set({
          pageTitle: title,
        }),

      setBreadcrumbs: (breadcrumbs) =>
        set({
          breadcrumbs,
        }),

      // Global loading actions
      setGlobalLoading: (loading, message) =>
        set({
          globalLoading: loading,
          globalLoadingMessage: loading ? message : undefined,
        }),

      // Command palette actions
      toggleCommandPalette: () =>
        set((state) => ({
          commandPaletteOpen: !state.commandPaletteOpen,
        })),

      setCommandPaletteOpen: (open) =>
        set({
          commandPaletteOpen: open,
        }),

      // Mobile actions
      setIsMobile: (isMobile) =>
        set({
          isMobile,
          sidebarOpen: !isMobile, // Auto-close sidebar on mobile
        }),

      toggleMobileMenu: () =>
        set((state) => ({
          mobileMenuOpen: !state.mobileMenuOpen,
        })),

      setMobileMenuOpen: (open) =>
        set({
          mobileMenuOpen: open,
        }),
    }),
    {
      name: 'ui-storage',
      partialize: (state) => ({
        sidebarCollapsed: state.sidebarCollapsed,
        theme: state.theme,
      }),
    }
  )
)

// Modal IDs constants for type safety
export const MODAL_IDS = {
  CONFIRM_DELETE: 'confirm-delete',
  CREATE_ELEICAO: 'create-eleicao',
  EDIT_ELEICAO: 'edit-eleicao',
  CREATE_CHAPA: 'create-chapa',
  EDIT_CHAPA: 'edit-chapa',
  CREATE_USUARIO: 'create-usuario',
  EDIT_USUARIO: 'edit-usuario',
  VIEW_DOCUMENTO: 'view-documento',
  UPLOAD_DOCUMENTO: 'upload-documento',
  FILTER_PANEL: 'filter-panel',
  EXPORT_DATA: 'export-data',
} as const

// Helper hooks
export const useModal = (modalId: string) => {
  const { openModal, closeModal, isModalOpen, getModalData } = useUIStore()

  return {
    isOpen: isModalOpen(modalId),
    data: getModalData(modalId),
    open: (data?: unknown) => openModal(modalId, data),
    close: () => closeModal(modalId),
  }
}

export const useTheme = () => {
  const theme = useUIStore((state) => state.theme)
  const setTheme = useUIStore((state) => state.setTheme)

  return { theme, setTheme }
}

export const useSidebar = () => {
  const sidebarOpen = useUIStore((state) => state.sidebarOpen)
  const sidebarCollapsed = useUIStore((state) => state.sidebarCollapsed)
  const toggleSidebar = useUIStore((state) => state.toggleSidebar)
  const toggleSidebarCollapse = useUIStore((state) => state.toggleSidebarCollapse)

  return {
    isOpen: sidebarOpen,
    isCollapsed: sidebarCollapsed,
    toggle: toggleSidebar,
    toggleCollapse: toggleSidebarCollapse,
  }
}
