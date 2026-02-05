import { Component, OnInit, TemplateRef, EventEmitter } from '@angular/core';
import { SecurityService } from '@cau/security';
import { ActivatedRoute, Router } from '@angular/router';
import { AtividadeSecundariaClientService } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-client.service';
import { MessageService } from '@cau/message';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { NgForm } from '@angular/forms';
import * as _ from 'lodash';
import { LayoutsService } from '@cau/layout';
import { DatePipe } from '@angular/common';

@Component({
  selector: 'app-form-numero-conselheiro',
  templateUrl: './form-numero-conselheiro.component.html',
  styleUrls: ['./form-numero-conselheiro.component.scss']
})
export class FormNumeroConselheiroComponent implements OnInit {
  public tab: any;
  public calendario: any;
  public cauUfs: any;
  public limitesPaginacao: number[] = [10, 20, 30];
  public limitePaginacao: number = 10;
  public profissionais: any[];
  private profissionaisAux: any[];
  private idAtividadeSecundaria: any;
  public ufFiltro: any = "";
  public modalRef: BsModalRef;
  public submitted: boolean = false;
  public justificativa: string;
  public justificativaAtualiza: string;
  private proporcaoConselheiroEdit: any;
  public qtdProporcaoConselheiroEdit: number;
  public totalProfAtivos: number;
  public totalProf: number;
  private hasJustificativa: boolean = false;
  private hasIniciadaAtividadeChapa: boolean = false;
  public search: string;

  constructor(
    private layoutsService: LayoutsService,
    private route: ActivatedRoute,
    private router: Router,
    private atividadeSecundariaService: AtividadeSecundariaClientService,
    private messageService: MessageService,
    private modalService: BsModalService,
    private datePipe: DatePipe
  ) { }

  ngOnInit() {
    this.layoutsService.onLoadTitle.emit({
      description: `${this.messageService.getDescription('LABEL_NUMERO_CONSELHEIRO')}`,
      icon: 'fa fa-wpforms'
    });
    this.calendario = this.route.snapshot.data['calendario'];
    this.setTotalProfissional(this.route.snapshot.data['profissionais']);
    this.cauUfs = this.getCauUfs();
    this.idAtividadeSecundaria = this.route.snapshot.paramMap.get('id');
    this.tab = {
      tabNumeroConselheiro: { active: true },
      tabExtrato: {},
      tabHistorico: {}
    };
    this.tab.current = this.tab.tabNumeroConselheiro;
  }

  /**
   * Retorna uma lista de CAUUFs baseada na lista de proporção de conselheiro
   */
  public getCauUfs(): any[] {
    let cauUfs = [];
    this.profissionaisAux.forEach(profissional => {
      cauUfs.push({
        id: profissional.idCauUf,
        prefixo: profissional.prefixo,
        descricao: profissional.descricao
      });
    });

    return cauUfs;
  }

  /**
   * Ajusta os dados para exibição na listagem
   */
  public parseDataTotalProfissional() {
    this.profissionais.forEach(totalProfissionais => {
      for (let key in totalProfissionais) {
        if (typeof totalProfissionais[key] === 'number' && totalProfissionais[key] === 0) {
          totalProfissionais[key] = '-';
        }
      }
      if(!totalProfissionais['lei']){
        totalProfissionais.lei = {
          'id' : '-',
          'descricao': '-'
        }
      }
    });
  }

  /**
   * Seta os dados da listagem de parametro de total de profissionais
   * @param dataTotalProfissional
   */
  public setTotalProfissional(dataTotalProfissional: any): void {
    
    this.profissionais = _.orderBy(dataTotalProfissional.listaProfissionais, ['prefixo'], ['asc']);
    this.parseDataTotalProfissional();
    this.profissionaisAux = this.profissionais;
    this.totalProfAtivos = dataTotalProfissional.totalProfAtivos;
    this.totalProf = dataTotalProfissional.totalProf;
    if (dataTotalProfissional.hasJustificativa) {
      this.hasJustificativa = dataTotalProfissional.hasJustificativa;
    }
    if (dataTotalProfissional.hasIniciadaAtividadeChapa) {
      this.hasIniciadaAtividadeChapa = dataTotalProfissional.hasIniciadaAtividadeChapa;
    }
  }

