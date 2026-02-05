import { create } from 'zustand'
import { persist } from 'zustand/middleware'

export type NotificationType = 'success' | 'error' | 'warning' | 'info'

export interface Notification {
  id: string
  type: NotificationType
  title: string
  message?: string
  duration?: number // in milliseconds, null = persistent
  dismissible?: boolean
  action?: {
    label: string
    onClick: () => void
  }
  createdAt: number
}

export interface SystemAlert {
  id: string
  type: 'info' | 'warning' | 'error' | 'success'
  title: string
  message: string
  link?: {
    label: string
    url: string
  }
  read: boolean
  createdAt: string
}

interface NotificationsState {
  // Toast notifications (ephemeral)
  notifications: Notification[]

  // System alerts (persistent)
  systemAlerts: SystemAlert[]
  unreadCount: number

  // Actions for toast notifications
  addNotification: (notification: Omit<Notification, 'id' | 'createdAt'>) => string
  removeNotification: (id: string) => void
  clearNotifications: () => void

  // Convenience methods
  success: (title: string, message?: string) => string
  error: (title: string, message?: string) => string
  warning: (title: string, message?: string) => string
  info: (title: string, message?: string) => string

  // Actions for system alerts
  setSystemAlerts: (alerts: SystemAlert[]) => void
  addSystemAlert: (alert: Omit<SystemAlert, 'id' | 'createdAt'>) => void
  markAlertAsRead: (id: string) => void
  markAllAlertsAsRead: () => void
  removeSystemAlert: (id: string) => void
  clearSystemAlerts: () => void
}

const generateId = () => Math.random().toString(36).substring(2, 9)

const DEFAULT_DURATION = 5000 // 5 seconds

export const useNotificationsStore = create<NotificationsState>()(
  persist(
    (set, get) => ({
      notifications: [],
      systemAlerts: [],
      unreadCount: 0,

      addNotification: (notification) => {
        const id = generateId()
        const newNotification: Notification = {
          ...notification,
          id,
          createdAt: Date.now(),
          dismissible: notification.dismissible ?? true,
          duration: notification.duration ?? DEFAULT_DURATION,
        }

        set((state) => ({
          notifications: [...state.notifications, newNotification],
        }))

        // Auto-remove after duration if set
        if (newNotification.duration && newNotification.duration > 0) {
          setTimeout(() => {
            get().removeNotification(id)
          }, newNotification.duration)
        }

        return id
      },

      removeNotification: (id) =>
        set((state) => ({
          notifications: state.notifications.filter((n) => n.id !== id),
        })),

      clearNotifications: () =>
        set({
          notifications: [],
        }),

      success: (title, message) =>
        get().addNotification({ type: 'success', title, message }),

      error: (title, message) =>
        get().addNotification({ type: 'error', title, message, duration: 8000 }),

      warning: (title, message) =>
        get().addNotification({ type: 'warning', title, message, duration: 6000 }),

      info: (title, message) =>
        get().addNotification({ type: 'info', title, message }),

      setSystemAlerts: (alerts) =>
        set({
          systemAlerts: alerts,
          unreadCount: alerts.filter((a) => !a.read).length,
        }),

      addSystemAlert: (alert) => {
        const newAlert: SystemAlert = {
          ...alert,
          id: generateId(),
          createdAt: new Date().toISOString(),
        }

        set((state) => ({
          systemAlerts: [newAlert, ...state.systemAlerts],
          unreadCount: state.unreadCount + (newAlert.read ? 0 : 1),
        }))
      },

      markAlertAsRead: (id) =>
        set((state) => {
          const alert = state.systemAlerts.find((a) => a.id === id)
          if (!alert || alert.read) return state

          return {
            systemAlerts: state.systemAlerts.map((a) =>
              a.id === id ? { ...a, read: true } : a
            ),
            unreadCount: Math.max(0, state.unreadCount - 1),
          }
        }),

      markAllAlertsAsRead: () =>
        set((state) => ({
          systemAlerts: state.systemAlerts.map((a) => ({ ...a, read: true })),
          unreadCount: 0,
        })),

      removeSystemAlert: (id) =>
        set((state) => {
          const alert = state.systemAlerts.find((a) => a.id === id)
          return {
            systemAlerts: state.systemAlerts.filter((a) => a.id !== id),
            unreadCount: alert && !alert.read
              ? Math.max(0, state.unreadCount - 1)
              : state.unreadCount,
          }
        }),

      clearSystemAlerts: () =>
        set({
          systemAlerts: [],
          unreadCount: 0,
        }),
    }),
    {
      name: 'notifications-storage',
      partialize: (state) => ({
        systemAlerts: state.systemAlerts,
        unreadCount: state.unreadCount,
      }),
    }
  )
)

// Helper hook for common notification patterns
export const useNotify = () => {
  const { success, error, warning, info } = useNotificationsStore()
  return { success, error, warning, info }
}
