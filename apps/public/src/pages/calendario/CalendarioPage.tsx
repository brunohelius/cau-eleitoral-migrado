import { useState } from 'react'
import { Link } from 'react-router-dom'
import {
  ChevronLeft,
  ChevronRight,
  Clock,
  MapPin,
  Info,
  Filter,
  Loader2,
  CalendarDays,
} from 'lucide-react'

// Types
interface EventoCalendario {
  id: string
  titulo: string
  descricao: string
  dataInicio: string
  dataFim: string
  tipo: 'inscricao' | 'votacao' | 'apuracao' | 'prazo' | 'reuniao' | 'outro'
  eleicaoId?: string
  eleicaoNome?: string
  local?: string
  destaque: boolean
}

// Mock data
const mockEventos: EventoCalendario[] = [
  {
    id: '1',
    titulo: 'Abertura das Inscricoes',
    descricao: 'Inicio do periodo de inscricao de chapas para a Eleicao Ordinaria 2024',
    dataInicio: '2024-01-20',
    dataFim: '2024-02-20',
    tipo: 'inscricao',
    eleicaoId: '1',
    eleicaoNome: 'Eleicao Ordinaria 2024',
    destaque: true,
  },
  {
    id: '2',
    titulo: 'Prazo Final para Documentacao',
    descricao: 'Ultimo dia para entrega de documentos complementares das chapas',
    dataInicio: '2024-02-25',
    dataFim: '2024-02-25',
    tipo: 'prazo',
    eleicaoId: '1',
    eleicaoNome: 'Eleicao Ordinaria 2024',
    destaque: false,
  },
  {
    id: '3',
    titulo: 'Periodo de Votacao',
    descricao: 'Votacao online para todos os eleitores aptos',
    dataInicio: '2024-03-15',
    dataFim: '2024-03-22',
    tipo: 'votacao',
    eleicaoId: '1',
    eleicaoNome: 'Eleicao Ordinaria 2024',
    destaque: true,
  },
  {
    id: '4',
    titulo: 'Apuracao dos Votos',
    descricao: 'Contagem e validacao dos votos pela Comissao Eleitoral',
    dataInicio: '2024-03-23',
    dataFim: '2024-03-25',
    tipo: 'apuracao',
    eleicaoId: '1',
    eleicaoNome: 'Eleicao Ordinaria 2024',
    destaque: false,
  },
  {
    id: '5',
    titulo: 'Reuniao da Comissao Eleitoral',
    descricao: 'Reuniao ordinaria para deliberacao sobre recursos',
    dataInicio: '2024-03-10',
    dataFim: '2024-03-10',
    tipo: 'reuniao',
    local: 'Sede do CAU/SP - Sala de Reunioes',
    destaque: false,
  },
  {
    id: '6',
    titulo: 'Divulgacao do Resultado Oficial',
    descricao: 'Publicacao do resultado oficial da eleicao',
    dataInicio: '2024-03-26',
    dataFim: '2024-03-26',
    tipo: 'outro',
    eleicaoId: '1',
    eleicaoNome: 'Eleicao Ordinaria 2024',
    destaque: true,
  },
]

const tipoConfig = {
  inscricao: { label: 'Inscricao', color: 'bg-blue-100 text-blue-800 border-blue-200' },
  votacao: { label: 'Votacao', color: 'bg-green-100 text-green-800 border-green-200' },
  apuracao: { label: 'Apuracao', color: 'bg-purple-100 text-purple-800 border-purple-200' },
  prazo: { label: 'Prazo', color: 'bg-red-100 text-red-800 border-red-200' },
  reuniao: { label: 'Reuniao', color: 'bg-yellow-100 text-yellow-800 border-yellow-200' },
  outro: { label: 'Evento', color: 'bg-gray-100 text-gray-800 border-gray-200' },
}

const meses = [
  'Janeiro', 'Fevereiro', 'Marco', 'Abril', 'Maio', 'Junho',
  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
]

