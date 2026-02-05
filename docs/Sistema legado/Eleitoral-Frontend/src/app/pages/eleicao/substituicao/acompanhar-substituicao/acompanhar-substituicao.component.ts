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
    selector: 'acompanhar-substituicao',
    templateUrl: './acompanhar-substituicao.component.html',
    styleUrls: ['./acompanhar-substituicao.component.scss']
})

export class AcompanharSubstituicao implements OnInit {

    public usuario;
    public cauUfs = [];
    public pedidos: any = [];
    public solicitacoesSubstituicao = [];
    private calendarioId: any;
    private permissoes = [];

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
        private substituicaoChapaClient: SubstiuicaoChapaClientService

    ) {
        this.cauUfs = route.snapshot.data["cauUfs"];
        this.solicitacoesSubstituicao = route.snapshot.data["pedidos"];
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
            icon: 'fa fa-user',
            description: this.messageService.getDescription('Pedido de Substituição')
        });

        this.usuario = this.securityService.credential["_user"];
        this.getPermissao();
    }

    public voltar(): void {
        this.router.navigate(['/eleicao/acompanhar-substituicao']);
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
            if (element == Constants.ROLE_ACESSOR_CEN || element == Constants.ROLE_ACESSOR_CE) {
                this.permissoes.push(element);
            }
        });

    }
    /**
     * Verifica se é acessr CEN, caso não seja abilita
     * apenas a ação de visualizar de sua UF
     * @param idCauUf
     */
    public isMostraAcao(idCauUf) {
        return (this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN])
            || (idCauUf == this.securityService.credential.user.cauUf.id
                && this.securityService.hasRoles([Constants.ROLE_ACESSOR_CE])));
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
    public getTotalPedidos(dados: any[]) {
        let soma = 0;
        dados.forEach((valor) => {
            soma += valor.quantidadePedidos;
        });

        return soma;
    }

    public redirecionaUf(id: number, solicitacoesSubstituicao: any): void {
        let total = solicitacoesSubstituicao.quantidadePedidos + solicitacoesSubstituicao.quantidadePedidosJulgados;
        if (total === 0) {
            this.messageService.addMsgWarning('LABEL_NENHUM_REGISTRO_ENCONTRADO');
        } else {
            this.router.navigate([
                `/eleicao/acompanhar-substituicao-uf/${id}/calendario/${this.calendarioId}`,
                {
                    isIES: id == 0
                }
            ]);
        }
    }


}