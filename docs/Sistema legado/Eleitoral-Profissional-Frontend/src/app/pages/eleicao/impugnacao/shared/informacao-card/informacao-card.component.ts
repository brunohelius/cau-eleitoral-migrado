import { Component, OnInit, Input } from '@angular/core';
import { StringService } from 'src/app/string.service';

/**
 * Componente responsável pela apresentação de listagem de Chapas por Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'informacao-card',
    templateUrl: './informacao-card.component.html',
    styleUrls: ['./informacao-card.component.scss']
})

export class informacaoCard implements OnInit {

    @Input() dados;
    
    constructor(
       
    ){}

    ngOnInit() {
    }
    
   /**
   * Retorna o registro com a mascara 
   * @param str 
   */
    public getRegistroComMask(str) {
        return StringService.maskRegistroProfissional(str);
    }
    
    /**
     * Retorna a opsição formatada
     *
     * @param numeroOrdem
     */
    public getPosicaoFormatada(numeroOrdem) {
        return numeroOrdem > 0 ? numeroOrdem : '-';
    }
}