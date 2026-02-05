import { Component, OnInit, Input, Output, EventEmitter} from '@angular/core';
import { BsModalRef} from 'ngx-bootstrap';


@Component({
  selector: 'list-impugnacao-resultado-contrarrazao',
  templateUrl: './list-impugnacao-resultado-contrarrazao.component.html',
  styleUrls: ['./list-impugnacao-resultado-contrarrazao.component.scss']
})
export class ListImpugnacaoResultadoContrarrazaoComponent implements OnInit {

  public modalRef: BsModalRef | null;

  @Input() bandeira: any;
  @Input() contrarrazoes: any = [];
  @Input() impugnacao: any;
  @Output() contrarrazao: EventEmitter<any> = new EventEmitter();

  /**
   * Construtor da classe.
   */
  constructor(
  ) {}

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
  }

  /**
   * Emite os dados da contrarrazão selecionada na ação
   * @param contrarrazao 
   */
  public abrirModal(contrarrazao): void {
    this.contrarrazao.emit(contrarrazao);
  }
}
