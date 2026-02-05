import {Routes} from '@angular/router';
import {SecurityGuard} from '@cau/security';

import { CalendariosConcluidosResolve } from '../../client/calendario-client/calendarios-concluidos.resolve';
import { TipoProcessosClientResolve } from 'src/app/client/eleicao-client/eleicoes-tipo-processos-client.resolve';
import { CauUFResolve } from 'src/app/client/cau-uf-client/cau-uf-client.resolve';
import { EleicaoConfiguracaoClientResolve } from 'src/app/client/eleicao-client/eleicao-configuracao-client.resolve';
import { AtividadeSecundariaResolve } from 'src/app/client/atividade-secundaria-client/atividade-secundaria.resolve';
import { TipoParticipacaoClientResolve } from 'src/app/client/eleicao-client/eleicoes-tipo-participacao-client.resolve';
import { ListAtividadePrincipalEleicaoResolve } from 'src/app/client/calendario-client/atividade-principal-eleicao.resolve';
import { AtividadeSecundariaEmailsResolve } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-emails.resolve';
import { EleicoesConcluidasInativasClientResolve } from 'src/app/client/eleicao-client/eleicoes-concluidas-inativas-client.resolve';
import { ExtratoNumeroConselheiroComponent } from './numero-conselheiro/extrato-numero-conselheiro/extrato-numero-conselheiro.component';
import { AtividadeSecundariaParamsDefinicaoEmailsResolve } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-params-definicao-emails.resolve';
import { AtividadeSecundariaParamsDefinicaoDeclaracoesResolve } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-params-definicao-declaracoes.resolve';
import { ListEleicaoChapaComponent } from './chapa-eleicao/list-eleicao-chapa/list-eleicao-chapa.component';
import { EleicoesChapaClientResolve } from 'src/app/client/chapa-client/eleicoes-chapa-client.resolve';
import { ChapaEleicaoAnoClientResolve } from 'src/app/client/chapa-client/eleicoes-chapa-ano.resolve';
import { ListChapaEleicaoComponent } from './chapa-eleicao/list-chapa-eleicao/list-chapa-eleicao.component';
import { ChapasEleicaoClientResolve } from 'src/app/client/chapa-client/chapas-eleicao-client.resolve';
import { ListChapaCauUfComponent } from './chapa-eleicao/list-chapa-cau-uf/list-chapa-cau-uf.component';
import { ChapasEleicaoCauUfClientResolve } from 'src/app/client/chapa-client/chapas-eleicao-cau-uf-client.resolve';
import { EleicoesAnosConcluidasInativasClientResolve } from 'src/app/client/eleicao-client/eleicoes-anos-concluidas-inativas-client.resolve';
import { ListEleicaoComponent } from './membros-comissao/list-eleicao/list-eleicao.component';
import { ListEleicaoConcluidaComponent } from './list-eleicao-concluida/list-eleicao-concluida.component';
import { ListAtividadePrincipalComponent } from './list-atividade-principal/list-atividade-principal.component';
import { FormComissaoMembroComponent } from './membros-comissao/form-comissao-membro/form-comissao-membro.component';
import { HistNumeroConselheiroComponent } from './numero-conselheiro/hist-numero-conselheiro/hist-numero-conselheiro.component';
import { FormInformacaoComissaoMembroComponent } from './form-informacao-comissao-membro/form-informacao-comissao-membro.component';
import { FormNumeroConselheiroComponent } from './numero-conselheiro/form-numero-conselheiro/form-numero-conselheiro.component';
import { AtividadeSecundariaCalendarioResolve } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-calendario.resolve';
import { AtividadeSecundariaProfissionaisTotaisResolve } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-profissionais-totais.resolve';
import { HistoricoChapaEleicaoClientResolve } from 'src/app/client/chapa-client/historico-chapa-eleicao.resolve';
import { VisualizarChapaComponent } from './chapa-eleicao/visualizar-chapa/visualizar-chapa.component';
import { ChapaEleicaoClientResolve } from 'src/app/client/chapa-client/chapa-eleicao-client.resolve';
import { EleicaoChapaClientResolve } from 'src/app/client/chapa-client/eleicao-chapa-client.resolve';
import { DefinirEmailDeclaracaoPorAtividadeComponent } from './definir-email-declaracao-por-atividade/definir-email-declaracao-por-atividade.component';
import {AcompanharSubstituicao} from './substituicao/acompanhar-substituicao/acompanhar-substituicao.component'
import {AcompanharSubstituicaoUF} from './substituicao/acompanhar-substituicao-uf/acompanhar-substituicao-uf.component'
import {AcompanharSubstituicaoDetalhamento} from './substituicao/acompanhar-substituicao-detalhamento/acompanhar-substituicao-detalhamento.component'
import {ListEleicaoChapaSubstituicaoComponent} from './substituicao/list-eleicao-chapa-substituicao/list-eleicao-chapa-substituicao';
import { AcompanharSubstituicaoClientResolve } from 'src/app/client/substituicao-chapa-client/acompanhar-substituicao-client.resolve';
import { SubstituicaoChapaUfClientResolve } from 'src/app/client/substituicao-chapa-client/acompanhar-substituicao-chapa-uf.resolve';
import { AcompanharSubstituicaoDetalhamentoResolve } from 'src/app/client/acompanhar-substituicao-detalhamento-client/acompanhar-substituicao-detalhamento-client.resolve';
import { EleicoesSubstituicaoChapaResolve } from 'src/app/client/substituicao-chapa-client/eleicoes-substituicao-chapa-client.resolve';
import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { JulgamentoSubstituicaoClientResolve } from 'src/app/client/substituicao-chapa-client/julgamento-substituicao-client.resolve';
import { ValidacaoNovaComissaoClientResolve } from 'src/app/client/eleicao-client/validacao-nova-comissao-client.resolve';
import { CauUfCalendarioClientResolve } from "../../client/cau-uf-client/cau-uf-calendario-client.resolve";
import { AtividadeSecundariaRecursoClientResolve } from 'src/app/client/substituicao-chapa-client/atividade-secundaria-recurso-client.resolve';
import { JulgamentoSegundaInstanciaClientResolve } from 'src/app/client/substituicao-chapa-client/julgamento-segunda-instancia-client.resolve';
import { PossuiRetificacaoChapaClientResolve } from 'src/app/client/chapa-client/possui-retificacao-chapa-client.resolve';
import { ListEleitosComponent } from './list-eleitos/list-eleitos.component';
import { FormTermoDePosseComponent } from './form-termo-de-posse/form-termo-de-posse.component';
import { FormDiplomaEleitoralComponent } from './form-diploma-eleitoral/form-diploma-eleitoral.component';

