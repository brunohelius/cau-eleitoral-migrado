import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'card-panel',
  templateUrl: './card-panel.component.html',
  styleUrls: ['./card-panel.component.scss']
})
export class CardPanelComponent implements OnInit {

  @Input() titulo: string;

  constructor() { }

  ngOnInit() {
  }

}
