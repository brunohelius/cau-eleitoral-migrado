import { NgForm } from '@angular/forms';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';
import { Component, OnInit, EventEmitter, Input, Output, TemplateRef, ViewChild, ɵConsole } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'confirmar-alterar-julgamento-modal',
    templateUrl: './confirmar-alterar-julgamento.component.html',
    styleUrls: ['./confirmar-alterar-julgamento.component.scss']
})
export class ModalConfirmarAlterarJulgamentoComponent implements OnInit {

  public opcaoAlteracaoJulgamento: number = 2;

  @Output() cancelar?: EventEmitter<any> = new EventEmitter();
  @Output() confirmar?: EventEmitter<any> = new EventEmitter();

  public isSubmitted: boolean = false;

  /**
   * Construtor da classe.
   */
  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService,
  ) {

  }

  /**
   * Inicialização das dependências do componente.
   */
  ngOnInit() {

   }

  /**
   * Cancelar Alteração de julgamento.
   */
  public cancelarModal() {
    this.isSubmitted = false;
    this.cancelar.emit(this.opcaoAlteracaoJulgamento);
  }

  /**
   * Confirmar Alteração de julgamento.
   */
  public confirmarModal(form: NgForm) {
    this.isSubmitted = true;
    if(form.valid) {
      this.confirmar.emit(this.opcaoAlteracaoJulgamento);
    }
  }

}