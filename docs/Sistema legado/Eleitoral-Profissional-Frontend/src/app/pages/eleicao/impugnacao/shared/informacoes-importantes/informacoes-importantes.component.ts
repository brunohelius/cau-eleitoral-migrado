import { Component, Input } from '@angular/core';

@Component({
  selector: 'informacoes-importantes',
  templateUrl: './informacoes-importantes.component.html',
  styleUrls: ['./informacoes-importantes.component.scss']
})

export class InformacoesImportantesComponent {

  @Input() mensagem: any;

  /**
   * Construtor da classe.
   */
  constructor() {}
}
