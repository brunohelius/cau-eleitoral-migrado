import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";

/**
 * Componente responsável pela apresentação de listagem de Chapas pela UF Selecionada.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'listar-uf-especifica',
    templateUrl: './listar-uf-especifica.component.html',
    styleUrls: ['./listar-uf-especifica.component.scss']
})

export class ListarUfEspecifica implements OnInit {

    public usuario;
    public idEleicao;
    public cauUf: any;
    public dadosUf: any;
    public iesRoute: any;
    public search: string;
    public limitePaginacao: number;
    
    public chapa: any = [];
    public pedidos: any = [];
    public limitesPaginacao = [];
    public dadosPedidos: any = [];
    public idCalendario: any;
    
    public _pedidos = null
    
    public imagemBandeira = {
        imagem: '',
        sigla: ''
    }
    public teste: any
    
    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
    ) {
        this.cauUf = route.snapshot.data["cauUf"];
        this.pedidos = route.snapshot.data["chapas"];
        this.idCalendario = route.snapshot.params.idCalendario;
    }

    /**
     * Inicialização dos dados do campo
    */
    ngOnInit() {
        this.getTituloPagina();
        this.limitePaginacao = 10;
        this.limitesPaginacao = [10, 25, 50, 100];
        this._pedidos = this.pedidos;
        this.usuario = this.securityService.credential["_user"];
        this.setImagemBandeira();
        this.iesRoute = this.cauUf.id === Constants.ID_CAUBR 
    }

    /**
     * retona o título do módulo de julgamento final
     */
    public getTituloPagina(): void {
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-wpforms',
            description: this.messageService.getDescription('TITLE_JULGAMENTO')
        });
    }

    /**
     * Ação que redireciona a página de acordo com o tipo de profisional
     */
    public voltar(): void {
        const idCalendario = this.route.snapshot.paramMap.get('idCalendario');

        if (idCalendario) {
            this.router.navigate([`/eleicao/julgamento-final/acompanhar-ufs/${idCalendario}`]);
        } else {
            this.router.navigate(['/']);
        }
    }

    public redirecionaPendencias(idChapa: number): void{       
        this.router.navigate([
            `eleicao/julgamento-final/chapa-eleicao/listar-pendencias/${idChapa}`,{uf: this.cauUf.id, idCalendario: this.idCalendario }
        ]);
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
     * Retorna a quantidade todal de pedidos de substituição
     */
    public getTotalPedidos(dados: any[]) {
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
        let filterItens = this._pedidos.filter((data) => {
            let textSearch = this.getSeachArray(data).join().toLowerCase();
            return textSearch.indexOf(search.toLowerCase()) !== -1
        });
        this.pedidos = filterItens;
    }

    /**
     * Cria array utilizado para buscar de termos na listagem.
     * @param obj
     */
    private getSeachArray(obj: any): Array<any> {
        let values: Array<any> = [];
        values.push(obj.numeroChapa);
        values.push(obj.statusChapaVigente.descricao);
        values.push(obj.quantidadeMembrosComPendencia);
        values.push(obj.statusChapaJulgamentoFinal.descricao);
        values.push(obj.responsaveis.map(membro => membro.nome).join(" "))
        
        return values;
    }

    /**
     * Verifica se a chapa é IES.
     * 
     * @param chapa 
     */
    public isCandidaturaIES(obj: any): boolean {
        return obj.tipoCandidatura.descricao === Constants.IES;
    }

    /**
     * Retorna a rota para detalhamento de substituição de membros
     * @param id 
     */
    public getRotaDetalhamento(id) {
        this.router.navigate([
            `/eleicao/julgamento-final/chapaEleicao/${id}`,{ idCalendario: this.idCalendario }
        ]);
    }

    /**
     * Retorna a classe de estilo de acordo com a situação da chapa
     * @param idSituacao 
     */
    public getClassStatusChapa(idSituacao: number): string {
        let classe = '';
        return classe = idSituacao == 1 ? 'bg-warning': 'bg-primary';
    }

    /**
     * Retorna a classe de estilo de acordo com a situação dos membros
     * @param idSituacao 
     */
    public getClassStatusMembros(idSituacao: number): string {
        let classe = '';
        return classe = idSituacao > 0 ? 'font-color-warning': 'font-color-bg-primary';
    }

    /**
     * Retorna a classe de estilo de acordo com a situação do Julgamento
     * @param idSituacao 
     */
    public getClassStatusJuglamento(idSituacao: number): string {
        let classe = '';
        return classe = idSituacao == 1 ? 'bg-warning': 'bg-primary';
    }

    /**
     * Retorna o texto dividido por espaços
     * @param texto 
     */
    public getTextoQuebrado(texto: string): any {
        let resultado = texto.split(" ");
        return resultado;
    }

    /**
    * retorna a label da aba de acompanhar chapa com quebra de linha
    */
    public getTituloAbaAcompanharChapa(): any {
        return this.messageService.getDescription('TITLE_ABA_ACOMPANHAR_CHAPA', ['<div>', '</div><div>', '</div>']);
    }

    /**
     * retorna a label de status da chapa
     */
    public getLabelStatusDaChapa(): any {
        return  this.messageService.getDescription('LABEL_STATUS_CHAPA_QUEBRA_LINHA',['<div>','</div><div>','</div>']);
    }
    
    /**
     * retorna a label de quantidade de membros com pendência
     */
    public getLabelMembrosComPendencia(): any {
        return  this.messageService.getDescription('LABEL_MEMBROS_COM_PENDENCIA',['<div>','</div><div>','</div>']);
    }

    /**
     * retorna a label de pedidos cadastrados
     */
    public getLabelPedidosCadastrados(): any {
        return  this.messageService.getDescription('LABEL_PEDIDOS_CADASTRADOS',['<div>','</div><div>','</div>']);
    }

    /**
     * retorna a label de pedidos cadastrados
     */
    public getLabelStatusDoJulgamento(): any {
        return  this.messageService.getDescription('LABEL_STATUS_DO_JULGAMENTO',['<div>','</div><div>','</div>']);
    }
}