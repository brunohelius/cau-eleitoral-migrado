import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { Injectable } from '@angular/core';
import { DenunciaClientService } from './denuncia-client.service';
import { MessageService } from '@cau/message';
import { Observable } from 'rxjs';


/**
 * Class 'Guard' responsável por garantir que não ocorra acessos indevidos no Cadastro de Mantenedora.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
  providedIn: 'root'
})
export class DenunciaAcompanharGuard implements CanActivate {

  /**
   * Construtor da classe.
   *
   * @param router
   * @param credencial
   */
  constructor( 
    private router: Router, 
    private denunciaService: DenunciaClientService,
    private messageService: MessageService) { }

  /**
   * Valida o acesso.
   *
   * @param next
   * @param state
   */
  canActivate(next: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any>  {
    let idDenuncia = next.params["id"];

    return new Observable(observer => {

      this.denunciaService.validarAcessoDenunciaCorporativoPorDenuncia(idDenuncia).subscribe(
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