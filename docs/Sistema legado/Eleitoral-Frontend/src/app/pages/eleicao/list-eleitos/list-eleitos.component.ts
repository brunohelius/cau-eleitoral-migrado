import * as _ from "lodash";
import { ActivatedRoute, NavigationExtras, Router} from "@angular/router";
import { MessageService } from '@cau/message';
import { Component, OnInit, EventEmitter } from '@angular/core';
import { LayoutsService } from '@cau/layout';
import { SecurityService } from '@cau/security';

import { UFClientService } from '../../../client/uf-client/uf-client.service';
import { CalendarioClientService } from '../../../client/calendario-client/calendario-client.service';
import { MembroChapaService } from '../../../client/membro-chapa-client/membro-chapa.service';
import { DiplomaEleitoralService } from '../../../client/diploma-eleitoral-client/diploma-eleitoral-client.service';
import { TermoDePosseService } from '../../../client/termo-de-posse-client/termo-de-posse-client.service';
import { NgForm } from "@angular/forms";

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'list-eleitos',
    templateUrl: './list-eleitos.component.html',
    styleUrls: ['./list-eleitos.component.scss']
})
export class ListEleitosComponent implements OnInit {
    public filtro: any;

    public ufs: any;
    public anos: any;

    public submitted: boolean = false;
    public resultado: any = [];
    public filtros: any = [];
    public query: any = '';
    public permitirTermo: boolean = false;
    
    public filtroPesquisa: any = '';
    page = 1;
    pageSize = 10;
    totalRecords: number;

    public user: any;

    /**
     * Construtor da classe.
     *
     * @param messageService
     * @param calendarioClientService
     * @param modalService
     */
    constructor(
        private messageService: MessageService,
        private ufService: UFClientService,
        private calendarioClientService: CalendarioClientService,
        private membroChapaService: MembroChapaService,
        private diplomaEleitoralService: DiplomaEleitoralService,
        private router: Router,
        private securityService: SecurityService,
        private termoDePosseService: TermoDePosseService,
        private layoutsService: LayoutsService,
    ) {
        this.filtroPesquisa = {};
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.layoutsService.onLoadTitle.emit({
            description: this.messageService.getDescription(
                'TITLE_PESQUISAR_DIPLMA_TERMO'
            ),
          });
        this.user = this.securityService.credential["_user"];
        this.filtro = {
            idFilial: null,
            cpf: null,
            nome: null,
            representacao: null,
            ano: null,
            tipoConselheiro: null,
            ordenar: null
        };
        
        this.initFiltros();
        this.initUfs();
        this.permitirTermoPosse();
    }

    /**
     * Inicia filtros com dados padrão
     */
    public initFiltros(): void {
        this.calendarioClientService.getAnos().subscribe(data => {
            const index = data.findIndex(x => x.eleicao.ano < 2021);  
            data.splice(index, 1);
            this.anos = data;
            this.filtro.ano = data[data.length - 1].eleicao.ano;
            this.filtro.representacao =  this.user.cauUf.id != 165 ? '' : 1;
            this.filtro.ordenar = 1;
            this.filtro.idFilial = this.user.cauUf.id != 165 ? this.user.cauUf.id : null;
            this.pesquisar();
        }, error => {
            this.messageService.addMsgDanger(error);
        })
    }

    /**
     * Inicia Unidades federativas 
     */
    public initUfs(): void {
        let filtroFilial = {
            tipoFilial: 7
        };
        
        this.ufService.getFilial(filtroFilial).subscribe(
            data => {
                const index = data.findIndex(x => x.id == 165);  
                data.splice(index, 1);
                this.ufs = data;
            },
            error => {
                const message = error.message || error.description;
                this.messageService.addMsgDanger(message);
            }
        );
    }

    /**
     * Limpa dados da pesquisa para padrão
     */
    public limparPesquisa(): void {
        this.submitted = false;
        this.filtro = {
            uf: null,
            cpf: null,
            nome: null,
            representacao: null,
            tipoConselheiro: null,
            ordenar: null
        };
    }

    /**
     * Validar pesquisar; Campos obrigatórios
     */
    public validarPesquisar(form: NgForm): void {
        this.submitted = true;
        if (!form.valid) {
            this.messageService.addMsgDanger('MSG_CAMPOS_OBRIGATORIOS');
            return;
        }
        this.pesquisar();
    }
        
    /**
     * Pesquisar membros eleitos
     */
    public pesquisar(): void {
        this.membroChapaService.getEleitoByFilter(this.filtro).subscribe(
            data => {
                this.resultado = data;
                if (this.filtro.ordenar == 1) {
                    this.ordenarResultado();
                }
            },
            error => {
                this.resultado = [];
                const message = error.message || error.description;
                this.messageService.addMsgDanger(message);
            }
        );
        this.setFiltros();
    }

    /**
     * Busca lista de filtros utilizados
     */
    public setFiltros(): void {
        this.filtros = [];

        if (this.filtro.idFilial) {
            this.filtros.push('UF');
        }

        if (this.filtro.cpf) {
            this.filtros.push('CPF');
        }

        if (this.filtro.nome) {
            this.filtros.push('Nome');
        }

        if (this.filtro.representacao) {
            this.filtros.push('Representação');
        }

        if (this.filtro.ano) {
            this.filtros.push('Ano');
        }

        if (this.filtro.tipoConselheiro) {
            this.filtros.push('Tipo de Conselheiro');
        }
    }

    /**
     * Ordena a lista por uf
     */
    public ordenarResultado(): void {
        this.resultado.sort((a, b) => a.uf > b.uf ? 1 : -1);
    }

    /**
     * Abrir página termo de posse
     */
    public abrirTermoDePosse(membro): void {
        const navigationExtras: NavigationExtras = {state: { membro }};
        this.router.navigate([`/eleicao/termo-de-posse/`], navigationExtras);
    }

     /**
     * Abrir página termo de posse
     */
     public abrirDiplomaEleitoral(membro): void {
        const navigationExtras: NavigationExtras = {state: { membro }};
        this.router.navigate([`/eleicao/diploma-eleitoral/`], navigationExtras);
    }

     /**
     * Download do Diploma Eleitoral
     */
    public baixarDiplomaEleitoral(idDiploma): void {
        this.diplomaEleitoralService.imprimir(idDiploma).subscribe(
            data => {
              var file = new Blob([data.body], { type: 'application/pdf' });
              var fileURL = window.URL.createObjectURL(file);
              window.open(fileURL, '_blank');
            },
            error => {
              const message = error.message || error.description;
              this.messageService.addMsgDanger(message);
            }
          );
    }

    
     /**
     * Download Termo de Posse
     */
     public baixarTermoDePosse(idTermo): void {
        this.termoDePosseService.imprimir(idTermo).subscribe(
            data => {
              var file = new Blob([data.body], { type: 'application/pdf' });
              var fileURL = window.URL.createObjectURL(file);
              window.open(fileURL, '_blank');
            },
            error => {
              const message = error.message || error.description;
              this.messageService.addMsgDanger(message);
            }
          );
    }

     /**
     * Verifica permissão para gerar Termo de posse
     */
    public permitirTermoPosse(): void {
        this.permitirTermo = this.securityService.hasRoles('01603002') ? true : false;
    }

    /**
     * Ajusta campo de pesquisa para letras e numeros
     */
    public validarCaracterEspecial(): void {
        var er = /[^a-z0-9]/gi;
		this.query = this.query.replace(er, "");
    }
}