export function CalendarioPage() {
  const [selectedMonth, setSelectedMonth] = useState(new Date().getMonth())
  const [selectedYear, setSelectedYear] = useState(new Date().getFullYear())
  const [selectedTipo, setSelectedTipo] = useState<string | null>(null)
  const [viewMode, setViewMode] = useState<'list' | 'calendar'>('list')
  const [isLoading] = useState(false)

  // Filter events
  const filteredEventos = mockEventos.filter(evento => {
    const eventoDate = new Date(evento.dataInicio)
    const matchesMonth = eventoDate.getMonth() === selectedMonth && eventoDate.getFullYear() === selectedYear
    const matchesTipo = !selectedTipo || evento.tipo === selectedTipo

    // For list view, show events from selected month
    // For calendar view, show all events in the year
    if (viewMode === 'list') {
      return matchesMonth && matchesTipo
    }
    return eventoDate.getFullYear() === selectedYear && matchesTipo
  })

  const goToPreviousMonth = () => {
    if (selectedMonth === 0) {
      setSelectedMonth(11)
      setSelectedYear(selectedYear - 1)
    } else {
      setSelectedMonth(selectedMonth - 1)
    }
  }

  const goToNextMonth = () => {
    if (selectedMonth === 11) {
      setSelectedMonth(0)
      setSelectedYear(selectedYear + 1)
    } else {
      setSelectedMonth(selectedMonth + 1)
    }
  }

  const goToToday = () => {
    setSelectedMonth(new Date().getMonth())
    setSelectedYear(new Date().getFullYear())
  }

  // Get events for a specific day
  const getEventsForDay = (day: number) => {
    const date = new Date(selectedYear, selectedMonth, day)
    return filteredEventos.filter(evento => {
      const start = new Date(evento.dataInicio)
      const end = new Date(evento.dataFim)
      return date >= start && date <= end
    })
  }

  // Generate calendar days
  const generateCalendarDays = () => {
    const firstDay = new Date(selectedYear, selectedMonth, 1).getDay()
    const daysInMonth = new Date(selectedYear, selectedMonth + 1, 0).getDate()
    const days: (number | null)[] = []

    // Add empty cells for days before the first day of the month
    for (let i = 0; i < firstDay; i++) {
      days.push(null)
    }

    // Add days of the month
    for (let i = 1; i <= daysInMonth; i++) {
      days.push(i)
    }

    return days
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando calendario...</span>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
            Calendario Eleitoral
          </h1>
          <p className="text-gray-600 mt-1">
            Acompanhe todas as datas importantes do processo eleitoral
          </p>
        </div>
        <div className="flex items-center gap-2">
          <button
            onClick={() => setViewMode('list')}
            className={`px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
              viewMode === 'list'
                ? 'bg-primary text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            }`}
          >
            Lista
          </button>
          <button
            onClick={() => setViewMode('calendar')}
            className={`px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
              viewMode === 'calendar'
                ? 'bg-primary text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            }`}
          >
            Calendario
          </button>
        </div>
      </div>

      {/* Navigation and Filters */}
      <div className="bg-white p-4 rounded-lg shadow-sm border">
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          {/* Month Navigation */}
          <div className="flex items-center gap-4">
            <button
              onClick={goToPreviousMonth}
              className="p-2 hover:bg-gray-100 rounded-lg"
            >
              <ChevronLeft className="h-5 w-5" />
            </button>
            <h2 className="text-lg font-semibold text-gray-900 min-w-[180px] text-center">
              {meses[selectedMonth]} {selectedYear}
            </h2>
            <button
              onClick={goToNextMonth}
              className="p-2 hover:bg-gray-100 rounded-lg"
            >
              <ChevronRight className="h-5 w-5" />
            </button>
            <button
              onClick={goToToday}
              className="px-3 py-1 text-sm text-primary hover:bg-primary/10 rounded-lg"
            >
              Hoje
            </button>
          </div>

          {/* Filter */}
          <div className="flex items-center gap-2">
            <Filter className="h-4 w-4 text-gray-500" />
            <select
              value={selectedTipo || ''}
              onChange={(e) => setSelectedTipo(e.target.value || null)}
              className="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary"
            >
              <option value="">Todos os tipos</option>
              {Object.entries(tipoConfig).map(([key, config]) => (
                <option key={key} value={key}>{config.label}</option>
              ))}
            </select>
          </div>
        </div>
      </div>

      {/* Calendar View */}
      {viewMode === 'calendar' && (
        <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
          {/* Days Header */}
          <div className="grid grid-cols-7 border-b">
            {['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'].map(day => (
              <div key={day} className="p-3 text-center text-sm font-medium text-gray-500 bg-gray-50">
                {day}
              </div>
            ))}
          </div>

          {/* Calendar Grid */}
          <div className="grid grid-cols-7">
            {generateCalendarDays().map((day, index) => {
              const events = day ? getEventsForDay(day) : []
              const isToday = day === new Date().getDate() &&
                selectedMonth === new Date().getMonth() &&
                selectedYear === new Date().getFullYear()

              return (
                <div
                  key={index}
                  className={`min-h-[100px] p-2 border-b border-r ${
                    day ? 'bg-white' : 'bg-gray-50'
                  }`}
                >
                  {day && (
                    <>
                      <span className={`inline-flex items-center justify-center w-7 h-7 text-sm ${
                        isToday
                          ? 'bg-primary text-white rounded-full font-bold'
                          : 'text-gray-900'
                      }`}>
                        {day}
                      </span>
                      <div className="mt-1 space-y-1">
                        {events.slice(0, 2).map(evento => (
                          <div
                            key={evento.id}
                            className={`text-xs p-1 rounded truncate ${tipoConfig[evento.tipo].color}`}
                            title={evento.titulo}
                          >
                            {evento.titulo}
                          </div>
                        ))}
                        {events.length > 2 && (
                          <span className="text-xs text-gray-500">
                            +{events.length - 2} mais
                          </span>
                        )}
                      </div>
                    </>
                  )}
                </div>
              )
            })}
          </div>
        </div>
      )}

      {/* List View */}
      {viewMode === 'list' && (
        <div className="space-y-4">
          {filteredEventos.length === 0 ? (
            <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
              <CalendarDays className="h-12 w-12 text-gray-400 mx-auto mb-4" />
              <p className="text-gray-500">Nenhum evento encontrado para este periodo</p>
            </div>
          ) : (
            filteredEventos.map(evento => {
              const config = tipoConfig[evento.tipo]
              const dataInicio = new Date(evento.dataInicio)
              const dataFim = new Date(evento.dataFim)
              const isSingleDay = evento.dataInicio === evento.dataFim

              return (
                <div
                  key={evento.id}
                  className={`bg-white rounded-lg shadow-sm border overflow-hidden ${
                    evento.destaque ? 'ring-2 ring-primary ring-offset-2' : ''
                  }`}
                >
                  <div className="flex">
                    {/* Date Badge */}
                    <div className="w-20 sm:w-24 flex-shrink-0 bg-gray-50 p-4 flex flex-col items-center justify-center border-r">
                      <span className="text-2xl sm:text-3xl font-bold text-primary">
                        {dataInicio.getDate()}
                      </span>
                      <span className="text-xs sm:text-sm text-gray-500 uppercase">
                        {meses[dataInicio.getMonth()].substring(0, 3)}
                      </span>
                    </div>

                    {/* Content */}
                    <div className="flex-1 p-4">
                      <div className="flex flex-wrap items-start gap-2 mb-2">
                        <span className={`px-2 py-0.5 text-xs font-medium rounded border ${config.color}`}>
                          {config.label}
                        </span>
                        {evento.destaque && (
                          <span className="px-2 py-0.5 text-xs font-medium bg-primary/10 text-primary rounded">
                            Destaque
                          </span>
                        )}
                      </div>

                      <h3 className="text-lg font-semibold text-gray-900">{evento.titulo}</h3>
                      <p className="text-gray-600 text-sm mt-1">{evento.descricao}</p>

                      <div className="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                        <div className="flex items-center gap-1">
                          <Clock className="h-4 w-4" />
                          {isSingleDay ? (
                            <span>{dataInicio.toLocaleDateString('pt-BR')}</span>
                          ) : (
                            <span>
                              {dataInicio.toLocaleDateString('pt-BR')} a {dataFim.toLocaleDateString('pt-BR')}
                            </span>
                          )}
                        </div>

                        {evento.local && (
                          <div className="flex items-center gap-1">
                            <MapPin className="h-4 w-4" />
                            <span>{evento.local}</span>
                          </div>
                        )}

                        {evento.eleicaoNome && (
                          <Link
                            to={`/eleicoes/${evento.eleicaoId}`}
                            className="flex items-center gap-1 text-primary hover:underline"
                          >
                            <Info className="h-4 w-4" />
                            <span>{evento.eleicaoNome}</span>
                          </Link>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              )
            })
          )}
        </div>
      )}

      {/* Legend */}
      <div className="bg-white p-4 rounded-lg shadow-sm border">
        <h3 className="text-sm font-medium text-gray-700 mb-3">Legenda</h3>
        <div className="flex flex-wrap gap-3">
          {Object.entries(tipoConfig).map(([key, config]) => (
            <span
              key={key}
              className={`px-3 py-1 text-xs font-medium rounded border ${config.color}`}
            >
              {config.label}
            </span>
          ))}
        </div>
      </div>
    </div>
  )
}