  /**
     * Método que realiza o controle das abas.
     *
     * @param selectTab
     * @param nomeAba
     */
  public onSelect(selectTab: any): void {
    this.habilitarAba(selectTab);
  }

  /**
     * Método responsável por habilitar as abas.
     *
     * @param selectTab
     */
  public habilitarAba(selectTab: any): void {
    this.tab.current.active = false;
    selectTab.active = true;
    this.tab.current = selectTab;
  }

  /**
   * Filtra os dados da grid conforme o valor informado na variável search.
   *
   * @param search
   */
  public filter(search): void {
    let filterItens = this.profissionaisAux.filter((data) => {
      let textSearch = this.getSeachArray(data).join().toLowerCase();
      return textSearch.indexOf(search.toLowerCase()) !== -1;
    });
    this.profissionais = filterItens;
  }

  /**
   * Cria array utilizado para buscar de termos na listagem.
   *
   * @param obj
   */
  private getSeachArray(obj: any): Array<any> {
    let values: Array<any> = [];
    values.push(obj.prefixo);
    values.push(obj.lei.descricao);
    values.push(obj.qtdProfissional);
    values.push(obj.situacaoEditado ? this.messageService.getDescription('LABEL_SIM') : this.messageService.getDescription('LABEL_NAO'));
    values.push(obj.numeroProporcaoConselheiro);
    return values;
  }

  /**
   * Recupera a lista de totais de profissionais pela uf selecionada no filtro
   */
  public filterPorUf(): void {
    let filtro = {
      idAtividadeSecundaria: Number(this.idAtividadeSecundaria),
      idsCauUf: this.ufFiltro ? [Number(this.ufFiltro)] : ''
    }
    this.atividadeSecundariaService.getQuantidadeProfissionalPorCauUf(filtro).subscribe(data => {
      this.setTotalProfissional(data);
    }, error => {
      this.messageService.addMsgDanger(error.message);
    })
  }

  /**
   * Abre a modal de alteração de proporção de conselheiro
   * @param conselheiro
   * @param template
   */
  public alterarProporcaoConselheiro(conselheiro: any, template: TemplateRef<any>): void {
    this.limpaCamposAlteracao();
    this.proporcaoConselheiroEdit = conselheiro;
    if (this.hasIniciadaAtividadeChapa) {
      const message = `<p>${this.messageService.getDescription('MSG_ALERTA_ATIVIDADE_CRIAR_CHAPA')} </p>
        ${this.messageService.getDescription('MSG_DESEJA_ALTERAR_PROPORCAO')}`;
      this.messageService.addConfirmYesNo(message, () => {
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal modal-lg' }));
      });
    } else {
      this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal modal-lg' }));
    }
  }

  /**
   * Verifica a situação de edição da proporção de conselheiros
   * @param profissional
   */
  public verificaStatusEditado(profissional: any): string {
    let statusEditado: string;
    if (profissional.situacaoEditado) {
      statusEditado = `<span class="text-danger">${this.messageService.getDescription('LABEL_SIM')}</span>`;
    } else if (!profissional.situacaoEditado && (typeof profissional.qtdProfissional === 'number' || profissional.descricao === 'IES')) {
      statusEditado = `<span >${this.messageService.getDescription('LABEL_NAO')}</span>`;
    } else {
      statusEditado = "<span>-</span>";
    }
    return statusEditado;
  }

  /**
   * Atualiza as proporções de conselheiro
   */
  public atualizarTotaisMembro(template: TemplateRef<any>) {
    this.submitted = false;
    this.limpaCamposAlteracao();
    let msgConfirma = this.messageService.getDescription('MSG_ATUALIZAR_NUMERO_PROFISSIONAIS');
    if (this.hasJustificativa && this.hasIniciadaAtividadeChapa) {
      msgConfirma = `<p>${this.messageService.getDescription('MSG_ALERTA_ATIVIDADE_CRIAR_CHAPA')} </p>
        ${this.messageService.getDescription('MSG_DESEJA_ATUALIZAR_PROPORCAO')}`;
      this.messageService.addConfirmYesNo(msgConfirma, () => {
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal modal-lg' }));
      });
    } else if (this.hasJustificativa) {
      this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal modal-lg' }));
    } else {
      this.executaAtualizarTotaisProfissionais();
    }

  }

