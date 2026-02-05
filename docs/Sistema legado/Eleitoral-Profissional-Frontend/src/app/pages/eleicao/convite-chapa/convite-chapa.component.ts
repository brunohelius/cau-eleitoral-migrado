import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { SecurityService } from '@cau/security';
import { ActivatedRoute } from '@angular/router';

@Component({
    selector: 'app-convite-chapa',
    templateUrl: './convite-chapa.component.html',
    styleUrls: []
})
export class ConviteChapaComponent implements OnInit {

    public tabs: any;
    public convites: Array<any>;
    public conviteSelecionado: any;
    public curriculo: any;
    public declaracoes: any;
    public declaracaoParticipacaoEleicao: any;
    public usuario: any;

    /**
     * Construtor da classe.
     *
     * @param route
     */
    constructor(
        private route: ActivatedRoute,
        private securtyService: SecurityService,
    ) {
        this.convites = route.snapshot.data["convites"];
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.usuario = this.securtyService.credential["_user"];
        this.inicializarTabs();
    }

    /**
     * Mudar tab selecionada.
     *
     * @param tab
     */
    public mudarTab(tab: string): void{
        this.tabs.listar.ativo = this.tabs.listar.nome == tab;
        this.tabs.curriculo.ativo = this.tabs.curriculo.nome == tab;
        this.tabs.declaracao.ativo = this.tabs.declaracao.nome == tab;
        this.tabs.representatividade.ativo = this.tabs.representatividade.nome == tab;
    }

    /**
     * Recebe evento de aceitar convite para participação da chapa.
     *
     * @param conviteSelecionado
     */
    public aceitarConvite(conviteSelecionado: any): void{
        this.conviteSelecionado = conviteSelecionado;
        this.mudarTab('curriculo');
    }

    /**
     * Recebe evento de salvar currículo.
     *
     * @param curriculo
     */
    public salvarCurriculo(curriculo: any): void{
        this.curriculo = curriculo;
        this.mudarTab('declaracao');
    }

    /**
     * Recebe evento de confirmação de preenchimento de declaração de participação de eleição.
     * 
     * @param declaracao 
     */
    public confirmarDeclaracaoParticipacaoEleicao(declaracoes: any): void{
        this.declaracoes = declaracoes;
        this.mudarTab('representatividade');
    }

    /**
     * Voltar para a pagina de listagem de convites.
     */
    public voltarListagemConvites(){
        this.curriculo = undefined;
        this.conviteSelecionado = undefined;
        this.mudarTab('listar');
    }

    /**
     * Volta para o formulário de Currículo do membro da chapa.
     */
    public voltarCurriculoMembro(){
        this.mudarTab('curriculo');
    }

    public cancelarCurriculoMembro(){
        this.curriculo = undefined;
        this.declaracaoParticipacaoEleicao = undefined;
        this.mudarTab('listar');
    }

    /**
     * Inicializa lista tabs.
     */
    public inicializarTabs(): void {
        this.tabs = {
            listar: { nome: 'listar', ativo: true },
            curriculo: { nome: 'curriculo', ativo: false },
            declaracao: { nome: 'declaracao', ativo: false },
            representatividade: { nome: 'representatividade', ativo: false }
        };
    }
}