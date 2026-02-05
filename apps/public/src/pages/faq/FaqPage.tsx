import { useState } from 'react'
import { Link } from 'react-router-dom'
import {
  HelpCircle,
  ChevronDown,
  ChevronUp,
  Search,
  MessageCircle,
  Phone,
  Mail,
  ExternalLink,
  Vote,
  Users,
  Calendar,
  FileText,
  Shield,
  AlertTriangle,
} from 'lucide-react'

// Types
interface FaqItem {
  id: string
  pergunta: string
  resposta: string
  categoria: string
}

interface FaqCategoria {
  id: string
  nome: string
  icon: React.ComponentType<{ className?: string }>
}

// Categories
const categorias: FaqCategoria[] = [
  { id: 'votacao', nome: 'Votacao', icon: Vote },
  { id: 'inscricao', nome: 'Inscricao', icon: Users },
  { id: 'calendario', nome: 'Calendario', icon: Calendar },
  { id: 'documentos', nome: 'Documentos', icon: FileText },
  { id: 'seguranca', nome: 'Seguranca', icon: Shield },
  { id: 'problemas', nome: 'Problemas', icon: AlertTriangle },
]

// Mock data
const mockFaqs: FaqItem[] = [
  // Votacao
  {
    id: '1',
    pergunta: 'Como faco para votar?',
    resposta: 'Para votar, acesse a Area do Eleitor no site, identifique-se com seu CPF e numero de registro no CAU. Apos a autenticacao, selecione a eleicao desejada e escolha a chapa de sua preferencia. Confirme seu voto e guarde o comprovante gerado.',
    categoria: 'votacao',
  },
  {
    id: '2',
    pergunta: 'Posso votar pelo celular?',
    resposta: 'Sim! O sistema de votacao e responsivo e funciona em dispositivos moveis. Voce pode acessar pelo navegador do seu celular ou tablet e realizar a votacao normalmente.',
    categoria: 'votacao',
  },
  {
    id: '3',
    pergunta: 'Meu voto e sigiloso?',
    resposta: 'Sim, seu voto e totalmente sigiloso. O sistema utiliza criptografia de ponta a ponta e nao armazena nenhuma relacao entre o eleitor e o voto escolhido. Apenas e registrado que voce votou, mas nao em quem.',
    categoria: 'votacao',
  },
  {
    id: '4',
    pergunta: 'Posso mudar meu voto apos confirmar?',
    resposta: 'Nao. Apos a confirmacao, o voto e computado e nao pode ser alterado. Por isso, revise sua escolha com atencao antes de confirmar.',
    categoria: 'votacao',
  },
  {
    id: '5',
    pergunta: 'O que e o voto em branco?',
    resposta: 'O voto em branco e uma opcao para o eleitor que nao deseja escolher nenhuma das chapas. Este voto e contabilizado separadamente e nao e considerado como voto valido para fins de apuracao.',
    categoria: 'votacao',
  },
  // Inscricao
  {
    id: '6',
    pergunta: 'Como me inscrevo como candidato?',
    resposta: 'Para se inscrever como candidato, voce deve fazer parte de uma chapa registrada. A inscricao de chapas e feita durante o periodo de inscricoes, conforme estabelecido no edital. E necessario preencher o formulario de inscricao e anexar todos os documentos exigidos.',
    categoria: 'inscricao',
  },
  {
    id: '7',
    pergunta: 'Quais sao os requisitos para ser candidato?',
    resposta: 'Os requisitos basicos incluem: estar em dia com as obrigacoes junto ao CAU, nao ter sofrido penalidades eticas nos ultimos 5 anos, ter exercicio profissional regular, e demais requisitos estabelecidos no regulamento eleitoral.',
    categoria: 'inscricao',
  },
  {
    id: '8',
    pergunta: 'Como formo uma chapa?',
    resposta: 'Uma chapa deve ser composta pelo numero minimo de membros estabelecido no edital, incluindo presidente, vice-presidente e demais cargos. Todos os membros devem atender aos requisitos de elegibilidade.',
    categoria: 'inscricao',
  },
  // Calendario
  {
    id: '9',
    pergunta: 'Quando sera a proxima eleicao?',
    resposta: 'Consulte o Calendario Eleitoral para ver todas as datas importantes, incluindo periodos de inscricao, campanha e votacao. As datas sao definidas no edital de cada eleicao.',
    categoria: 'calendario',
  },
  {
    id: '10',
    pergunta: 'Qual o prazo para inscricao de chapas?',
    resposta: 'O prazo para inscricao de chapas e definido no edital de cada eleicao. Geralmente, o periodo de inscricoes dura cerca de 30 dias. Consulte o calendario eleitoral para datas especificas.',
    categoria: 'calendario',
  },
  // Documentos
  {
    id: '11',
    pergunta: 'Onde encontro o edital da eleicao?',
    resposta: 'O edital e todos os documentos oficiais estao disponiveis na secao Documentos do site. Voce pode baixar o edital, regulamento, formularios e outros documentos necessarios.',
    categoria: 'documentos',
  },
  {
    id: '12',
    pergunta: 'Quais documentos preciso para me candidatar?',
    resposta: 'Os documentos necessarios incluem: RG, CPF, comprovante de registro no CAU, declaracao de quitacao eleitoral, certidoes negativas, e demais documentos especificados no edital.',
    categoria: 'documentos',
  },
  // Seguranca
  {
    id: '13',
    pergunta: 'Como o sistema garante a seguranca do voto?',
    resposta: 'O sistema utiliza multiplas camadas de seguranca: criptografia SSL/TLS, autenticacao de dois fatores, auditoria completa de acoes, servidores seguros e backup em tempo real. Todas as transacoes sao registradas em log imutavel.',
    categoria: 'seguranca',
  },
  {
    id: '14',
    pergunta: 'Como sei que meu voto foi computado?',
    resposta: 'Apos confirmar seu voto, voce recebera um comprovante com codigo unico de verificacao. Este codigo pode ser usado para verificar que seu voto foi registrado no sistema, sem revelar em quem voce votou.',
    categoria: 'seguranca',
  },
  // Problemas
  {
    id: '15',
    pergunta: 'Esqueci minha senha, o que faco?',
    resposta: 'Na tela de login, clique em "Esqueci minha senha". Informe seu CPF e e-mail cadastrado. Voce recebera um link para redefinir sua senha. Se ainda tiver problemas, entre em contato com o suporte.',
    categoria: 'problemas',
  },
  {
    id: '16',
    pergunta: 'O sistema esta fora do ar, o que faco?',
    resposta: 'Em caso de indisponibilidade do sistema, aguarde alguns minutos e tente novamente. Se o problema persistir, entre em contato com o suporte tecnico. O periodo de votacao pode ser estendido em caso de problemas tecnicos significativos.',
    categoria: 'problemas',
  },
  {
    id: '17',
    pergunta: 'Nao consigo acessar minha conta, como proceder?',
    resposta: 'Verifique se seus dados (CPF e CAU) estao corretos. Certifique-se de que voce esta apto a votar nesta eleicao. Se o problema persistir, entre em contato com o suporte tecnico informando seus dados para verificacao.',
    categoria: 'problemas',
  },
]

