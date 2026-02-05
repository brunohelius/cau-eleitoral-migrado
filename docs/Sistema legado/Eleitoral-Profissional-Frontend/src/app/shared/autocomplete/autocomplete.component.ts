import { Component, Input, Output, EventEmitter, ViewChild, TemplateRef } from '@angular/core';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';
import { Observable } from 'rxjs';
import { MessageService } from '@cau/message';
import { TypeaheadMatch } from 'ngx-bootstrap';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';


@Component({
  selector: 'app-autocomplete',
  templateUrl: './autocomplete.component.html',
  styleUrls: ['./autocomplete.component.scss']
})
export class AutocompleteComponent {
  @Input() limitMax?: number = 10000;
  @Input() inputModel: string;
  @Input() membroChapaAtual: any;
  @Input() disabled?: boolean;
  @Input() placeholder?: string = '';
  @Input() isNumeroRegistro?: boolean;
  @Output() inputModelChange = new EventEmitter<string>();
  @Output() onSelectProfissional: EventEmitter<any> = new EventEmitter<any>();
  @Output() onDeSelectProfissional: EventEmitter<any> = new EventEmitter<any>();
  
  public parametroConsulta: any;
  public search: Observable<any>;

  /**
   * Construtor da classe.
   *
   * @param ChapaEleicaoClientService
   * @param messageService
   */
  constructor(
    private chapaEleitoralService: ChapaEleicaoClientService,
    private messageService: MessageService,
    private stringService: StringService
  ) {
    
    this.search = Observable.create((observer: any) => {
      
      if(this.isNumeroRegistro === true) {
        this.parametroConsulta = { registroNome: this.inputModel }
      } else {
        this.parametroConsulta = {cpfNome: this.inputModel }
      }

      this.chapaEleitoralService.getProfissionalPorCpfNome(this.parametroConsulta)
        .subscribe(
          (data: any[]) => {
            if (data.length > 0) {
              observer.next(data);
            } else {
              this.onDeSelectProfissional.emit();
              this.inputModel = "";
            }
          },
          error => {            
            if(error.code == Constants.CODIGO_ERRO_CPF_NOME_NAO_ENCONTRADO || error.code == Constants.CODIGO_ERRO_NUMERO_REGISTRO_NOME_NAO_ENCONTRADO){
              this.inputModel = "";
              this.messageService.addMsgWarning(error);
            } else {
              this.messageService.addMsgDanger(error);
            }
          }
        );
    });
  }

  /**
   * Método chamado quando o input de autocomplete perde o foco.
   */
  public focusout(): void {
    if(this.inputModel != undefined && this.inputModel.length == 0 && this.membroChapaAtual != undefined && this.membroChapaAtual.profissional) {
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

  /**
   * Retorna o registro com a mascara 
   * @param str 
   */
  public getRegistroComMask(str) {
    return StringService.maskRegistroProfissional(str);
  }

}