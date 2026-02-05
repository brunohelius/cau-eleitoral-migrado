import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'paginacao',
  templateUrl: './paginacao.component.html',
  styleUrls: ['./paginacao.component.scss']
})
export class PaginacaoComponent implements OnInit {

  @Input('paginaAtual') pagina: number = 1;
  @Input('totalPorPagina') totalPorPagina: number = 0;
  @Input('totalRegistros') totalRegistros: number = 0;
  @Input('mostrarTotalDeRegistros') mostrarTotalDeRegistros: boolean = true;

  /**
   * Construtor da classe.
   */
  constructor() { }

  /**
   * Após inicializar o componente.
   */
  ngOnInit() {
    this.pagina = this.pagina == undefined ? 1 : this.pagina;
    this.totalPorPagina = this.totalPorPagina == undefined ? 0 : this.totalPorPagina;
    this.totalRegistros = this.totalRegistros == undefined ? 0 : this.totalRegistros;
  }
}
