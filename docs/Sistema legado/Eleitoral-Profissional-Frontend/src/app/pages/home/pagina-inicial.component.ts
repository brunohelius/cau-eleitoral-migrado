import { Component, OnInit } from '@angular/core';
import { MessageService } from '@cau/message';
import { LayoutsService } from '@cau/layout';

@Component({
    selector: 'app-pagina-inicial',
    templateUrl: './pagina-inicial.component.html',
    styleUrls: ['./pagina-inicial.component.scss']
})
export class PaginaInicialComponent implements OnInit {

    constructor(
        private messageService: MessageService,
        private layoutsService: LayoutsService,
    ) { }

    ngOnInit() {
        this.layoutsService.onLoadTitle.emit({
            icon: '',
            description: this.messageService.getDescription('')
        });
    }

}
