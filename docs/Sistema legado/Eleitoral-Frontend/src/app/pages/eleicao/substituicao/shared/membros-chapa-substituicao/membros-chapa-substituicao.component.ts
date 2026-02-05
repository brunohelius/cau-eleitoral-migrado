import { Component, OnInit, Input, TemplateRef } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { StringService } from 'src/app/string.service';

@Component({
  selector: 'membros-chapa-substituicao',
  templateUrl: './membros-chapa-substituicao.component.html',
  styleUrls: ['./membros-chapa-substituicao.component.scss']
})
export class MembrosChapaSubstituicaoComponent implements OnInit {

  @Input() public membroSubstitutoTitular: any;
  @Input() public membroSubstituidoTitular: any;
  @Input() public membroSubstitutoSuplente: any;
  @Input() public membroSubstituidoSuplente: any;

  public membroChapaSelecionado: any;

  public modalPendeciasMembro: BsModalRef;

  constructor(
    private modalService: BsModalService,
  ) { }

  ngOnInit() {
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
   * 
   * @param membro 
   */
  public statusValidacao(membro?: any): boolean {
    let validacao = false;
    if (membro) {
      validacao = membro.statusValidacaoMembroChapa.id == Constants.STATUS_SEM_PENDENCIA;
    }
    return validacao;
  }

  /**
    * Métodos de retorno do status da solicitação de substituição de membros    
    */
  public getStatusConfirmado(id: any) {
    return id == Constants.STATUS_CONFIRMADO;
  }

  /**
     * Exibe modal de listagem de pendencias do profissional selecionado.
     * 
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
  public getRegistroComMask(str) {
    return StringService.maskRegistroProfissional(str);
  }
}