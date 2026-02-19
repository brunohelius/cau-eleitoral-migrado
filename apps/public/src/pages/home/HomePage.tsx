import { Link } from 'react-router-dom'
import { Vote, Calendar, Users, CheckCircle } from 'lucide-react'

export function HomePage() {
  return (
    <div className="space-y-12">
      {/* Hero */}
      <section className="text-center py-12">
        <h1 className="text-4xl font-bold text-gray-900 mb-4">
          Sistema Eleitoral CAU
        </h1>
        <p className="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
          Participe das eleicoes do Conselho de Arquitetura e Urbanismo.
          Vote de forma segura e transparente.
        </p>
        <div className="flex justify-center gap-4">
          <Link
            to="/votacao"
            className="bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primary/90"
          >
            Votar Agora
          </Link>
          <Link
            to="/eleicoes"
            className="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-50"
          >
            Ver Eleicoes
          </Link>
        </div>
      </section>

      {/* Features */}
      <section className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div className="bg-white p-6 rounded-lg shadow-sm">
          <Vote className="h-10 w-10 text-primary mb-4" />
          <h3 className="text-lg font-semibold mb-2">Votação Online</h3>
          <p className="text-gray-600">Vote de qualquer lugar com segurança e praticidade.</p>
        </div>
        <div className="bg-white p-6 rounded-lg shadow-sm">
          <Calendar className="h-10 w-10 text-primary mb-4" />
          <h3 className="text-lg font-semibold mb-2">Calendário Eleitoral</h3>
          <p className="text-gray-600">Acompanhe todas as datas importantes das eleições.</p>
        </div>
        <div className="bg-white p-6 rounded-lg shadow-sm">
          <Users className="h-10 w-10 text-primary mb-4" />
          <h3 className="text-lg font-semibold mb-2">Chapas</h3>
          <p className="text-gray-600">Conheça as chapas e candidatos de cada eleição.</p>
        </div>
        <div className="bg-white p-6 rounded-lg shadow-sm">
          <CheckCircle className="h-10 w-10 text-primary mb-4" />
          <h3 className="text-lg font-semibold mb-2">Resultados</h3>
          <p className="text-gray-600">Acompanhe os resultados em tempo real.</p>
        </div>
      </section>

      {/* CTA */}
      <section className="bg-primary text-white rounded-xl p-8 text-center">
        <h2 className="text-2xl font-bold mb-4">Pronto para votar?</h2>
        <p className="mb-6">Acesse a área do eleitor e exerça seu direito ao voto.</p>
        <Link
          to="/votacao"
          className="bg-white text-primary px-6 py-3 rounded-lg font-medium hover:bg-gray-100 inline-block"
        >
          Acessar Area do Eleitor
        </Link>
      </section>
    </div>
  )
}
