import { useState } from 'react'
import { Outlet, Link, useLocation, useNavigate } from 'react-router-dom'
import {
  Users,
  FileText,
  Megaphone,
  AlertTriangle,
  Shield,
  Scale,
  History,
  User,
  LogOut,
  Menu,
  X,
  ChevronDown,
} from 'lucide-react'
import { cn } from '@/lib/utils'

const navigation = [
  { name: 'Minha Chapa', href: '/candidato', icon: Users },
  { name: 'Documentos', href: '/candidato/documentos', icon: FileText },
  { name: 'Plataforma', href: '/candidato/plataforma', icon: Megaphone },
  { name: 'Denuncias', href: '/candidato/denuncias', icon: AlertTriangle },
  { name: 'Defesas', href: '/candidato/defesas', icon: Shield },
  { name: 'Recursos', href: '/candidato/recursos', icon: Scale },
  { name: 'Historico', href: '/candidato/historico', icon: History },
  { name: 'Perfil', href: '/candidato/perfil', icon: User },
]

export function CandidateLayout() {
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const [userMenuOpen, setUserMenuOpen] = useState(false)
  const location = useLocation()
  const navigate = useNavigate()

  // Mock user data - replace with actual auth store
  const user = {
    nome: 'Maria Santos',
    cpf: '***.***.***-00',
    cau: 'A54321-0',
    chapa: 'Chapa Renovacao',
    cargo: 'Presidente',
  }

  const handleLogout = () => {
    // TODO: Implement actual logout
    navigate('/candidato/login')
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Mobile sidebar backdrop */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* Mobile sidebar */}
      <div
        className={cn(
          'fixed inset-y-0 left-0 z-50 w-64 bg-white transform transition-transform duration-300 ease-in-out lg:hidden',
          sidebarOpen ? 'translate-x-0' : '-translate-x-full'
        )}
      >
        <div className="flex h-16 items-center justify-between px-4 border-b">
          <span className="text-xl font-bold text-primary">Area do Candidato</span>
          <button
            onClick={() => setSidebarOpen(false)}
            className="p-2 rounded-md text-gray-500 hover:bg-gray-100"
          >
            <X className="h-5 w-5" />
          </button>
        </div>
        <div className="px-4 py-3 border-b bg-blue-50">
          <p className="text-sm font-medium text-gray-900">{user.chapa}</p>
          <p className="text-xs text-gray-600">{user.cargo}</p>
        </div>
        <nav className="flex-1 space-y-1 px-2 py-4 overflow-y-auto">
          {navigation.map((item) => (
            <Link
              key={item.name}
              to={item.href}
              onClick={() => setSidebarOpen(false)}
              className={cn(
                'group flex items-center px-3 py-2 text-sm font-medium rounded-md',
                location.pathname === item.href
                  ? 'bg-primary text-white'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
              )}
            >
              <item.icon className="mr-3 h-5 w-5" />
              {item.name}
            </Link>
          ))}
        </nav>
        <div className="border-t p-4">
          <button
            onClick={handleLogout}
            className="flex items-center w-full px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md"
          >
            <LogOut className="mr-3 h-5 w-5" />
            Sair
          </button>
        </div>
      </div>

      {/* Desktop sidebar */}
      <div className="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
        <div className="flex min-h-0 flex-1 flex-col border-r border-gray-200 bg-white">
          <div className="flex h-16 shrink-0 items-center px-4 border-b">
            <Link to="/" className="text-xl font-bold text-primary">
              CAU Eleitoral
            </Link>
          </div>
          <div className="px-4 py-4 border-b bg-blue-50">
            <p className="text-sm font-medium text-gray-900">{user.chapa}</p>
            <p className="text-xs text-gray-600">{user.cargo}: {user.nome}</p>
            <p className="text-xs text-gray-500">CAU: {user.cau}</p>
          </div>
          <nav className="flex-1 space-y-1 px-2 py-4 overflow-y-auto">
            {navigation.map((item) => (
              <Link
                key={item.name}
                to={item.href}
                className={cn(
                  'group flex items-center px-3 py-2 text-sm font-medium rounded-md',
                  location.pathname === item.href
                    ? 'bg-primary text-white'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                )}
              >
                <item.icon className="mr-3 h-5 w-5" />
                {item.name}
              </Link>
            ))}
          </nav>
          <div className="border-t p-4">
            <button
              onClick={handleLogout}
              className="flex items-center w-full px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md"
            >
              <LogOut className="mr-3 h-5 w-5" />
              Sair
            </button>
          </div>
        </div>
      </div>

      {/* Main content */}
      <div className="lg:pl-64">
        {/* Top bar */}
        <header className="sticky top-0 z-30 flex h-16 items-center gap-4 border-b bg-white px-4 shadow-sm">
          <button
            className="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100"
            onClick={() => setSidebarOpen(true)}
          >
            <Menu className="h-5 w-5" />
          </button>

          <div className="flex-1">
            <h1 className="text-lg font-semibold text-gray-900 lg:hidden">
              Area do Candidato
            </h1>
          </div>

          {/* User menu */}
          <div className="relative">
            <button
              onClick={() => setUserMenuOpen(!userMenuOpen)}
              className="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md"
            >
              <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <User className="h-4 w-4 text-blue-600" />
              </div>
              <span className="hidden sm:block">{user.nome}</span>
              <ChevronDown className="h-4 w-4" />
            </button>

            {userMenuOpen && (
              <>
                <div
                  className="fixed inset-0 z-40"
                  onClick={() => setUserMenuOpen(false)}
                />
                <div className="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border z-50">
                  <div className="py-1">
                    <Link
                      to="/candidato/perfil"
                      onClick={() => setUserMenuOpen(false)}
                      className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Meu Perfil
                    </Link>
                    <Link
                      to="/"
                      onClick={() => setUserMenuOpen(false)}
                      className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Site Publico
                    </Link>
                    <button
                      onClick={handleLogout}
                      className="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                    >
                      Sair
                    </button>
                  </div>
                </div>
              </>
            )}
          </div>
        </header>

        {/* Page content */}
        <main className="p-4 sm:p-6 lg:p-8">
          <Outlet />
        </main>

        {/* Footer */}
        <footer className="border-t bg-white px-4 py-4 text-center text-sm text-gray-500">
          <p>2024 CAU Sistema Eleitoral. Todos os direitos reservados.</p>
        </footer>
      </div>
    </div>
  )
}
