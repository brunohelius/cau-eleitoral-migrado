import { LayoutsService } from '@cau/layout';
import { formatDate } from "@angular/common";
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";
import { AcompanharImpugnacaoClientService } from 'src/app/client/impugnacao-client/impugnacao-client.service';
import { CauUFService } from 'src/app/client/cau-uf-client/cau-uf-client.service';

/**
 * Componente responsável pela apresentação de listagem de Chapas por Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'acompanhar-impugnacao-uf',
    templateUrl: './acompanhar-impugnacao-uf-especifica.component.html',
    styleUrls: ['./acompanhar-impugnacao-uf-especifica.component.scss']
})

export class AcompanharImpugnacaoUfEspecifica implements OnInit {

    public usuario;
    public cauUfs = [];
    public dadosUf: any;
    public cauUf: any;
    public search: string;
    public idUfRoute: any;
    public pedidos: any = [];
    public limitesPaginacao = [];
    public dadosPedidos: any = [];
    public limitePaginacao: number;
    public solicitacoesSubstituicao = [];
    public _solicitacoesImpugnacao = null
    public imagemBandeira = {
        imagem: '',
        sigla: ''
    }

    constructor(
        private cauUFService : CauUFService,
        private router: Router,
        private route: ActivatedRoute,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
        private pedidosImpugnacaoChapa: AcompanharImpugnacaoClientService

    ){
        this.cauUf = route.snapshot.data["cauUf"];

        this.dadosPedidos = route.snapshot.data["pedidos"];
        this.pedidos = this.dadosPedidos.pedidosImpugnacao;
        this.setImagemBandeira();
    }

    /**
     * Inicialização dos dados do campo
    */
    ngOnInit() {

      this.layoutsService.onLoadTitle.emit({
          icon: 'fa fa-user',
          description: this.messageService.getDescription('TITLE_PEDIDO_DE_IMPUGNACAO')
      });

      this.limitePaginacao = 10;
      this.limitesPaginacao = [10, 25, 50];
      this._solicitacoesImpugnacao = this.pedidos;
      this.usuario = this.securityService.credential["_user"];
      this.idUfRoute = this.route.snapshot.params.id;
    }


    /**
     * Ação que redireciona a página de acordo com a permissão do profissional
     * na rota de acompanhar.
     */
    public voltar(): void {
        let tipoProfissional = this.getValorParamDoRoute('tipoProfissional');

        if (tipoProfissional == Constants.TIPO_PROFISSIONAL){
            this.router.navigate(['/eleicao/impugnacao/acompanhar-profissional-solicitante']);
        } else {
            this.pedidosImpugnacaoChapa.pedidosImpugnacaoChapa().subscribe(
                data => {
                    this.router.navigate(['/eleicao/impugnacao/acompanhar']);
                },
                error => {
                    this.router.navigate(['/']);
                }
            );
        }
    }

    /**
     * Método que retorna a rota de detalhamento da solicitação selecionada
     * @param id
     */
    public redirecionaDetalhamento(id: number): void {

        let tipoProfissional = this.getValorParamDoRoute('tipoProfissional');

        let ds_url = `detalhar-solicintante`;
        if (tipoProfissional != Constants.TIPO_PROFISSIONAL) {
            ds_url = tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA ? 'detalhar-responsavel' : 'detalhar';
        }

        this.router.navigate(
            [
                `/eleicao/impugnacao/${id}/${ds_url}`,
                {
                    visualizar: true,
                    isIES: (this.idUfRoute == 0)||(this.idUfRoute == Constants.ID_CAUBR),
                }

            ],
            {
                state: {
                    isIES: (this.idUfRoute == 0)||(this.idUfRoute == Constants.ID_CAUBR),
                }
            }
        );

    }


    /**
     * Verifica qual é o status da solicitação e retorna a classe de acordo com as seguintes opções:
     * bg-warning: Em análise
     * colorCircle: Deferido
     * bg-danger: Indeferido
     * @param id
     */
    public status(id: number): String {
        let classe = "bg-warning";

        if (id === Constants.STATUS_IMPUGNACAO_PROCEDENTE || id === Constants.STATUS_IMPUGNACAO_RECURSO_PROCEDENTE) {
            classe = "colorCircle"
        }
        if (id === Constants.STATUS_IMPUGNACAO_IMPROCEDENTE || id === Constants.STATUS_IMPUGNACAO_RECURSO_IMPROCEDENTE) {
            classe = "bg-danger"
        }
        return classe;
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

        let filterItens = this._solicitacoesImpugnacao.filter((data) => {
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
        values.push(obj.protocolo);
        values.push(obj.impugnante);
        values.push(obj.numeroChapa);
        values.push(obj.statusImpugnacao);
        values.push(obj.responsaveis.map(membro => membro.nome).join(" "))
        values.push(formatDate(obj.dataCadastro, 'dd/MM/yyyy mm:ss', 'en-US'));

        return values;
    }


    /**
     * Verifica se a chapa é IES.
     *
     * @param chapa
     */
    public isIES(): boolean {
        return (this.dadosPedidos.pedidosImpugnacao.idUf == 0 || this.dadosPedidos.pedidosImpugnacao.idUf == Constants.ID_CAUBR)
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


    /**
     * Busca imagem de bandeira do estado do CAUUF.
     *
     * @param idCauUf
     */
    public setImagemBandeira(): void {
        let tipoProfissional = this.getValorParamDoRoute('tipoProfissional');

        if (tipoProfissional == Constants.TIPO_PROFISSIONAL_COMISSAO) {
            this.imagemBandeira.imagem = this.cauUf.imagemBandeira;
            this.imagemBandeira.sigla = this.cauUf.descricao;

            if (this.cauUf.id === Constants.ID_CAUBR) {
                this.imagemBandeira.sigla = Constants.IES;
            }
        }
    }

}