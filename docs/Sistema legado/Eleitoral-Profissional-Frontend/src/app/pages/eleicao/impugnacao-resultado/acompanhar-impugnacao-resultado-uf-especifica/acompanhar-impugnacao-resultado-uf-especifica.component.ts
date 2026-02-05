import * as _ from "lodash";
import { formatDate, DatePipe } from '@angular/common';
import { Constants } from 'src/app/constants.service';
import { CauUFService } from 'src/app/client/cau-uf-client/cau-uf-client.service';
import { LayoutsService } from '@cau/layout';
import { SecurityService } from '@cau/security';
import { MessageService } from '@cau/message';
import { ActivatedRoute, Router } from '@angular/router';
import { Component, OnInit } from '@angular/core';
import { UtilsService } from 'src/app/utils.service';

@Component({
    selector: 'app-acompanhar-impugnacao-resultado-uf-especifica',
    templateUrl: './acompanhar-impugnacao-resultado-uf-especifica.component.html',
    styleUrls: ['./acompanhar-impugnacao-resultado-uf-especifica.component.scss']
})
export class AcompanharImpugnacaoResultadoUfEspecificaComponent implements OnInit {

    public cauUf: any;
    public imagemBandeira: any = {
        imagem: '',
        sigla: ''
    }; 
    public impugnacoes: Array<any>;
    public _impugnacoes: Array<any> = []; 
    public limitePaginacao = 10;
    public limitesPaginacao: Array<number> = [];
    public search: string;
    public tipoProfissional;

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private cauUFService: CauUFService,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
        private datePipe: DatePipe
    ) {
        this.cauUf = route.snapshot.data["cauUf"];
        this.impugnacoes = route.snapshot.data["impugnacoes"];
        this.tipoProfissional = UtilsService.getValorParamDoRoute('tipoProfissional', this.route);
    }

    ngOnInit(): void {
        this.atribuirImagemBandeira();
        this._impugnacoes = _.cloneDeep(this.impugnacoes);
        this.limitesPaginacao = [10, 25, 50, 100];

        this.inicializaIconeTitulo();
    }

    /**
   * Inicializa ícone e título do header da página .
   */
    private inicializaIconeTitulo(): void {
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-fw fa-list',
            description: this.messageService.getDescription('LABEL_PARAMETRIZACAO_CALENDARIO_IMPUGNACAO_RESULTADO')
        });
    }

    /**
     * Busca imagem de bandeira do estado do CAUUF.
     *
     * @param idCauUf
     */
    public atribuirImagemBandeira(): void {
        if(this.cauUf){
            this.setImagemBandeira();
        } 
    }

    /**
     * Busca imagem de bandeira do estado do CAUUF.
     * 
     * @param idCauUf 
     */
    public setImagemBandeira(): void {

        this.imagemBandeira.imagem = this.cauUf.imagemBandeira;
        this.imagemBandeira.sigla = this.cauUf.descricao;

        if (this.cauUf.id === Constants.ID_CAUBR) {
            this.imagemBandeira.sigla = Constants.IES;
        }
    }

    /**
     * Retorna a classe de estilo de acordo com a situação do Pedido de impugnação de Resultado.
     * @param idSituacao 
     */
    public getClassStatusImpugnacaoResultado(idSituacao: number): string {
        return 'bg-status-impugnacao-resultado-' + idSituacao;
    }

     /**
     * Retorna descrição do status de acordo com a situação do Pedido de impugnação de Resultado.
     * @param idSituacao 
     */
    public getDescricaoStatusImpugnacaoResultado(idSituacao: number): string {
        let descricao = '';
        if(idSituacao == Constants.ID_STATUS_AGUARDANDO_ALEGACOES_IMPUGNACAO_RESULTADO){
            descricao = this.messageService.getDescription('LABEL_AGUARDANDO_ALEGACOES');
        }
        return descricao;
    }

    /**
     * Voltar para página de inicial.
     */
    public voltar(): void {
        if (this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA) {
            this.router.navigate(['/']);

        }
        else if (this.tipoProfissional == Constants.TIPO_PROFISSIONAL_COMISSAO) {
            this.router.navigate(['/eleicao/impugnacao-resultado/acompanhar-comissao']);

        } else if (this.tipoProfissional == Constants.TIPO_PROFISSIONAL) {
            this.router.navigate(['/eleicao/impugnacao-resultado/acompanhar']);
        }

    }

     /**
     * Filtra os dados da grid conforme o valor informado na variável search.
     *
     * @param search
     */
    public filter(search) {
        let filterItens = this._impugnacoes.filter((data) => {
            let textSearch = this.getSeachArray(data).join().toLowerCase();
            return textSearch.indexOf(search.toLowerCase()) !== -1
        });
        this.impugnacoes = filterItens;
    }

    /**
     * Cria array utilizado para buscar de termos na listagem.
     * 
     * @param obj
     */
    private getSeachArray(obj: any): Array<any> {
        let values: Array<any> = [];
        values.push(obj.numero);
        values.push(this.getDescricaoStatusImpugnacaoResultado(obj.idStatus));
        values.push(this.datePipe.transform(obj.dataCadastro, 'dd/MM/yyyy', '+0') + ' às ' + this.datePipe.transform(obj.dataCadastro, 'HH:mm', '+0'));
        values.push(obj.nomeProfissional);
        return values;
    }

    /**
     * Retorna a rota para detalhamento de substituição de membros
     * @param id 
     */
    public getRotaDetalhamento(impugnacao) {
        const idCauBR = impugnacao.idCauBR ? impugnacao.idCauBR : Constants.ID_IES;

        let ds_url = `profissional/visualizar`;
        if (this.tipoProfissional != Constants.TIPO_PROFISSIONAL) {
            ds_url = this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA ? 'chapa/visualizar' : 'comissao/visualizar';
        }

        this.router.navigate([`/eleicao/impugnacao-resultado/${idCauBR}/${ds_url}/${impugnacao.id}`]);
    }

}
