import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'card-panel-custom',
  templateUrl: './card-panel-custom.component.html',
  styleUrls: ['./card-panel-custom.component.scss']
})
export class CardPanelCustomComponent implements OnInit {

  @Input() titulo?: string;
  @Input() background?: string;

  constructor() { 
  }


  ngOnInit() {
    if(!this.background) {
      this.background = '#e4f0f0'
    }
  }
}
