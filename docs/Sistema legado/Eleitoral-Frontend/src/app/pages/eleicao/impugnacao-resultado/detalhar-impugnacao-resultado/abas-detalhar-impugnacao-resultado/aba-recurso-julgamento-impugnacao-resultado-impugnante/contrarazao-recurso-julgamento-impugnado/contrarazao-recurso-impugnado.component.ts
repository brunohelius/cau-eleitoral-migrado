import { Component, OnInit, Input} from '@angular/core';
import { BsModalRef} from 'ngx-bootstrap';


@Component({
  selector: 'contrarazao-recurso-impugnado',
  templateUrl: './contrarazao-recurso-impugnado.component.html',
  styleUrls: ['./contrarazao-recurso-impugnado.component.scss']
})
export class ContrarazaoRecursoImpugnadoComponent implements OnInit {

  public modalRef: BsModalRef | null;

  @Input() bandeira: any;
  @Input() contrarrazoes: any = [];
  @Input() impugnacao: any;

  /**
   * Construtor da classe.
   */
  constructor(
  ) {
  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
  }
}
