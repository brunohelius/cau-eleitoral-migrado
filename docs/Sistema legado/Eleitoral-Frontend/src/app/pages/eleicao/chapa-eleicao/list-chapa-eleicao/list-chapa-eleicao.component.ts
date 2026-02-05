import { ActivatedRoute, Router } from "@angular/router";
import { Component, OnInit, EventEmitter } from '@angular/core';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-client/chapa-eleicao-client.service';
import { Constants } from 'src/app/constants.service';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';

/**
 * Componente responsável pela apresentação de listagem de Chapas por Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'list-chapa-eleicao',
    templateUrl: './list-chapa-eleicao.component.html',
    styleUrls: ['./list-chapa-eleicao.component.scss']
})
export class ListChapaEleicaoComponent implements OnInit {

    public chapas: Array<any>;
    private cauUfs: Array<any>;
    public historico: Array<any>;

    public tabs: any;
    public numeroRegistrosPaginacao: number;

    public idCalendario: number;

    /**
     * Construtor da classe.
     *
     * @param route
     * @param messageService
     * @param chapaEleicaoService
     * @param securityService
     * @param router
     */
    constructor(
        private route: ActivatedRoute,
        private messageService: MessageService,
        private chapaEleicaoService: ChapaEleicaoClientService,
        private securityService: SecurityService,
        private router: Router,
    ) {
      this.chapas = route.snapshot.data["chapas"];
      this.cauUfs = route.snapshot.data["cauUfs"];
      this.historico = route.snapshot.data["historico"];
      this.idCalendario = route.snapshot.params.id;
    }

    /**
     * Inicialização dos dados do campo
     */
    ngOnInit() {
        this.inicializartabs();
        this.numeroRegistrosPaginacao = null;
    }

    /**
     * Método responsável por atualizar a aba.
     *
     * @param nomeAba
     */
    public mudarAbaSelecionada(nomeAba): void {
        this.tabs.principal.ativo = this.tabs.principal.nome == nomeAba;
        this.tabs.historico.ativo = this.tabs.historico.nome == nomeAba;
    }

    /**
     * Busca imagem de bandeira do estado do CAUUF.
     *
     * @param idCauUf
     */
    public getImagemBandeira(idCauUf): String {
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
     * Calcula total de chapas cadastradas.
     */
    public getTotalChapas(): number {
        let total: number = 0;
        this.chapas.forEach(chapa => {
            if (chapa.quantidadeTotalChapas != undefined) {
                total = total + chapa.quantidadeTotalChapas;
            }
        });
        return total;
    }

    /**
     * Retorna Rota de visualização de chaps por UF.
     *
     * @param idCauUf
     */
    public getURLVisualizarChapasUf(idCauUf: any): Array<any>{
        if(idCauUf == undefined) {
            idCauUf = 0;
        }
        return ['/eleicao', this.idCalendario, 'chapa', idCauUf ,'uf', 'listar'];
    }

    /**
     * Inicializa abas.
     */
    public inicializartabs(): void {
        this.tabs = {
            principal: {ativo: true, nome: 'principal'},
            historico: {ativo: false, nome: 'historico'}
        };
    }

    /**
     * Verifica se a chapa é IES.
     *
     * @param chapa
     */
    public isIES(chapa: any): boolean {
        return chapa.uf == Constants.IES;
    }

    /**
     * Mostra ação de visualizar chapas.
     */
    public isDisabledAcaoVisualizarChapas(chapa): boolean{
        return chapa.quantidadeTotalChapas == 0;
    }

    /**
     * Mostra Aba de Histórico de chapas.
     */
    public isMostrarHistoricoChapa(): boolean {
        return this.securityService.hasRoles([ Constants.ROLE_ACESSOR_CEN ]) && (this.historico.length > 0);
    }

    /**
     * Volta Para a pagina anterior.
     */
    public voltar(): void {
        this.router.navigate(["eleicao", 'chapa', 'listar']);
    }

    /**
    * Realiza download do extrato de quantidade de chapas da eleição.
    *
    * @param event
    * @param idCalendario
    */
   public downloadExtratoQuantidadeChapa(event: EventEmitter<any>): void {
        this.chapaEleicaoService.gerarExtratoQuantidadeChapa(this.idCalendario).subscribe(
            (data: Blob) => {
                event.emit(data);
            }, error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Realiza download do extrato de quantidade de chapas da eleição.
     *
     * @param event
     * @param idCauUf
     */
    public downloadExtratoChapaPorUf(event: EventEmitter<any>, idCauUf: any): void {

      if (idCauUf == undefined) {
        idCauUf = 0;
      }

      this.chapaEleicaoService.gerarExtratoChapaPorUf(this.idCalendario, idCauUf).subscribe(
        (data: Blob) => {
          event.emit(data);
        }, error => {
          this.messageService.addMsgDanger(error);
        }
      );
    }

    /**
     * retorna a label de pedidos cadastrados
     */
    public getLabelExtratoChapaPorUf(uf: string): any {
      return  this.messageService.getDescription('LABEL_EXTRATO_CHAPA_POR_UF',[uf]);
    }

  /**
   * Verifica se o usuário tem permissão para acessar o extrato da chapa.
   *
   * @param idCauUf
   */
  public isMostrarAcaoExtratoChapa(idCauUf: any): boolean {
      return (this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN])
        || (idCauUf == this.securityService.credential.user.cauUf.id
          && this.securityService.hasRoles([Constants.ROLE_ACESSOR_CE])));
  }
}