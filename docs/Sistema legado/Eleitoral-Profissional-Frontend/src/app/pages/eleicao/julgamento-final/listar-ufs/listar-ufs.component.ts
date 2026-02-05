import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";
import { SubstiuicaoChapaClientService } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-client.module';

/**
 * Componente responsável pela apresentação de listagem de Chapas por Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'listar-ufs',
    templateUrl: './listar-ufs.component.html',
    styleUrls: ['./listar-ufs.component.scss']
})

export class ListarUfsComponent implements OnInit {

    public usuario;
    public cauUfs = [];
    public pedidos: any = [];
    private calendarioId: any;
    private permissoes = [];
    public chapas: any = [];

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
        private substituicaoChapaClient: SubstiuicaoChapaClientService

    ) {
        this.cauUfs = route.snapshot.data["cauUfs"];
        this.chapas = route.snapshot.data["chapas"];
        this.calendarioId = route.snapshot.params.id;
    }

    /**
     * Inicialização dos dados do campo
    */
    ngOnInit() {

        /**
        * Define ícone e título do header da página 
        */
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-wpforms',
            description: this.messageService.getDescription('TITLE_JULGAMENTO')
        });

        this.usuario = this.securityService.credential["_user"];
        this.getPermissao();
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
        if (idCauUf === 0) {
            idCauUf = 165
        }
        let imagemBandeira = undefined;
        this.cauUfs.forEach(cauUf => {
            if (idCauUf == cauUf.id) {
                imagemBandeira = cauUf.imagemBandeira;
            } else if (idCauUf == undefined && cauUf.id == Constants.ID_CAUBR) {
                imagemBandeira = cauUf.imagemBandeira;
            }

        });
        return imagemBandeira;
    }

    /**
     * Retorna as permissões do usuário logado
     */
    public getPermissao() {
        const regras = this.usuario.roles;
        regras.forEach(element => {
            if(element == Constants.ROLE_ACESSOR_CEN || element == Constants.ROLE_ACESSOR_CE) {
                this.permissoes.push(element);
            }  
        });   
    
    }

    /**
     * Verifica se a chapa é IES.
     * 
     * @param chapa 
     */
    public isIES(chapa: any): boolean {
        return chapa.idCauUf == 0 || !chapa.idCauUf;
    }

    /**
     * Retorna a quantidade todal de pedidos de substituição
     */
    public getTotalPedidos(dados: any[]) {
        let soma = 0;
        dados.forEach((valor) => {
            soma += valor.quantidadeTotalChapas;
        });

        return soma;
    }

    public redirecionaUf(id: number, chapas: any): void {

        !id ? id = Constants.ID_IES : id = id;
        let total = chapas.quantidadeChapasPendentes + chapas.quantidadeChapasSemPendentes + chapas.quantidadeTotalChapas;

        if (total === 0) {
            this.messageService.addMsgWarning('LABEL_NENHUM_REGISTRO_ENCONTRADO');
        } else {
            this.router.navigate([
                `/eleicao/julgamento-final/acompanhar-uf/${id}`,
                {
                    isIES: id == 0
                }
            ]);
        }
    }

    /**
    * retorna a label da aba de acompanhar chapa com quebra de linha
    */
    public getTituloAbaAcompanharChapa(): any {
        return  this.messageService.getDescription('LABEL_ACOMPANHAR_CHAPA',['<div>','</div><div>','</div>']);
    }

    /**
     * retorna a label de quantidade  de chapas cadastradas
     */
    public getLabelQuantidadeDeChapas(): any {
        return  this.messageService.getDescription('LABEL_QUANTIDADE_CHAPAS_QUEBRA_LINHA',['<div>','</div><div>','</div>']);
    }
    
    /**
     * retorna a label de quantidade de chapas com pendência
     */
    public getLabelQuantidadeDeChapasComPendencia(): any {
        return  this.messageService.getDescription('LABEL_QUANTIDADE_CHAPAS_COM_PENDENCIA_QUEBRA_LINHA',['<div>','</div><div>','</div>']);
    }

    /**
     * retorna a label de quantidade de chapas sem pendência
     */
    public getLabelQuantidadeDeChapasSemPendencia(): any {
        return  this.messageService.getDescription('LABEL_QUANTIDADE_CHAPAS_SEM_PENDENCIA_QUEBRA_LINHA',['<div>','</div><div>','</div>']);
    }
 
}