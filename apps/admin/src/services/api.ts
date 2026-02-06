import axios, { AxiosError, InternalAxiosRequestConfig } from 'axios'
import { useAuthStore } from '@/stores/auth'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
  },
})

// Request interceptor to add auth token
api.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const token = useAuthStore.getState().accessToken
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// Response interceptor to handle token refresh
api.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    const originalRequest = error.config as InternalAxiosRequestConfig & { _retry?: boolean }

    // If 401 and not already retrying
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true

      try {
        const refreshToken = useAuthStore.getState().refreshToken
        if (refreshToken) {
          const response = await axios.post('/api/auth/refresh-token', {
            refreshToken,
          })

          const { accessToken, refreshToken: newRefreshToken, user } = response.data
          useAuthStore.getState().setAuth(user, accessToken, newRefreshToken)

          originalRequest.headers.Authorization = `Bearer ${accessToken}`
          return api(originalRequest)
        }
      } catch (refreshError) {
        useAuthStore.getState().logout()
        window.location.href = '/login'
      }
    }

    return Promise.reject(error)
  }
)

// Helper to map API paginated response (items/totalCount) to frontend format (data/total)
export function mapPagedResponse<T>(apiResponse: Record<string, unknown>): {
  data: T[]
  total: number
  page: number
  pageSize: number
  totalPages: number
} {
  return {
    data: (apiResponse.items as T[]) || [],
    total: (apiResponse.totalCount as number) || 0,
    page: (apiResponse.page as number) || 1,
    pageSize: (apiResponse.pageSize as number) || 10,
    totalPages: (apiResponse.totalPages as number) || 1,
  }
}

export default api
