import { Routes } from '@angular/router';

import { SecurityGuard } from '@cau/security';
import { FormCalendarioComponent } from './form-calendario/form-calendario.component';
import { CalendarioResolve } from '../../client/calendario-client/calendario.resolve';
import { CalendariosResolve } from '../../client/calendario-client/calendarios.resolve';
import { ListCalendarioComponent } from './list-calendario/list-calendario.component';
import { PrazosCalendarioResolve } from 'src/app/client/calendario-client/prazos-calendario.resolve';
import { CalendarioAtividadesPrincipaisResolve } from 'src/app/client/calendario-client/calendario-atividades-principais.resolve';

/**
 * Configurações de rota de Calendário.
 *
 * @author Squadra Tecnologia
 */
export const CalendarioRoutes: Routes = [
  {
    path: 'incluir',
    component: FormCalendarioComponent,
    data: [{
      'acao': 'incluir'
    }]
  },
  {
    path: ':id/alterar',
    component: FormCalendarioComponent,
    data: [{
      'acao': 'alterar'
    }],
    resolve: {
      calendario: CalendarioResolve,
      calendarioAtividadesPrincipais: CalendarioAtividadesPrincipaisResolve,
      prazosCalendario: PrazosCalendarioResolve
    }
  },
  {
    path: ':id/visualizar',
    component: FormCalendarioComponent,
    data: [{
      'acao': 'visualizar'
    }],
    resolve: {
      calendario: CalendarioResolve,
      prazosCalendario: PrazosCalendarioResolve,
      calendarioAtividadesPrincipais: CalendarioAtividadesPrincipaisResolve
    }
  },
  {
    path: 'listar',
    component: ListCalendarioComponent,
    data: [{
      'acao': 'listar'
    }],
    resolve: {
      listCalendario: CalendariosResolve
    }
  },

];
