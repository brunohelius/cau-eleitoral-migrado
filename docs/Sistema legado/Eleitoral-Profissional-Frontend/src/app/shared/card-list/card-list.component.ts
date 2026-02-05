import { Component, Input, OnInit } from '@angular/core';
import { ConfigCardListInterface } from './config-card-list-interface';

/**
 * 
 */
@Component({
  selector: 'card-list',
  templateUrl: './card-list.component.html',
  styleUrls: ['./card-list.component.scss']
})
export class CardListComponent implements OnInit {

  @Input('config') inputConfig: ConfigCardListInterface;
  @Input('pageLimit') pageLimit: number;

  constructor() { }

  /**
   * Inicia o componente
   */
  ngOnInit(): void {
    if (!this.pageLimit) {
      this.pageLimit = 10;
    }
  }
}
