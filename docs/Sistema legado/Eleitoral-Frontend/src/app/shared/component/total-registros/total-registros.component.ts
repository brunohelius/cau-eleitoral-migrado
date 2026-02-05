import { Component, Input } from '@angular/core';

@Component({
  selector: 'total-registros',
  templateUrl: './total-registros.component.html',
  styleUrls: ['./total-registros.component.scss']
})
export class TotalRegistrosComponent {

  @Input('paginaAtual') pagina: number = 1;
  @Input('totalPorPagina') totalPorPagina: number = 0;
  @Input('totalRegistros') totalRegistros: number = 0;

  constructor() { }

  /**
   * Recupera o registro inferior da paginação.
   */
  public getTotalInferior(): number {
    return (this.pagina - 1) * this.totalPorPagina + 1;
  }

  /**
   * Recupera o total superior da paginação.
   */
  public getTotalSuperior(): number {
    let totalSuperior = this.pagina * this.totalPorPagina;

    if (totalSuperior > this.getTotalRegistros()) {
      totalSuperior = this.getTotalRegistros();
    }

    return totalSuperior;
  }

  /**
   * Recupera o total de registros.
   */
  public getTotalRegistros(): number {
    return this.totalRegistros;
  }

}