  /**
   * Metodo que executa a atualização dos totais de profissionais
   */
  public executaAtualizarTotaisProfissionais(justificativa?: string): void {
    this.submitted = false;
    let params = {
      idAtividadeSecundaria: this.idAtividadeSecundaria
    }
    if (justificativa) {
      params['justificativa'] = justificativa;
    }
    let msgConfirma = this.messageService.getDescription('MSG_ATUALIZAR_NUMERO_PROFISSIONAIS');
    this.messageService.addConfirmYesNo(msgConfirma, () => {
      this.confirmaExecutaAtualizacaoTotaisProfissionais(params);
    }, () => {
      this.limpaCamposAlteracao();
    });
  }

  /**
   * Executa a chamada do serviço de atualização da proporção de conselheiros
   * @param params
   */
  public confirmaExecutaAtualizacaoTotaisProfissionais(params: any): void {
    this.atividadeSecundariaService.atualizaNumeroConselheiro(params).subscribe(data => {
      var dataAtual = this.datePipe.transform(Date.now(), 'dd/MM/yyyy');
      this.messageService.addMsgSuccess(
        this.messageService.getDescription('MSG_SOLICITACAO_SENDO_EXECUTADA_AGUARDAR_ATUALIZACAO')
      );
      this.setTotalProfissional(data);
      this.modalRef.hide();
    }, error => {
      this.messageService.addMsgDanger(error.message);
    });
  }

  /**
   * Limpa os campos referente as modais de justificativa e alteração de proporção
   */
  public limpaCamposAlteracao() {
    this.submitted = false;
    this.justificativaAtualiza = "";
    this.justificativa = "";
    this.qtdProporcaoConselheiroEdit = null;
  }

  /**
   * Atualiza uma proporção de conselheiro especifica de um CAU/UF
   * @param form
   */
  public salvarAlteracaoJustificativa(form: NgForm) {
    this.submitted = true;
    if (form.valid) {
      this.modalRef.hide();
      this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_ALTERAR_DADOS', () => {
        this.submitted = false;
        this.proporcaoConselheiroEdit.situacaoEditado = true;
        this.proporcaoConselheiroEdit.qtdAtual = this.proporcaoConselheiroEdit.numeroProporcaoConselheiro;
        this.proporcaoConselheiroEdit.numeroProporcaoConselheiro = this.qtdProporcaoConselheiroEdit;
        this.proporcaoConselheiroEdit.justificativa = this.justificativa;
        this.atividadeSecundariaService.salvarProporcaoConselheiro(this.proporcaoConselheiroEdit).subscribe(data => {
          this.messageService.addMsgSuccess(this.messageService.getDescription('LABEL_DADOS_ALTERADOS_SUCESSO'));
          this.modalRef.hide();
        }, error => {
          this.submitted = false;
          this.messageService.addMsgDanger(error.message);
        });
      }, () => {
        this.modalRef.hide();
      })
    }
  }

  /**
   * Salva justificativa da atualização de proporção de profissionais
   * @param form
   */
  public salvarJustificativaAtualiza(form: NgForm) {
    this.submitted = true;
    if (form.valid) {
      this.executaAtualizarTotaisProfissionais(this.justificativaAtualiza);
    }
  }

  /**
   * Voltar para a listagem de calendarios
   */
  public voltarLista() {
    this.router.navigate([`eleicao/${this.calendario.id}/atividade-principal/lista`]);
  }

  /**
   * Realiza o download da exportação da listagem da proporção de conselheiros em XLSX (Excel) ou PDF
   * @param event
   * @param file (pdf || xls)
   */
  public gerarFile(event: EventEmitter<any>, file: string): void {
    this.atividadeSecundariaService[`gerarConselheirosFile${file}`](this.idAtividadeSecundaria).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      console.error(error);
      this.messageService.addMsgDanger('MSG_ERRO_COMUNICACAO_TENTE_MAIS_TARDE');
    });
  }

}
