import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";
import { formatDate } from "@angular/common";
import { SecurityService } from '@cau/security';
import { SubstiuicaoChapaClientService } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-client.module';
import { CauUFService } from 'src/app/client/cau-uf-client/cau-uf-client.service';

/**
 * Componente responsável pela apresentação de listagem de Chapas por Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'acompanhar-substituicao-uf',
    templateUrl: './acompanhar-substituicao-uf.component.html',
    styleUrls: ['./acompanhar-substituicao-uf.component.scss']
})

export class AcompanharSubstituicaoUF implements OnInit {

    public usuario;
    public cauUfs = [];
    public dadosUf: any;
    public search: string;
    public pedidos: any = [];
    public limitesPaginacao = [];
    public dadosPedidos: any = [];
    public limitePaginacao: number;
    public solicitacoesSubstituicao = [];
    public _solicitacoesSubstituicao = null;
    public idCauUf: any;
    public imagemBandeira = {
        imagem: '',
        sigla: ''
    }
    private permissoes = [];
    public idUfRoute: any;
    public cauUf: any;
    public tipoProfissional: any;

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
        private cauUFService: CauUFService,
        private substituicaoChapaClient: SubstiuicaoChapaClientService

    ){
        this.cauUf = route.snapshot.data["cauUf"];
        this.dadosPedidos = route.snapshot.data["pedidos"];

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

      this.inicializaDados();
      this.inicializaControlePaginacao();
      this._solicitacoesSubstituicao = this.pedidos;
      this.usuario = this.securityService.credential["_user"];
      this.atribuirImagemBandeira();


      this.idUfRoute = this.route.snapshot.params.id;
    }

    /**
     * Ação que redireciona a página de acordo com o tipo de profisional
     */
    public voltar(): void {
        this.substituicaoChapaClient.getQuantidadePedidosParaCadaUf().subscribe(
            data => {
                this.router.navigate(['/eleicao/substituicao/acompanhar']);
            },
            error => {
                this.router.navigate(['/']);
            }
        );
    }

    /**
     * Método responsável por inicializar os dados dos pedidos tando para
     * membro da comissão quanto responsável da chapa
     */
    public inicializaDados(): void {

        this.tipoProfissional = this.getValorParamDoRoute('tipoProfissional');

        if(this.tipoProfissional != 'membroChapa') {
          this.pedidos = this.dadosPedidos.pedidos;
            this.idCauUf = this.getIdCauUfPedidos();
        } else {
          this.pedidos = this.dadosPedidos;
        }

    }

    /**
     * Inicializa os valores do controle de paginação
     */
    public inicializaControlePaginacao(): void {
        this.limitePaginacao = 10;
        this.limitesPaginacao = [, 10, 20, 30, 50];
    }

    /**
     * Busca imagem de bandeira do estado do CAUUF.
     *
     * @param idCauUf
     */
    public atribuirImagemBandeira(): void {
        if(this.tipoProfissional != 'membroChapa') {
            if(this.cauUf){
                this.setImagemBandeira();
            } else {
                let idCauUf = this.getIdCauUfPedidos();

                if (idCauUf) {
                    this.cauUFService.getBandeiraPorCauUF(idCauUf).subscribe(
                        data => {
                            this.cauUf = data;
                            this.setImagemBandeira();
                        },
                        error => {
                            this.messageService.addMsgDanger(error);
                        }
                    );
                }

            }

        }
    }

    /**
     * Seta imagem de bandeira do estado do CAUUF.
     */
    private setImagemBandeira() {
        this.imagemBandeira.imagem = this.cauUf.imagemBandeira;
        this.imagemBandeira.sigla = this.cauUf.descricao;

        if (this.cauUf.id === Constants.ID_CAUBR) {
            this.imagemBandeira.sigla = Constants.IES;
        }
    }

    /**
     * Pega o id da cau uf dos pedidos
     */
    private getIdCauUfPedidos(): any {
        let idCauUf = undefined;

        if(this.pedidos && this.pedidos.length > 0 ) {
            idCauUf = this.pedidos[0].chapaEleicao.idCauUf;
        }
        return idCauUf;
    }


    /**
     * Retorna a quantidade todal de pedidos de substituição
     */
    public getTotalPedidos(dados: any []) {
        let soma = 0;
        dados.forEach((valor) => {
            soma += valor.quantidadeSubstituicao;
        });

        return soma;
    }

    /**
     * Filtra os dados da grid conforme o valor informado na variável search.
     *
     * @param search
     */
    public filter(search) {
        let filterItens = this._solicitacoesSubstituicao.filter((data) => {
            let textSearch = this.getSeachArray(data).join().toLowerCase();
            return textSearch.indexOf(search.toLowerCase()) !== -1
        });
        this.pedidos = filterItens;
    }

    /**
     * Cria array utilizado para buscar de termos na listagem.
     *
     * @param obj
     */
    private getSeachArray(obj: any): Array<any> {
        let values: Array<any> = [];
        values.push(obj.numeroProtocolo);
        values.push(obj.statusSubstituicaoChapa.descricao);
        values.push(formatDate(obj.dataCadastro, 'dd/MM/yyyy mm:ss', 'en-US'));

        if(this.tipoProfissional != 'membroChapa') {
            values.push(obj.chapaEleicao.numeroChapa);
            values.push(obj.chapaEleicao.membrosChapa.map(membro => membro.profissional.nome).join(" "));
        } else {
            values.push(obj.membroSubstitutoTitular.profissional.nome);
            values.push(obj.membroSubstituidoTitular.profissional.nome);
            values.push(obj.membroSubstitutoSuplente.profissional.nome);
            values.push(obj.membroSubstituidoSuplente.profissional.nome);
        }

        return values;
    }


    /**
     * Verifica se a chapa é IES.
     *
     * @param chapa
     */
    public isIES(): boolean {
        return this.dadosPedidos.idUf && (this.dadosPedidos.idUf == 0 || this.dadosPedidos.idUf == Constants.IES)
    }

    /**
     * verifica se o status do pedido de substituição está em andamento
     * @param idSituacao
     */
    public isAndmento(idSituacao: number) {
        return idSituacao == Constants.EM_ANDAMENTO || idSituacao == Constants.SUBSTITUICAO_EM_ANDAMENTO_RECURDO
    }

    /**
     * verifica se o status do pedido de substituição foi deferido
     * @param idSituacao
     */
    public isDeferido(idSituacao: number) {
        return idSituacao == Constants.DEFERIDO || idSituacao == Constants.SUBSTITUICAO_DEFERIDO_RECURSO
    }

     /**
     * verifica se o status do pedido de substituição foi indeferido
     * @param idSituacao
     */
    public isIndeferido(idSituacao: number) {
        return idSituacao == Constants.INDEFERIDO || idSituacao == Constants.SUBSTITUICAO_INDEFERIDO_RECURSO
    }

    /**
     * Retorna a rota para detalhamento de substituição de membros
     * @param id
     */
    public getRotaDetalhamento(id) {

        let url = `/eleicao/substituicao/responsavel-chapa-detalhamento/${id}`;

        if(this.tipoProfissional != 'membroChapa') {
            url = `/eleicao/substituicao/detalhamento/${id}`;
        }

        return url ;
    }



    /**
     * Retorna um valor de parâmetro passado na rota
     * @param nameParam
     */
    private getValorParamDoRoute(nameParam) {
        let data = this.route.snapshot.data;

        let valor = undefined;

        for (let index of Object.keys(data)) {
            let param = data[index];

            if (param !== null && typeof param === 'object' && param[nameParam] !== undefined) {
                valor = param[nameParam];
                break;
            }
        }
        return valor;
    }

}