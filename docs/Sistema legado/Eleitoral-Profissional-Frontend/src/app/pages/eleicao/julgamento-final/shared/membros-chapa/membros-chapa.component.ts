import { Component, OnInit, Input, TemplateRef } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { StringService } from 'src/app/string.service';

@Component({
  selector: 'membros-chapa',
  templateUrl: './membros-chapa.component.html',
  styleUrls: ['./membros-chapa.component.scss']
})
export class MembrosChapaComponent implements OnInit{

  @Input() public titular?: any;
  @Input() public suplente?: any;
  @Input() public title?: boolean;
  @Input() public membros?: any;
  @Input() public isMostraCheckVazio?: boolean;

  public membroChapaSelecionado: any;
  public modalPendeciasMembro: BsModalRef;

  constructor(
    private modalService: BsModalService,
  ) {}

  ngOnInit(){
  }

  /**
  * Verifica se o membro é  responsáveç.
  * @param id 
  */
  public isResponsavel(membro?: any): boolean {
    let validacao = false;
    if (membro) {
      validacao = membro.situacaoResponsavel == true;
    }
    return validacao;
  }

  /**
   * Verifica o status de Validação do Membro.
   * @param membro 
   */
  public statusValidacao(id?: any): boolean {
   return  id == Constants.STATUS_SEM_PENDENCIA;
  }

  /**
  * Métodos de retorno do status da solicitação de substituição de membros    
  */
  public getStatusConfirmado(id: any) {
    return id == Constants.STATUS_CONFIRMADO;
  }

  /**
   * Exibe modal de listagem de pendencias do profissional selecionado.
   * @param template 
   * @param element 
   */
  public abrirModalPendeciasMembro(template: TemplateRef<any>, element: any): void {
    this.membroChapaSelecionado = element;
    this.modalPendeciasMembro = this.modalService.show(template, Object.assign({}, { class: 'my-modal' }));
  }

    /**
   * Retorna o registro com a mascara 
   * @param str 
   */
  public getRegistroComMask(str?: string) {
    if(str) {
      return StringService.maskRegistroProfissional(str);
    } 
    else {
      return "";
    }
  }
}