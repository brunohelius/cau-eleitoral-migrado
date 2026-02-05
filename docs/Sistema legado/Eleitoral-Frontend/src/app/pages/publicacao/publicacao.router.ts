import { Routes } from '@angular/router';
import { SecurityGuard } from '@cau/security';
import { ListarDocumentoComponent } from './documento/listar-documento/listar-documento.component';
import { CalendarioEleicaoResolve } from 'src/app/client/calendario-client/calendario-eleicao.resolve';
import { PublicarDocumentoComponent } from './documento/publicar-documento/publicar-documento.component';
import { DocumentosPublicadosResolve } from 'src/app/client/documento-eleicao/documentos-publicados.resolve';
import { CalendariosConcluidosResolve } from 'src/app/client/calendario-client/calendarios-concluidos.resolve';
import { TipoProcessosClientResolve } from 'src/app/client/eleicao-client/eleicoes-tipo-processos-client.resolve';
import { EleicoesAnosConcluidasClientResolve } from 'src/app/client/eleicao-client/eleicoes-anos-concluidas-client.resolve';
import { ListarComissaoEleitoralComponent } from './comissao-eleitoral/listar-comissao-eleitoral/listar-comissao-eleitoral.component';
import { CalendariosComissaoEleitoralPublicacao } from 'src/app/client/calendario-client/calendarios-comissao-eleitoral-publicacao.resolve';
import { DocumentoComissaoMembroClientResolve } from 'src/app/client/documento-comissao-membro-client/documento-comissao-membro-client.resolve';
import { VisualizarComissaoEleitoralComponent } from './comissao-eleitoral/visualizar-comissao-eleitoral/visualizar-comissao-eleitoral.component';
import { CalendariosAnosPublicacaoComissaoMembroClientResolve } from 'src/app/client/calendario-client/calendarios-anos-publicacao-comissao-membro-client.resolve';

/**
 * Configurações de rota de Calendário.
 *
 * @author Squadra Tecnologia
 */
export const PublicacaoRoutes: Routes = [
    {
        path: 'comissao-eleitoral',
        component: ListarComissaoEleitoralComponent,
        data: [{
            'acao': 'listar'
        }],
        resolve: {
            tipoProcessos: TipoProcessosClientResolve,
            calendariosPublicados: CalendariosComissaoEleitoralPublicacao,
            anosEleicoes: CalendariosAnosPublicacaoComissaoMembroClientResolve
        }
    },
    {
        path: 'comissao-eleitoral/:id/visualizar',
        component: VisualizarComissaoEleitoralComponent,
        data: [{
            'acao': 'visualizar'
        }],
        resolve: {
            documentoComissaoMembro: DocumentoComissaoMembroClientResolve
        }
    },
    {
        path: 'documento',
        component: ListarDocumentoComponent,
        data: [{
            'acao': 'publicar'
        }],
        resolve: {
            tipoProcessos: TipoProcessosClientResolve,
            calendarios: CalendariosConcluidosResolve,
            anosEleicoes: EleicoesAnosConcluidasClientResolve
        }
    },
    {
        path: 'documento/:id/publicar',
        component: PublicarDocumentoComponent,
        data: [{
            'acao': 'publicar'
        }],
        resolve: {
            calendario: CalendarioEleicaoResolve,
            documentosPublicados: DocumentosPublicadosResolve,
        }
    }
];
