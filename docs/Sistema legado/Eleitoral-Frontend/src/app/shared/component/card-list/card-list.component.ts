import { Component, Input } from '@angular/core';
import { ConfigCardListInterface } from './config-card-list-interface';

@Component({
  selector: 'card-list',
  templateUrl: './card-list.component.html',
  styleUrls: ['./card-list.component.scss']
})
export class CardListComponent {

  public limitePaginacao = 10;
  @Input('config') inputConfig: ConfigCardListInterface;

  constructor() { }
}