/**
 * Configurações de rota de Calendário.
 *
 * @author Squadra Tecnologia
 */
export const EleicaoRoutes: Routes = [
    {
        path: 'impugnacao',
        loadChildren: () => import('./impugnacao/impugnacao.module').then(module => module.ImpugnacaoModule),
    },
    {
        path: 'julgamento-final',
        loadChildren: () => import('./julgamento-final/julgamento-final.module').then(module => module.JulgamentoFinalModule),
    },
    {
        path: 'impugnacao-resultado',
        loadChildren: () => import('./impugnacao-resultado/impugnacao-resultado.module').then(module => module.ImpugnacaoResultadoModule),
    },
    {
        path: 'atividade-secundaria/:id/incluir-informacao-comissao-membro',
        component: FormInformacaoComissaoMembroComponent,
        data: [{
            'acao': 'incluir'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/alterar-informacao-comissao-membro',
        component: FormInformacaoComissaoMembroComponent,
        data: [{
            'acao': 'alterar'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/visualizar-informacao-comissao-membro',
        component: FormInformacaoComissaoMembroComponent,
        data: [{
            'acao': 'visualizar'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve
        }
    },
    {
        path: ':id/atividade-principal/lista',
        component: ListAtividadePrincipalComponent,
        resolve: {
            atividadesPrincipais: ListAtividadePrincipalEleicaoResolve,
        }
    },
    {
        path: 'concluida/listar',
        component: ListEleicaoConcluidaComponent,
        resolve: {
            eleicoes: CalendariosConcluidosResolve
        }
    },
    {
        path: 'membros-comissao',
        component: ListEleicaoComponent,
        resolve: {
            eleicoes: EleicoesConcluidasInativasClientResolve,
            anosEleicoes: EleicoesAnosConcluidasInativasClientResolve,
            tipoProcessos: TipoProcessosClientResolve
        }
    },
    {
        path: 'membros-comissao/:id/cadastrar',
        component: FormComissaoMembroComponent,
        data: [{
            'acao': 'incluir'
        }],
        resolve: {
            validacaoNovaComissao: ValidacaoNovaComissaoClientResolve,
            cauUfs: CauUfCalendarioClientResolve,
            anosEleicoes: EleicoesAnosConcluidasInativasClientResolve,
            eleicoes: EleicoesConcluidasInativasClientResolve,
            configuracaoEleicao: EleicaoConfiguracaoClientResolve,
            tipoParticipacao: TipoParticipacaoClientResolve
        }
    },
    {
        path: 'membros-comissao/:id/visualizar',
        component: FormComissaoMembroComponent,
        data: [{
            'acao': 'visualizar'
        }],
        resolve: {
            cauUfs: CauUfCalendarioClientResolve,
            anosEleicoes: EleicoesAnosConcluidasInativasClientResolve,
            eleicoes: EleicoesConcluidasInativasClientResolve,
            configuracaoEleicao: EleicaoConfiguracaoClientResolve,
            tipoParticipacao: TipoParticipacaoClientResolve
        }
    },
    {
        path: 'membros-comissao/:id/alterar/:cauUf',
        component: FormComissaoMembroComponent,
        data: [{
            'acao': 'alterar'
        }],
        resolve: {
            cauUfs: CauUfCalendarioClientResolve,
            anosEleicoes: EleicoesAnosConcluidasInativasClientResolve,
            eleicoes: EleicoesConcluidasInativasClientResolve,
            configuracaoEleicao: EleicaoConfiguracaoClientResolve,
            tipoParticipacao: TipoParticipacaoClientResolve
        }
    },
    {
        path: 'membros-comissao/:id/alterar',
        component: FormComissaoMembroComponent,
        data: [{
            'acao': 'alterar'
        }],
        resolve: {
            cauUfs: CauUfCalendarioClientResolve,
            anosEleicoes: EleicoesAnosConcluidasInativasClientResolve,
            eleicoes: EleicoesConcluidasInativasClientResolve,
            configuracaoEleicao: EleicaoConfiguracaoClientResolve,
            tipoParticipacao: TipoParticipacaoClientResolve
        },
    },
    {
        path: 'atividade-secundaria/:id/definir-email-comissao-membro',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_ENVIADO_PARA_MEMBROS_COMISSAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'chapa/listar',
        component: ListEleicaoChapaComponent,
        resolve: {
            eleicoes: EleicoesChapaClientResolve,
            eleicoesAno: ChapaEleicaoAnoClientResolve
        }
    },
    {
        path: ':id/chapa/listar',
        component: ListChapaEleicaoComponent,
        resolve: {
            chapas: ChapasEleicaoClientResolve,
            cauUfs: CauUFResolve,
            historico: HistoricoChapaEleicaoClientResolve
        }
    },
    {
        path: ':id/chapa/:idCauUf/uf/listar',
        component: ListChapaCauUfComponent,
        resolve: {
            chapas: ChapasEleicaoCauUfClientResolve,
            cauUfs: CauUFResolve,
        }
    },
    {
        path: 'chapa/:id/visualizar-chapa',
        component: VisualizarChapaComponent,
        resolve: {
            chapaEleicao: ChapaEleicaoClientResolve,
            eleicao: EleicaoChapaClientResolve,
            possuiRetificacao: PossuiRetificacaoChapaClientResolve
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-declaracao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_DECLARACAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_PARTICIPACAO_COMISSAO',
            'msgConfirmaDeclaracao': 'MSG_DESEJA_REALMENTE_ALTERAR_DECLARACAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-numero-conselheiro',
        component: FormNumeroConselheiroComponent,
        resolve: {
            calendario: AtividadeSecundariaCalendarioResolve,
            profissionais: AtividadeSecundariaProfissionaisTotaisResolve
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-declaracao-chapa',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_DECLARACAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_DECLARACAO_REGISTRO_CHAPA',
            'msgConfirmaDeclaracao': 'MSG_DESEJA_REALMENTE_ALTERAR_DECLARACAO_CHAPA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-declaracao-confirm-partic-chapa',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_DECLARACAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_DECLARACAO_CONFIRMAR_PARTICIPACAO',
            'msgConfirmaDeclaracao': 'MSG_DESEJA_REALMENTE_ALTERAR_DECLARACAO_CHAPA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-pedido-substituicao-chapa',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_PEDIDO_SUBSTITUICAO_CHAPA',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_PEDIDO_SUBSTITUICAO_CHAPA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-declaracao-pedido-impugnacao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_DECLARACAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_DECLARACAO_PEDIDO_IMPUGNACAO',
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-recurso-julgamento-admissibilidade',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_ADMISSIBILIDADE',
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-recurso-julgamento',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_RECURSO_JULGAMENTO',
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-defesa',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_DEFESA_PEDIDO_IMPUGNACAO',
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-denuncia',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_CADASTRAR_DENUNCIA',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_CADASTRAR_DENUNCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-admissibilidade-denuncia',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_ADMISSIBILIDADE_DENUNCIA',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_ADMISSIBILIDADE_DENUNCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-apresentar-defesa',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_APRESENTAR_DEFESA',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_APRESENTAR_DEFESA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-producao-provas',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_PRODUCAO_PROVAS',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_PRODUCAO_PROVAS'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-impedimento-suspeicao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_IMPEDIMENTO_SUSPEICAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_IMPEDIMENTO_SUSPEICAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-inserir-provas',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_INSERIR_PROVAS',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_INSERIR_PROVAS'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-agendamento-audiencia-instrucao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_AGENDAMENTO_AUDIENCIA_INSTRUCAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_AGENDAMENTO_AUDIENCIA_INSTRUCAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-realizacao-audiencia-instrucao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_REALIZACAO_AUDIENCIA_INSTRUCAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_REALIZACAO_AUDIENCIA_INSTRUCAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-alegacoes-finais',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_ALEGACOES_FINAIS',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_ALEGACOES_FINAIS'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-inserir-alegacoes-finais',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_INSERIR_ALEGACOES_FINAIS',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_INSERIR_ALEGACOES_FINAIS'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-inserir-novo-relator',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_INSERIR_NOVO_RELATOR',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_INSERIR_NOVO_RELATOR'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-parecer-final',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_PARECER_FINAL',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_PARECER_FINAL'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-julgamento-primeira-instancia',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_PRIMEIRA_INSTANCIA',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_PRIMEIRA_INSTANCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-recurso-reconsideracao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_RECURSO_RECONSIDERACAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_RECURSO_RECONSIDERACAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-cadastro-contrarrazao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_CADASTRO_CONTRARRAZAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_CADASTRO_CONTRARRAZAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'numero-conselho/historico',
        component: HistNumeroConselheiroComponent,
        resolve: {
            eleicoes: CalendariosConcluidosResolve
        }
    },
    {
        path: 'numero-conselho/extrato',
        component: ExtratoNumeroConselheiroComponent,
        resolve: {
            eleicoes: CalendariosConcluidosResolve
        }
    },
    {
        path: 'acompanhar-substituicao',
        component: ListEleicaoChapaSubstituicaoComponent,
        resolve: {
            cauUfs: CauUFResolve,
            eleicoes: EleicoesSubstituicaoChapaResolve,
            eleicoesAno: ChapaEleicaoAnoClientResolve
        }
    },
    {
        path: 'acompanhar-substituicao-ufs/:id',
        component: AcompanharSubstituicao,
        resolve: {
            cauUfs: CauUFResolve,
            pedidos: AcompanharSubstituicaoClientResolve
        }
    },
    {
        path: 'acompanhar-substituicao-uf/:id/calendario/:idCalendario',
        component: AcompanharSubstituicaoUF,
        resolve: {
            cauUf: BandeiraCauUFResolve,
            pedidos: SubstituicaoChapaUfClientResolve
        }
    },
    {
        path: 'substituicao-detalhamento/:id',
        component: AcompanharSubstituicaoDetalhamento,
        resolve: {
            cauUfs: CauUFResolve,
            pedido: AcompanharSubstituicaoDetalhamentoResolve,
            julgamentoSubstituicao: JulgamentoSubstituicaoClientResolve,
            atividadeSecundaria : AtividadeSecundariaRecursoClientResolve,
            julgamentoSegundaInstancia : JulgamentoSegundaInstanciaClientResolve
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-julgamento-substituicao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_SUBSTITUICAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_SUBSTITUICAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-julgamento-impugnacao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_IMPUGNACAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_IMPUGNACAO_PRIMEIRA_INSTANCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-recurso-substituicao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_RECURSO_SUBSTITUICAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_RECURSO_SUBSTITUICAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-julgamento-recurso-substituicao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_RECURSO_SUBSTITUICAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_RECURSO_SUBSTITUICAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-recurso-impugnacao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_RECURSO_IMPUGNACAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_RECURSO_JULG_IMPUGNACAO_PRIMEIRA_INSTANCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-contrarrazao-pedido-impugnacao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_CONTRARRAZAO_IMPUGNACAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_CONTRARRAZAO_IMPUGNACAO'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-julgamento-recurso-impugnacao',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_JULG_RECURSO_IMPUGNACAO',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_JULG_RECURSO_IMPUGNACAO_SEG_INSTANCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-admissibilidade-denuncia',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_ADMISSIBILIDADE_DENUNCIA',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_ADMISSIBILIDADE_DENUNCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-inserir-relator',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_INSERIR_RELATOR',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_INSERIR_RELATOR'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-julgamento-segunda-instancia',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            'tituloPrincipal': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_SEGUNDA_INSTANCIA',
            'tituloSecundario': 'LABEL_DEFINIR_EMAIL_JULGAMENTO_SEGUNDA_INSTANCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-julgamento-final',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL_JULG_FINAL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_JULG_FINAL_PRIMEIRA_INSTANCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-recurso-julgamento-final',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL_RECURSO_JULG_FINAL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_RECURSO_JULG_FINAL_PRIMEIRA_INSTANCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-substituicao-julgamento-final',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL_SUBST_JULG_FINAL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_SUBST_JULG_FINAL'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-julgamento-final-segunda-instancia',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL_JULG_FINAL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_JULG_FINAL_SEGUNDA_INSTANCIA'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-recurso-da-substituicao-julgamento-final',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL_RECURSO_SUBST_JULG_FINAL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_RECURSO_SUBST_JULG_FINAL'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-pedido-impugnacao-resultado',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_IMPUGNACAO_RESULT'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-alegacao-impugnacao-resultado',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_ALEGACAO_IMPUGNACAO_RESULT'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-julgamento-impugnacao-resultado',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULT'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-recurso-impugnacao-resultado',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_RECURSO_IMPUGNACAO_RESULT'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-contrarrazao-impugnacao-resultado',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_CONTRARRAZAO_IMPUGNACAO_RESULT'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'atividade-secundaria/:id/definir-email-julgamento-recurso-impugnacao-resultado',
        component: DefinirEmailDeclaracaoPorAtividadeComponent,
        data: [{
            tituloPrincipal: 'LABEL_DEFINIR_EMAIL',
            tituloSecundario: 'LABEL_DEFINIR_EMAIL_JULG_RECURSO_IMPUGNACAO_RESULT'
        }],
        resolve: {
            atividadeSecundaria: AtividadeSecundariaResolve,
            paramsEmails: AtividadeSecundariaParamsDefinicaoEmailsResolve,
            paramsDeclaracoes: AtividadeSecundariaParamsDefinicaoDeclaracoesResolve,
        }
    },
    {
        path: 'listar-eleitos',
        component: ListEleitosComponent,
    },
    {
        path: 'termo-de-posse',
        component: FormTermoDePosseComponent,
    },
    {
        path: 'diploma-eleitoral',
        component: FormDiplomaEleitoralComponent,
    }
];
