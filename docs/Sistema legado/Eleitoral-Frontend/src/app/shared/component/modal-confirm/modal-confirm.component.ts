import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'modal-confirm',
  templateUrl: './modal-confirm.component.html',
  styleUrls: ['./modal-confirm.component.scss']
})
export class ModalConfirmComponent implements OnInit {

  @Input() titulo: any;
  @Input() descricao: any;
  @Input() isModalConfirmOk?: boolean;
  @Input() isModalConfirmSimNao?: boolean = false;

  @Output() cancelar: EventEmitter<any> = new EventEmitter();
  @Output() confirmar: EventEmitter<any> = new EventEmitter();

  constructor() { }

  ngOnInit() {
  }

  public cancelarModal() {
    this.cancelar.emit();
  }

  public confirmarModal() {
    this.confirmar.emit();
  }
}
