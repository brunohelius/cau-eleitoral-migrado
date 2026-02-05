import { Outlet } from 'react-router-dom'

export function AuthLayout() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
      <div className="w-full max-w-md p-8">
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold text-gray-900">CAU Sistema Eleitoral</h1>
          <p className="text-gray-600 mt-2">Painel Administrativo</p>
        </div>
        <Outlet />
      </div>
    </div>
  )
}
