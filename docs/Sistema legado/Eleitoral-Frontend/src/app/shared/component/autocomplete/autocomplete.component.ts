import { Component, Input, Output, EventEmitter } from '@angular/core';
import { Observable } from 'rxjs';
import { MessageService } from '@cau/message';
import { TypeaheadMatch } from 'ngx-bootstrap';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-client/chapa-eleicao-client.service';

@Component({
  selector: 'app-autocomplete',
  templateUrl: './autocomplete.component.html',
  styleUrls: ['./autocomplete.component.scss']
})
export class AutocompleteComponent {
  @Input() inputModel: string;
  @Input() membroChapaAtual: any;
  @Input() disabled?: boolean;
  @Input() placeholder?: string = '';
  @Output() inputModelChange = new EventEmitter<string>();
  @Output() onSelectProfissional: EventEmitter<any> = new EventEmitter<any>();
  @Output() nenhumRegistroEncontrado: EventEmitter<any> = new EventEmitter<any>();

  public search: Observable<any>;

  /**
   * Construtor da classe.
   *
   * @param ChapaEleicaoClientService
   * @param messageService
   */
  constructor(
    private chapaEleitoralService: ChapaEleicaoClientService,
    private messageService: MessageService
  ) {
    this.search = Observable.create((observer: any) => {
      this.chapaEleitoralService.getProfissionalPorCpfNome({ cpfNome: this.inputModel })
        .subscribe((data: any[]) => {
          if (data.length > 0) {
            observer.next(data);
          } else {
            this.inputModel = "";
            this.nenhumRegistroEncontrado.emit();
          }
        });
    });
  }

  /**
   * Método chamado quando o input de autocomplete perde o foco.
   */
  public focusout(): void {
    if(this.inputModel.length == 0 && this.membroChapaAtual.profissional) {
      this.inputModel = this.membroChapaAtual.profissional.cpf;
    }
  }

  /**
   * Emite evento ao selecionar profissional. 
   * 
   * @param event 
   */
  public adicionarMembroChapa(event: TypeaheadMatch): void {
    let profissional: any = event.item;

    this.onSelectProfissional.emit({
      membroChapa: this.membroChapaAtual,
      profissional: profissional
    });
  }

}