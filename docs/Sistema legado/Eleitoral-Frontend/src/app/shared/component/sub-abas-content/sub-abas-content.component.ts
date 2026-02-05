import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'sub-abas-content',
  templateUrl: './sub-abas-content.component.html',
  styleUrls: ['./sub-abas-content.component.scss']
})
export class SubAbasContentComponent implements OnInit {

  @Input() label_a: string;
  @Input() label_b: string;
  @Input() isAbaVisivel?: boolean;
  @Input() abaInicial?: string = '';

  @Output() labelAtiva: EventEmitter<any> = new EventEmitter();

  public abaAtiva: string;

  constructor() { }

  ngOnInit() {

    if(this.isAbaVisivel == undefined) {
      this.isAbaVisivel = true
    }
    this.abaInicial ? this.abaAtiva = this.abaInicial: this.abaAtiva = this.label_a;
  }

  public ativarLabel(identificador: any): void {
    this.abaAtiva = identificador;
    this.labelAtiva.emit(identificador);
  }
}
