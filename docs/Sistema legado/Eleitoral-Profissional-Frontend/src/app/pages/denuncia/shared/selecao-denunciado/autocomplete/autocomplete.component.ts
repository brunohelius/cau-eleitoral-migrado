import { Observable } from 'rxjs';
import { Component, Input, Output, EventEmitter } from '@angular/core';
import { MessageService } from '@cau/message';
import { TypeaheadMatch } from 'ngx-bootstrap';

import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';

@Component({
  selector: 'app-autocomplete-membro',
  templateUrl: './autocomplete.component.html',
  styleUrls: ['./autocomplete.component.scss']
})
export class AutocompleteMembroComponent {
  @Input() inputModel: any;
  @Input() membroAtual: any;
  @Input() disabled?: boolean;
  @Input() methodService: any;
  @Input() placeholder?: string = '';
  @Output() inputModelChange = new EventEmitter<string>();
  @Output() onSelectMembro?: EventEmitter<any> = new EventEmitter<any>();
  @Output() onDeSelectProfissional?: EventEmitter<any> = new EventEmitter<any>();

  public search: Observable<any>;

  /**
   * Construtor da classe.
   *
   * @param DenunciaClientService
   * @param messageService
   */
  constructor(
    private messageService: MessageService,
    private denunciaService: DenunciaClientService,
  ) {
    this.search = Observable.create((observer: any) => {
      this.denunciaService[this.methodService](this.inputModel)
        .subscribe(
          (data: any[]) => {
            if (data.length > 0) {
              observer.next(data);
              observer.complete();
            } else {
              this.inputModel = {
                idCauUf: this.inputModel.idCauUf
              };
              this.onDeSelectProfissional.emit();
            }
          },
          error => {
            this.messageService.addMsgDanger(error);
          }
        );
    });
  }

  /**
   * Método chamado quando o input de autocomplete perde o foco.
   */
  public focusout(): void {
    if (this.inputModel.length == 0 && this.membroAtual.profissional) {
      this.inputModel = this.membroAtual.profissional.nome;
    }
  }

  /**
   * Emite evento ao selecionar membro.
   *
   * @param event
   */
  public adicionarMembro(event: TypeaheadMatch): void {
    let membro: any = event.item;
    this.inputModel.nomeRegistro = membro.profissional.nome;
    this.onSelectMembro.emit(membro);
  }
}