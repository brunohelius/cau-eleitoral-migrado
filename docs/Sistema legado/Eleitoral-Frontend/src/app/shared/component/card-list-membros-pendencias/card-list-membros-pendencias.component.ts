import { MessageService } from '@cau/message';
import { Component, Input, Output, EventEmitter, ElementRef, ViewChild, AfterViewChecked } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { StringService } from 'src/app/string.service';
import { emit } from 'process';

@Component({
  selector: 'card-list-membros-pendencias',
  templateUrl: './card-list-membros-pendencias.component.html',
  styleUrls: ['./card-list-membros-pendencias.component.scss']
})
export class CardListMembrosPendenciasComponent implements  AfterViewChecked  {

  @Input() public temExcluir?: any;
  @Input() public membrosSelecionados: any;
  @Input() public barraRolagem = false;

  public membroChapaSelecionado: any;

  public modalPendeciasMembro: BsModalRef;
  public isDivMembrosSelecionadosComBarraDeRolagem = false;

  @Output() excluirMembro: EventEmitter<any> = new EventEmitter();
  @Output() excluirTodos: EventEmitter<any> = new EventEmitter();

  @ViewChild('divMembrosSelecionados', { static: false }) divMembrosSelecionados: ElementRef;

  constructor(
    private messageService: MessageService,
  ) {

  }

  ngAfterViewChecked() {
    setTimeout(()=> {
      this.setDivMembrosSelecionadosComBarraDeRolagem();
    }, 1000);
  }

  /**
   * Responsavel por excluir o membro.
   */
  public excluirMembroEmit(membro, indiceRef): any {
    const event = {
      membro,
      indice: indiceRef
    };
    this.excluirMembro.emit(event);

  }

  /**
   * Exclui todos os membros.
   */
  public excluirTodosEmit(): void {
    this.messageService.addConfirmYesNo('MSG_CONFIRMAR_EXCLUIR_TODOS_MEMBROS_INDICADOS',
      () => {
        this.excluirTodos.emit();
      },
    );
  }

  /**
   * Verifica se a div de membros selecionados tem barra de rolagem.
   */
  public setDivMembrosSelecionadosComBarraDeRolagem(): void {
    if(this.divMembrosSelecionados){
      this.isDivMembrosSelecionadosComBarraDeRolagem = this.divMembrosSelecionados.nativeElement.scrollHeight > 350;
    } else {
      this.isDivMembrosSelecionadosComBarraDeRolagem = false;
    }

  }

  /**
   * Retorna o registro com a mascara
   */
  public getRegistroComMask(str: string) {
    return StringService.maskRegistroProfissional(str);
  }
}
