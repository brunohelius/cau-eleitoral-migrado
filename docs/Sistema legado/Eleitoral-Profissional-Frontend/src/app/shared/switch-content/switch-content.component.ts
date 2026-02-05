import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'switch-content',
  templateUrl: './switch-content.component.html',
  styleUrls: ['./switch-content.component.scss']
})
export class SwitchContentComponent  {

    @Input() value: any;
    @Input() cases: Array<any>;
    @Input() onlyHide: boolean = true;
    @Output() valueChange = new EventEmitter<string>();

}
