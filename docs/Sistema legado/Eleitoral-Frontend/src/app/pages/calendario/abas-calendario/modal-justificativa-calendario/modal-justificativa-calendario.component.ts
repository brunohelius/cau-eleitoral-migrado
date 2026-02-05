import { Component, OnInit, ViewChild, EventEmitter, Output } from '@angular/core';
import { ModalDirective } from 'ngx-bootstrap/modal';

@Component({
  selector: 'modal-justificativa-calendario',
  templateUrl: './modal-justificativa-calendario.component.html',
  styleUrls: ['./modal-justificativa-calendario.component.scss']
})
export class ModalJustificativaCalendarioComponent implements OnInit {
  @ViewChild(ModalDirective, { static: false }) modal: ModalDirective;
  @Output() open: EventEmitter<any> = new EventEmitter();

  messages: string[];

  constructor() { }

  ngOnInit() {
  }

  showModal() {
    this.messages = [];
    this.modal.show();
  }
  handler(type: string, $event: ModalDirective) {
    this.messages.push(
      `event ${type} is fired${$event.dismissReason
        ? ', dismissed by ' + $event.dismissReason
        : ''}`
    );
  }

}
