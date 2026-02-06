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
  { id: 'geral', nome: 'Geral', icon: HelpCircle },
  { id: 'votacao', nome: 'Votacao', icon: Vote },
  { id: 'candidatura', nome: 'Candidatura', icon: Users },
  { id: 'processo', nome: 'Processo Eleitoral', icon: FileText },
]

// FAQ data
const faqs: FaqItem[] = [
  // Geral
  {
    id: '1',
    pergunta: 'O que e o Sistema Eleitoral do CAU?',
    resposta: 'O Sistema Eleitoral do CAU (Conselho de Arquitetura e Urbanismo) e a plataforma oficial utilizada para conduzir as eleicoes dos orgaos de representacao do conselho profissional. Por meio deste sistema, sao realizadas as eleicoes para os Conselhos de Arquitetura e Urbanismo dos Estados e do Distrito Federal (CAU/UF) e para o Conselho de Arquitetura e Urbanismo do Brasil (CAU/BR). O sistema permite o registro de chapas, a votacao online, a apuracao dos resultados e o acompanhamento de todo o processo eleitoral de forma transparente e segura.',
    categoria: 'geral',
  },
  {
    id: '2',
    pergunta: 'Quem pode votar nas eleicoes do CAU?',
    resposta: 'Podem votar nas eleicoes do CAU todos os profissionais de Arquitetura e Urbanismo que possuam registro ativo e regular junto ao CAU/UF de sua jurisdicao. Para estar apto a votar, o profissional deve estar em dia com suas anuidades e obrigacoes junto ao conselho, nao possuir impedimentos legais ou disciplinares vigentes, e estar devidamente inscrito no cadastro eleitoral. O registro no CAU e o CPF sao utilizados como credenciais para acesso ao sistema de votacao.',
    categoria: 'geral',
  },
  // Votacao
  {
    id: '3',
    pergunta: 'Como funciona o processo de votacao online?',
    resposta: 'O processo de votacao online e realizado inteiramente pela internet, de forma segura e acessivel. O eleitor acessa a Area do Eleitor no portal publico do sistema, identifica-se com seu CPF e numero de registro no CAU, e informa sua senha. Apos a autenticacao, o eleitor visualiza as eleicoes disponiveis para o seu perfil e seleciona aquela em que deseja votar. Na cedula eletronica, sao apresentadas todas as chapas concorrentes, e o eleitor escolhe a chapa de sua preferencia ou opta pelo voto em branco. Antes da confirmacao, e exibido um resumo da escolha para revisao. Apos confirmar, o voto e registrado de forma anonima e um comprovante de votacao e gerado para o eleitor.',
    categoria: 'votacao',
  },
  {
    id: '4',
    pergunta: 'O voto e obrigatorio?',
    resposta: 'Sim, o voto nas eleicoes do CAU e obrigatorio para todos os profissionais com registro ativo e regular. O profissional que nao exercer o voto e nao apresentar justificativa dentro do prazo previsto no calendario eleitoral podera estar sujeito a penalidades previstas no regulamento eleitoral e na legislacao do conselho. A justificativa de ausencia deve ser feita por meio do proprio sistema eleitoral ou pelos canais indicados no edital da eleicao. Recomenda-se que o eleitor fique atento ao periodo de votacao e exerca seu direito e dever de voto dentro do prazo estipulado.',
    categoria: 'votacao',
  },
  // Candidatura
  {
    id: '5',
    pergunta: 'O que e uma chapa eleitoral?',
    resposta: 'Uma chapa eleitoral e o conjunto de candidatos que se apresentam de forma unificada para concorrer aos cargos eletivos do CAU. Cada chapa e composta por membros titulares e suplentes que ocuparao os cargos de conselheiros no CAU/UF ou no CAU/BR, conforme a eleicao em questao. A chapa deve atender aos requisitos estabelecidos no edital, incluindo o numero minimo e maximo de membros, a representatividade regional quando exigida, e a conformidade de todos os seus integrantes com os criterios de elegibilidade. As chapas sao registradas durante o periodo de inscricoes e, apos analise pela Comissao Eleitoral, sao homologadas ou indeferidas.',
    categoria: 'candidatura',
  },
  {
    id: '6',
    pergunta: 'Como posso me candidatar?',
    resposta: 'Para se candidatar nas eleicoes do CAU, o profissional deve integrar uma chapa eleitoral. O primeiro passo e verificar os requisitos de elegibilidade estabelecidos no edital da eleicao, que geralmente incluem: possuir registro ativo e regular no CAU, estar em dia com as anuidades e obrigacoes financeiras, nao ter sofrido penalidades eticas ou disciplinares nos periodos definidos pelo regulamento, e ter exercicio profissional regular. O candidato deve reunir os demais membros da chapa, preencher o formulario de inscricao disponivel no sistema, anexar a documentacao exigida (como RG, CPF, comprovante de registro no CAU e declaracoes) e submeter a inscricao dentro do prazo previsto no calendario eleitoral. A Comissao Eleitoral analisara a documentacao e publicara a lista de chapas deferidas e indeferidas.',
    categoria: 'candidatura',
  },
  // Processo Eleitoral
  {
    id: '7',
    pergunta: 'O que e uma impugnacao?',
    resposta: 'A impugnacao e um instrumento juridico e administrativo que permite a qualquer eleitor, candidato ou chapa registrada questionar a validade de um ato do processo eleitoral. Pode-se impugnar, por exemplo, o registro de uma chapa que nao atenda aos requisitos do edital, o resultado de uma votacao em caso de irregularidades comprovadas, ou qualquer decisao da Comissao Eleitoral que se entenda contraria ao regulamento. A impugnacao deve ser formalizada por escrito, com fundamentacao e provas, dentro do prazo estabelecido no calendario eleitoral. A Comissao Eleitoral ou a Comissao Julgadora, conforme o caso, analisara o pedido e emitira uma decisao. O resultado da analise e publicado oficialmente no sistema.',
    categoria: 'processo',
  },
  {
    id: '8',
    pergunta: 'Como acompanhar o calendario eleitoral?',
    resposta: 'O calendario eleitoral contem todas as datas e prazos relevantes do processo eleitoral, como o periodo de inscricao de chapas, os prazos para impugnacoes, o periodo de campanha, as datas de votacao, a apuracao e a proclamacao dos resultados. Para acompanhar o calendario, acesse a secao "Calendario" no menu principal do portal publico. Todas as etapas sao listadas em ordem cronologica com suas respectivas datas de inicio e termino. E recomendavel que eleitores e candidatos consultem o calendario regularmente para nao perder prazos importantes. As datas sao definidas pela Comissao Eleitoral e publicadas no edital de cada eleicao.',
    categoria: 'processo',
  },
  {
    id: '9',
    pergunta: 'Como posso fazer uma denuncia eleitoral?',
    resposta: 'Se voce identificar qualquer irregularidade durante o processo eleitoral, como praticas vedadas de campanha, uso indevido de recursos do conselho, coacao de eleitores ou qualquer outra conduta contraria ao regulamento, voce pode registrar uma denuncia pelo sistema. Acesse a secao de denuncias no portal ou entre em contato com a Comissao Eleitoral responsavel. A denuncia deve conter a descricao detalhada dos fatos, indicacao de data e local (quando aplicavel) e, sempre que possivel, provas ou evidencias que sustentem a alegacao. A Comissao Eleitoral analisara o caso e adotara as providencias cabiveis conforme o regulamento. A identidade do denunciante sera tratada com sigilo.',
    categoria: 'processo',
  },
  {
    id: '10',
    pergunta: 'Onde posso obter os documentos oficiais?',
    resposta: 'Todos os documentos oficiais relacionados ao processo eleitoral estao disponiveis na secao "Documentos" do portal publico do sistema. Nesta area, voce encontra o edital da eleicao, o regulamento eleitoral vigente, formularios de inscricao de chapas, modelos de requerimentos, resolucoes da Comissao Eleitoral, atas de reunioes e demais documentos pertinentes. Os documentos podem ser visualizados diretamente no navegador ou baixados em formato PDF. Recomenda-se que candidatos e eleitores leiam atentamente o edital e o regulamento antes de participar do processo, para compreender todas as regras, prazos e procedimentos aplicaveis.',
    categoria: 'processo',
  },
]

export function FaqPage() {
  const [searchTerm, setSearchTerm] = useState('')
  const [selectedCategoria, setSelectedCategoria] = useState<string | null>(null)
  const [expandedIds, setExpandedIds] = useState<string[]>([])

  // Filter FAQs
  const filteredFaqs = faqs.filter(faq => {
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
            Object.entries(faqsByCategoria).map(([catId, catFaqs]) => {
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
                    {catFaqs.map(faq => (
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
