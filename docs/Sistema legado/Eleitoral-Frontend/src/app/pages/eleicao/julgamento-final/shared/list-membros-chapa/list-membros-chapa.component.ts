import { Component, OnInit, Input, TemplateRef } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { StringService } from 'src/app/string.service';

@Component({
  selector: 'list-membros-chapa',
  templateUrl: './list-membros-chapa.component.html',
  styleUrls: ['./list-membros-chapa.component.scss']
})
export class ListMembrosChapaComponent implements OnInit{

  @Input() public membrosChapa?: any;

  public limitePaginacao: number;
  public limitesPaginacao: any = [];


  constructor(
    private modalService: BsModalService,
  ) {}

  ngOnInit(){
    this.limitePaginacao = 10;
    this.limitesPaginacao = [10,20, 30, 50];
  }

}