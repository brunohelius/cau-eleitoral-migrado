import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";

/**
 * Componente responsável pela apresentação de listagem de Chapas por Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'acompanhar-substituicao',
    templateUrl: './acompanhar-substituicao.component.html',
    styleUrls: ['./acompanhar-substituicao.component.scss']
})

export class AcompanharSubstituicao implements OnInit {

    public cauUfs = [];
    public pedidos: any = [];
    public solicitacoesSubstituicao = [];

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        
    ){
        this.cauUfs = route.snapshot.data["cauUfs"];
        this.solicitacoesSubstituicao = route.snapshot.data["pedidos"];
        
    }

    /**
     * Inicialização dos dados do campo
    */
    ngOnInit() {
    
      /**
      * Define ícone e título do header da página 
      */
      this.layoutsService.onLoadTitle.emit({
          icon: 'fa fa-user',
          description: this.messageService.getDescription('Pedido de Substituição')
      });
    } 

    public voltar(): void {
        this.router.navigate(['/']);
    }

    /**
     * Busca imagem de bandeira do estado do CAUUF.
     * 
     * @param idCauUf 
     */
    public getImagemBandeira(idCauUf): String {
        if(idCauUf === 0) {
            idCauUf = 165
        }
        let imagemBandeira = undefined;
        this.cauUfs.forEach(cauUf => {
            if(idCauUf == cauUf.id) {
                imagemBandeira = cauUf.imagemBandeira;
            } else if(idCauUf == undefined &&  cauUf.id == Constants.ID_CAUBR){
                imagemBandeira = cauUf.imagemBandeira;
            }
            
        });
        return imagemBandeira;
    }

    /**
     * Verifica se a chapa é IES.
     * 
     * @param chapa 
     */
    public isIES(chapa: any): boolean {
        return chapa.idCauUf == 0;
    }

    /**
     * Retorna a quantidade todal de pedidos de substituição
     */
    public getTotalPedidos(dados: any []) {
        let soma = 0;
        dados.forEach((valor) => {
            soma += valor.quantidadePedidos;
        });
        
        return soma;
    }

    public redirecionaUf(id: number, qtdSubstituicoes: number, qtdJulgados: number): void {
        if(qtdSubstituicoes > 0 || qtdJulgados > 0){
            this.router.navigate([`/eleicao/substituicao/acompanhar-uf/${id}`]);
        } else {
            this.messageService.addMsgDanger('MSG_SEM_PEDIDO_SUBSTITUICAO_MEMBRO_RESPONSAVEL');

        }
    }


}