export function FaqPage() {
  const [searchTerm, setSearchTerm] = useState('')
  const [selectedCategoria, setSelectedCategoria] = useState<string | null>(null)
  const [expandedIds, setExpandedIds] = useState<string[]>([])

  // Filter FAQs
  const filteredFaqs = mockFaqs.filter(faq => {
    const matchesSearch = faq.pergunta.toLowerCase().includes(searchTerm.toLowerCase()) ||
      faq.resposta.toLowerCase().includes(searchTerm.toLowerCase())
    const matchesCategoria = !selectedCategoria || faq.categoria === selectedCategoria
    return matchesSearch && matchesCategoria
  })

  // Group by category
  const faqsByCategoria = filteredFaqs.reduce((acc, faq) => {
    if (!acc[faq.categoria]) {
      acc[faq.categoria] = []
    }
    acc[faq.categoria].push(faq)
    return acc
  }, {} as Record<string, FaqItem[]>)

  const toggleExpand = (id: string) => {
    setExpandedIds(prev =>
      prev.includes(id)
        ? prev.filter(i => i !== id)
        : [...prev, id]
    )
  }

  const expandAll = () => {
    setExpandedIds(filteredFaqs.map(f => f.id))
  }

  const collapseAll = () => {
    setExpandedIds([])
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="text-center max-w-2xl mx-auto">
        <div className="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-full mb-4">
          <HelpCircle className="h-8 w-8 text-primary" />
        </div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
          Perguntas Frequentes
        </h1>
        <p className="text-gray-600 mt-2">
          Encontre respostas para as duvidas mais comuns sobre o processo eleitoral
        </p>
      </div>

      {/* Search */}
      <div className="max-w-2xl mx-auto">
        <div className="relative">
          <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
          <input
            type="text"
            placeholder="Digite sua duvida..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-lg"
          />
        </div>
      </div>

      {/* Categories */}
      <div className="flex flex-wrap justify-center gap-2">
        <button
          onClick={() => setSelectedCategoria(null)}
          className={`px-4 py-2 rounded-full text-sm font-medium transition-colors ${
            !selectedCategoria
              ? 'bg-primary text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          }`}
        >
          Todas
        </button>
        {categorias.map(cat => {
          const Icon = cat.icon
          return (
            <button
              key={cat.id}
              onClick={() => setSelectedCategoria(cat.id)}
              className={`inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-colors ${
                selectedCategoria === cat.id
                  ? 'bg-primary text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              }`}
            >
              <Icon className="h-4 w-4" />
              {cat.nome}
            </button>
          )
        })}
      </div>

      {/* Results Info */}
      <div className="flex items-center justify-between">
        <span className="text-sm text-gray-600">
          {filteredFaqs.length} pergunta(s) encontrada(s)
        </span>
        <div className="flex gap-2">
          <button
            onClick={expandAll}
            className="text-sm text-primary hover:underline"
          >
            Expandir todas
          </button>
          <span className="text-gray-300">|</span>
          <button
            onClick={collapseAll}
            className="text-sm text-primary hover:underline"
          >
            Recolher todas
          </button>
        </div>
      </div>

      {/* FAQ List */}
      {filteredFaqs.length === 0 ? (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <HelpCircle className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500">Nenhuma pergunta encontrada</p>
          {(searchTerm || selectedCategoria) && (
            <button
              onClick={() => {
                setSearchTerm('')
                setSelectedCategoria(null)
              }}
              className="mt-2 text-primary hover:underline"
            >
              Limpar filtros
            </button>
          )}
        </div>
      ) : (
        <div className="space-y-6">
          {selectedCategoria ? (
            // Show flat list when category is selected
            <div className="bg-white rounded-lg shadow-sm border divide-y">
              {filteredFaqs.map(faq => (
                <FaqItemComponent
                  key={faq.id}
                  faq={faq}
                  isExpanded={expandedIds.includes(faq.id)}
                  onToggle={() => toggleExpand(faq.id)}
                />
              ))}
            </div>
          ) : (
            // Show grouped by category
            Object.entries(faqsByCategoria).map(([catId, faqs]) => {
              const categoria = categorias.find(c => c.id === catId)
              if (!categoria) return null
              const Icon = categoria.icon

              return (
                <div key={catId}>
                  <h2 className="flex items-center gap-2 text-lg font-semibold text-gray-900 mb-3">
                    <Icon className="h-5 w-5 text-primary" />
                    {categoria.nome}
                  </h2>
                  <div className="bg-white rounded-lg shadow-sm border divide-y">
                    {faqs.map(faq => (
                      <FaqItemComponent
                        key={faq.id}
                        faq={faq}
                        isExpanded={expandedIds.includes(faq.id)}
                        onToggle={() => toggleExpand(faq.id)}
                      />
                    ))}
                  </div>
                </div>
              )
            })
          )}
        </div>
      )}

      {/* Contact Section */}
      <div className="bg-gradient-to-r from-primary/5 to-primary/10 rounded-xl p-6 sm:p-8">
        <div className="text-center mb-6">
          <h2 className="text-xl font-semibold text-gray-900">
            Nao encontrou o que procurava?
          </h2>
          <p className="text-gray-600 mt-1">
            Entre em contato conosco pelos canais abaixo
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <a
            href="mailto:suporte@cau.org.br"
            className="flex items-center justify-center gap-3 p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow"
          >
            <div className="p-2 bg-primary/10 rounded-lg">
              <Mail className="h-5 w-5 text-primary" />
            </div>
            <div className="text-left">
              <p className="font-medium text-gray-900">E-mail</p>
              <p className="text-sm text-gray-500">suporte@cau.org.br</p>
            </div>
          </a>

          <a
            href="tel:0800-000-0000"
            className="flex items-center justify-center gap-3 p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow"
          >
            <div className="p-2 bg-primary/10 rounded-lg">
              <Phone className="h-5 w-5 text-primary" />
            </div>
            <div className="text-left">
              <p className="font-medium text-gray-900">Telefone</p>
              <p className="text-sm text-gray-500">0800-000-0000</p>
            </div>
          </a>

          <Link
            to="/contato"
            className="flex items-center justify-center gap-3 p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow"
          >
            <div className="p-2 bg-primary/10 rounded-lg">
              <MessageCircle className="h-5 w-5 text-primary" />
            </div>
            <div className="text-left">
              <p className="font-medium text-gray-900">Chat</p>
              <p className="text-sm text-gray-500">Fale conosco online</p>
            </div>
          </Link>
        </div>
      </div>

      {/* Useful Links */}
      <div className="bg-white rounded-lg shadow-sm border p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Links Uteis</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <Link
            to="/documentos"
            className="flex items-center gap-2 text-primary hover:underline"
          >
            <FileText className="h-4 w-4" />
            Documentos Oficiais
            <ExternalLink className="h-3 w-3" />
          </Link>
          <Link
            to="/calendario"
            className="flex items-center gap-2 text-primary hover:underline"
          >
            <Calendar className="h-4 w-4" />
            Calendario Eleitoral
            <ExternalLink className="h-3 w-3" />
          </Link>
          <Link
            to="/eleicoes"
            className="flex items-center gap-2 text-primary hover:underline"
          >
            <Vote className="h-4 w-4" />
            Eleicoes em Andamento
            <ExternalLink className="h-3 w-3" />
          </Link>
          <Link
            to="/votacao"
            className="flex items-center gap-2 text-primary hover:underline"
          >
            <Users className="h-4 w-4" />
            Area do Eleitor
            <ExternalLink className="h-3 w-3" />
          </Link>
        </div>
      </div>
    </div>
  )
}

// FAQ Item Component
interface FaqItemComponentProps {
  faq: FaqItem
  isExpanded: boolean
  onToggle: () => void
}

function FaqItemComponent({ faq, isExpanded, onToggle }: FaqItemComponentProps) {
  return (
    <div>
      <button
        onClick={onToggle}
        className="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
      >
        <span className="font-medium text-gray-900 pr-4">{faq.pergunta}</span>
        {isExpanded ? (
          <ChevronUp className="h-5 w-5 text-gray-400 flex-shrink-0" />
        ) : (
          <ChevronDown className="h-5 w-5 text-gray-400 flex-shrink-0" />
        )}
      </button>
      {isExpanded && (
        <div className="px-6 pb-4">
          <p className="text-gray-600 leading-relaxed">{faq.resposta}</p>
        </div>
      )}
    </div>
  )
}
