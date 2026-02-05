import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";
import { formatDate } from "@angular/common";
import { SecurityService } from '@cau/security';
import { SubstiuicaoChapaClientService } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-client.module';

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
    public cauUf: any;
    public dadosUf: any;
    public search: string;
    public pedidos: any = [];
    private calendarioId: any;
    private iesRoute: any;
    public limitesPaginacao = [];
    public dadosPedidos: any = [];
    public limitePaginacao: number;
    public solicitacoesSubstituicao = [];
    public _solicitacoesSubstituicao = null
    public idEleicao;
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
        private substituicaoChapaClient: SubstiuicaoChapaClientService
        
    ){
        this.cauUf = route.snapshot.data["cauUf"];
        this.dadosPedidos = route.snapshot.data["pedidos"];
        this.pedidos = this.dadosPedidos.pedidos;
        this.calendarioId = route.snapshot.params.idCalendario;

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

      this.limitePaginacao = 10;
      this.limitesPaginacao = [10, 15, 20, 50];
      this._solicitacoesSubstituicao = this.pedidos;
      this.usuario = this.securityService.credential["_user"];
      this.iesRoute = this.isIES();
      this.setImagemBandeira();
    } 

    /**
     * Ação que redireciona a página de acordo com o tipo de profisional
     */
    public voltar(): void { 
        const idCalendario = this.route.snapshot.paramMap.get('idCalendario');

        if(idCalendario) {
            this.router.navigate([`/eleicao/acompanhar-substituicao-ufs/${idCalendario}`]);    
        } else {
            this.router.navigate(['/']);    
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

        if(this.cauUf.id === Constants.ID_CAUBR){
            this.imagemBandeira.sigla = Constants.IES;
        }
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
        values.push(formatDate(obj.dataCadastro, 'dd/MM/yyyy mm:ss', 'en-US'));
        values.push(obj.chapaEleicao.numeroChapa);
        values.push(obj.numeroProtocolo);
        values.push(obj.statusSubstituicaoChapa.descricao);
        values.push(obj.chapaEleicao.membrosChapa.map(membro => membro.profissional.nome).join(" "))

        return values;
    }

    /**
     * Verifica se a chapa é IES.
     * 
     * @param chapa 
     */
    public isIES(): boolean {  
        return (this.dadosPedidos.idUf == 0 || this.dadosPedidos.idUf == Constants.ID_CAUBR)
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
        this.router.navigate([
            `/eleicao/substituicao-detalhamento/${id}`, 
            {
                calendarioId: this.calendarioId, 
                isIES: this.iesRoute
            }
        ]);

    }


}