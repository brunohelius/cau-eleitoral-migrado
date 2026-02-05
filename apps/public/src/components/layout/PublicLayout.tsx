import { useState } from 'react'
import { Outlet, Link, useLocation } from 'react-router-dom'
import { Menu, X } from 'lucide-react'

export function PublicLayout() {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const location = useLocation()

  const navigation = [
    { name: 'Inicio', href: '/' },
    { name: 'Eleicoes', href: '/eleicoes' },
    { name: 'Calendario', href: '/calendario' },
    { name: 'Documentos', href: '/documentos' },
    { name: 'FAQ', href: '/faq' },
  ]

  const isActive = (href: string) => {
    if (href === '/') return location.pathname === '/'
    return location.pathname.startsWith(href)
  }

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      {/* Header */}
      <header className="bg-white shadow-sm sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            {/* Logo */}
            <Link to="/" className="flex items-center">
              <span className="text-xl font-bold text-primary">CAU Sistema Eleitoral</span>
            </Link>

            {/* Desktop Navigation */}
            <nav className="hidden md:flex items-center space-x-8">
              {navigation.map((item) => (
                <Link
                  key={item.name}
                  to={item.href}
                  className={`text-sm font-medium transition-colors ${
                    isActive(item.href)
                      ? 'text-primary'
                      : 'text-gray-600 hover:text-gray-900'
                  }`}
                >
                  {item.name}
                </Link>
              ))}
            </nav>

            {/* Desktop Actions */}
            <div className="hidden md:flex items-center gap-3">
              <Link
                to="/candidato/login"
                className="text-sm font-medium text-gray-600 hover:text-gray-900"
              >
                Area do Candidato
              </Link>
              <Link
                to="/votacao"
                className="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary/90 text-sm font-medium"
              >
                Area do Eleitor
              </Link>
            </div>

            {/* Mobile menu button */}
            <button
              className="md:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              {mobileMenuOpen ? (
                <X className="h-6 w-6" />
              ) : (
                <Menu className="h-6 w-6" />
              )}
            </button>
          </div>
        </div>

        {/* Mobile Navigation */}
        {mobileMenuOpen && (
          <div className="md:hidden border-t bg-white">
            <div className="px-4 py-4 space-y-2">
              {navigation.map((item) => (
                <Link
                  key={item.name}
                  to={item.href}
                  onClick={() => setMobileMenuOpen(false)}
                  className={`block px-3 py-2 rounded-md text-base font-medium ${
                    isActive(item.href)
                      ? 'bg-primary/10 text-primary'
                      : 'text-gray-600 hover:bg-gray-100'
                  }`}
                >
                  {item.name}
                </Link>
              ))}
              <div className="pt-4 border-t space-y-2">
                <Link
                  to="/candidato/login"
                  onClick={() => setMobileMenuOpen(false)}
                  className="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100"
                >
                  Area do Candidato
                </Link>
                <Link
                  to="/votacao"
                  onClick={() => setMobileMenuOpen(false)}
                  className="block px-3 py-2 rounded-md text-base font-medium bg-primary text-white text-center"
                >
                  Area do Eleitor
                </Link>
              </div>
            </div>
          </div>
        )}
      </header>

      {/* Main */}
      <main className="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <Outlet />
      </main>

      {/* Footer */}
      <footer className="bg-gray-800 text-white mt-auto">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            {/* About */}
            <div className="md:col-span-1">
              <h3 className="text-lg font-semibold mb-4">CAU Sistema Eleitoral</h3>
              <p className="text-gray-400 text-sm">
                Sistema oficial de eleicoes do Conselho de Arquitetura e Urbanismo.
              </p>
            </div>

            {/* Quick Links */}
            <div>
              <h3 className="text-lg font-semibold mb-4">Links Rapidos</h3>
              <ul className="space-y-2 text-gray-400 text-sm">
                <li><Link to="/eleicoes" className="hover:text-white">Eleicoes</Link></li>
                <li><Link to="/calendario" className="hover:text-white">Calendario</Link></li>
                <li><Link to="/documentos" className="hover:text-white">Documentos</Link></li>
                <li><Link to="/faq" className="hover:text-white">FAQ</Link></li>
              </ul>
            </div>

            {/* Areas */}
            <div>
              <h3 className="text-lg font-semibold mb-4">Areas Restritas</h3>
              <ul className="space-y-2 text-gray-400 text-sm">
                <li><Link to="/votacao" className="hover:text-white">Area do Eleitor</Link></li>
                <li><Link to="/candidato/login" className="hover:text-white">Area do Candidato</Link></li>
              </ul>
            </div>

            {/* Contact */}
            <div>
              <h3 className="text-lg font-semibold mb-4">Contato</h3>
              <ul className="space-y-2 text-gray-400 text-sm">
                <li>suporte@cau.org.br</li>
                <li>0800-000-0000</li>
              </ul>
            </div>
          </div>

          <div className="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400 text-sm">
            <p>2024 CAU - Conselho de Arquitetura e Urbanismo. Todos os direitos reservados.</p>
          </div>
        </div>
      </footer>
    </div>
  )
}
