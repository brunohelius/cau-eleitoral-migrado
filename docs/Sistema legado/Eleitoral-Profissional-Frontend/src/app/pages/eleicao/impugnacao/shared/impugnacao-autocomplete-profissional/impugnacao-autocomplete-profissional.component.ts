import { MessageService } from '@cau/message';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'impugnacao-autocomplete-profissional',
  templateUrl: './impugnacao-autocomplete-profissional.component.html',
  styleUrls: ['./impugnacao-autocomplete-profissional.component.scss']
})
export class ImpugnacaoAutocompleteProfissionalComponent implements OnInit {
  
  @Input() titulo: any;
  @Input() msgHint?: any;
  @Input() placeholder: any;
  @Input() limitMax?: number;
  @Input() profissional: any;
  @Input() mensagemDados: any;
  @Input() hasHint?: any = false;
  @Output() adicionarProfissional: EventEmitter<any> = new EventEmitter();

  /**
   * Construtor da classe.
   */
  constructor(
    private messageService: MessageService
    ) {

  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
    
  }

  /**
   * Retorna placeholder utilizado no autocomplete de profissional.
   * 
   * @param membro 
   */
  public getPlaceholderAutoCompleteProfissional(): string {
    return this.messageService.getDescription(this.placeholder);
  }


  /**
   * Retorna texto de hint apresentado na tela de cadastro de plataforma eleitoral.
   */
  public getHintImpugnacao(): string {
    return this.messageService.getDescription(this.msgHint);
  }
}
