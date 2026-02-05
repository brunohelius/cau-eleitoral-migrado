import { LayoutsService } from '@cau/layout';
import { formatDate } from "@angular/common";
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";
import { ImpugnacaoCandidaturaClientService } from 'src/app/client/impugnacao-candidatura-client/impugnacao-candidatura-client.service';

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

export class AcompanharImpugnacaoUfEspecificaComponent implements OnInit {

    public usuario;
    public cauUfs = [];
    public dadosUf: any;
    public cauUf: any;
    public search: string;
    public idUfRoute: any;
    public pedidos: any = [];
    public idCalendario: any;
    public limitesPaginacao = [];
    public dadosPedidos: any = [];
    public limitePaginacao: number;
    public solicitacoesSubstituicao = [];
    public _solicitacoesImpugnacao = null;
    public isAssessorUf: boolean = false;
    public imagemBandeira = {
        imagem: '',
        sigla: ''
    }


    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
        private pedidosImpugnacaoChapa: ImpugnacaoCandidaturaClientService

    ){

        this.cauUf = route.snapshot.data["cauUf"];

        this.dadosPedidos = route.snapshot.data["pedidos"];
        this.pedidos = this.dadosPedidos.pedidosImpugnacao;
        this.idCalendario = this.dadosPedidos.idCalendario;
        this.setImagemBandeira();

        if (this.securityService.hasRoles(Constants.ROLE_ACESSOR_CE) && !this.securityService.hasRoles(Constants.ROLE_ACESSOR_CEN)) {
            this.isAssessorUf = true
        }
    }

    /**
     * Inicialização dos dados do campo
    */
    ngOnInit() {
        this.inicializaIconeTitulo();
        this.limitePaginacao = 10;
        this.limitesPaginacao = [10, 20, 30, 50];
        this._solicitacoesImpugnacao = this.pedidos;
        this.usuario = this.securityService.credential["_user"];
        this.idUfRoute = this.route.snapshot.params.id;
    }

    /**
     * Inicializa ícone e título do header da página .
     */
    private inicializaIconeTitulo(): void {
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-user',
            description: this.messageService.getDescription('TITLE_PEDIDO_DE_IMPUGNACAO')
        });
    }

    /**
     * Ação que redireciona a página de acordo com a permissão do usuário
     * na rota de acompanhar.
     */
    public voltar(): void {
        if (this.isAssessorUf) {
            this.router.navigate([`/eleicao/impugnacao/acompanhar`]);
        } else {
            this.router.navigate([`/eleicao/impugnacao/acompanhar-impugnacao/${this.idCalendario}`]);
        }

    }

    /**
     * Método que retorna a rota de detalhamento da solicitação selecionada
     * @param id
     */
    public redirecionaDetalhamento(id: number): void {
        this.router.navigate([`/eleicao/impugnacao/${id}/detalhar`, {
            idCalendario: this.idCalendario
        }]);
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
     * Retorna a rota para detalhamento de substituição de membros
     * @param id
     */
    public getRotaDetalhamento(id) {
        return `/`;
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

}