import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message';
import { Observable } from 'rxjs';
import { ComissaoEleitoralService } from './comissao-eleitoral-client.service';


/**
 * Class 'Guard' responsável por garantir que não ocorra acessos indevidos.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
  providedIn: 'root'
})
export class ComissaoEleicaoVigenteGuard implements CanActivate {

  /**
   * Construtor da classe.
   *
   * @param router
   * @param credencial
   */
  constructor( 
    private router: Router, 
    private comissaoEleitoralService: ComissaoEleitoralService,
    private messageService: MessageService) { }

  /**
   * Valida o acesso.
   *
   * @param next
   * @param state
   */
  canActivate(next: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any>  {
    return new Observable(observer => {

      this.comissaoEleitoralService.validarMembroComissaoEleicaoVigente().subscribe(
        data => {
          observer.next(true);
          observer.complete();
        },
        error => {
          observer.error(error);
          this.messageService.addMsgDanger(error);
          this.router.navigate(['/']);
        }
    );
    });
  }
}