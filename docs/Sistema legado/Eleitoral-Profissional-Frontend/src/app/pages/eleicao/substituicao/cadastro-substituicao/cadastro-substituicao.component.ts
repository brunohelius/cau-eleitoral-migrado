import { NgForm } from '@angular/forms';
import { LayoutsService } from '@cau/layout';
import { CKEditor4 } from 'ckeditor4-angular';
import { MessageService } from '@cau/message';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, EventEmitter, OnInit, TemplateRef, ViewChild } from '@angular/core';

import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';

import { SubstiuicaoChapaClientService } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-client.module';


@Component({
  selector: 'app-cadastro-substituicao',
  templateUrl: './cadastro-substituicao.component.html',
  styleUrls: ['./cadastro-substituicao.component.scss']
})
export class CadastroSubstituicaoComponent implements OnInit {

  @ViewChild('templateConfirmacaoSubstituicao', null) templateConfirmacaoSubstituicao: TemplateRef<any>;

  public isMembros: boolean;
  public isTitular: boolean;
  public isSuplente: boolean;
  public submittedFormSubstituirMembro: boolean;
  
  public limiteMaximo: number;
  public idChapaEleicao: number;

  public membroChapaSelecionado: any;
  public titularResponsavel: any;
  public suplenteResponsavel: any;

  public modalRef: BsModalRef;
  public modalPendeciasMembro: BsModalRef;
  
  public nomeMembroChapa: string;
  public nomeDocumentoJustificativa: string;

  public responsaveis: any = [];

  public novoMembro: any = {};
  public confirmacao: any = {};
  public chapaEleicao: any = {};
  public membrosAtuais: any = {};
  public substitutoTitular: any = {};
  public substitutoSuplente: any = {};
  public configuracaoCkeditor: any = {};
  public documentoJustificativa: any = {};

  /**
   * Construtor da classe.
   */
  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private modalService: BsModalService,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private substituicaoChapaService: SubstiuicaoChapaClientService) {
  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
    this.chapaEleicao = this.route.snapshot.data['chapaEleicao']; 
    this.isMembros = false;
    this.isTitular = false;
    this.isSuplente = false;
    this.submittedFormSubstituirMembro = false;
    this.inicializaIconeTitulo();
    this.inicializaConfiguracaoCkeditor();
  }

  /**
   * Válida se é "Conselheiro UF-BR".
   */
  public isConselheiroUfBR(): boolean {
    return this.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_UF_BR;
  }

  /**
   * Válida se o membro é o titular
   */
  public isMembroTitular(membro: any): boolean {
    return membro.tipoParticipacaoChapa.id == Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR;
  }

  /**
   * Válida se a substituição já foi confirmada.
   */
  public isConfirmado(): boolean {
    return this.confirmacao.numeroProtocolo == undefined;
  }

  /**
   * Retorna placeholder utilizado no autocomplete de profissional.
   * 
   * @param membro 
   */
  public getPlaceholderAutoCompleteProfissional(): string {
   return this.messageService.getDescription('LABEL_INSIRA_CPF_NOME_MEMBRO_SUBSTITUIDO');
  }

  /**
   * Retorna placeholder utilizado no autocomplete do Titular.
   * 
   * @param membro 
   */
  public getPlaceholderAutoCompleteTitular(): string {
    return this.messageService.getDescription('LABEL_INSIRA_CPF_NOME_MEMBRO');
   }

  /**
   * Retorna placeholder utilizado no autocomplete do Suplente.
   */
  public getPlaceholderAutoCompleteSuplente(): string {
    return this.messageService.getDescription('LABEL_INSIRA_CPF_NOME_SUPLENTE');
   }

  /**
   * Incluir membro chapa selecionado.
   *
   * @param event
   */
  public adicionarMembroChapa(membro): void {
    let idProfissional = membro.profissional.id;
    
    this.substituicaoChapaService.getMembroSubstituicao(idProfissional).subscribe(
      data => {
        this.membrosAtuais = [data.titular, data.suplente];
        this.responsaveis[0] = data.titular !== undefined ? data.titular.situacaoResponsavel : false;
        this.responsaveis[1] = data.suplente !== undefined ? data.suplente.situacaoResponsavel : false;

        if (data) {
          this.isMembros = true;
        }
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }
  
  /**
   * Responsavel por trazer as informações do Substituto do autocomplete.
   * 
   * @param membro
   */
  public buscarMembroSubstituto(membro, posicao): any {
    let substituto;
    let idChapa = this.chapaEleicao.id;
    let idProfissional = membro.profissional.id;

    substituto = this.montarMembroSubstituto(idProfissional, posicao);

    if (this.membrosAtuais[posicao] && substituto.idProfissional == this.membrosAtuais[posicao].idProfissional) {
      this.preencherSubistituto(posicao, this.membrosAtuais[posicao]);
    } else {
      this.substituicaoChapaService.buscarMembroSubstituto(idChapa, substituto).subscribe(
        data => {
          this.preencherSubistituto(posicao, data);
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    }
  }

  /**
   * Responsavel por preencher o substituto titular ou suplente.
   * 
   * @param posicao
   * @param substituto 
   */
  public preencherSubistituto(posicao, substituto){
    if (posicao == Constants.POSICAO_TITULAR) {
      this.substitutoTitular = substituto;
      this.isTitular = true;

      if (this.membrosAtuais[0].id != undefined){
        this.titularResponsavel = this.membrosAtuais[0].situacaoResponsavel;
      }
    } else {
      this.substitutoSuplente = substituto;
      this.isSuplente = true;

      if (this.membrosAtuais[0].id != undefined){
        this.suplenteResponsavel = this.membrosAtuais[1].situacaoResponsavel;
      }
    }
  }

  /**
   * Responsavel por montar o objeto do substituto.
   * 
   * @param idProfissional 
   */
  public montarMembroSubstituto(idProfissional, posicao): any {
    let substituto;

    this.membrosAtuais.forEach((element, key) => {
      if(element) {
        substituto = {
          idProfissional: idProfissional,
          idTipoMembro: element.tipoMembroChapa.id,
          idTipoParticipacaoChapa: posicao == 0 ? Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR : Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE,
          numeroOrdem: element.numeroOrdem
        }
        
        this.idChapaEleicao = element.chapaEleicao.id;
      }
    });
    
    return substituto;
  }

  /**
   * Retorna para a tela anterior ou a tela inicial do sistema.
   */
  public voltar(): void {
    if (this.isMembros) {

      this.messageService.addConfirmYesNo('MSG_CONFIRMA_VOLTAR',
      () => {
        this.novoMembro = {};
        this.isMembros = false;
        this.isTitular = false;
        this.isSuplente = false;
        this.nomeMembroChapa = '';
        this.substitutoTitular = {};
        this.substitutoSuplente = {};
        this.documentoJustificativa = {};
        this.documentoJustificativa = {};
        this.nomeDocumentoJustificativa = '';
      });

    } else {
      this.router.navigate(['/']);
    }
  }

  /**
   * sai da tela para a tela inicial do sistema.
   */
  public sair(): void {
    this.router.navigate(['/']);
  }

  /**
   * Verifica o status de Validação do Membro.
   * 
   * @param membro 
   */
  public statusValidacao(membro): boolean {
    return membro.statusValidacaoMembroChapa.id == Constants.STATUS_SEM_PENDENCIA;
  }

  /**
   * Verifica o status de Participação do Membro.
   * 
   * @param membro 
   */
  public statusParticipacao(membro): boolean {
    return membro.statusParticipacaoChapa.id == Constants.STATUS_CONFIRMADO;
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor(): void {
    this.configuracaoCkeditor = {
      toolbar: [
        { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'] },
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
      ],
      title: 'Justificativa'
    };
  }

  /**
   * Inicializa ícone e título do header da página .
   */
  private inicializaIconeTitulo(): void {
    this.layoutsService.onLoadTitle.emit({
        icon: 'fa fa-user',
        description: this.messageService.getDescription('LABEL_PEDIDO_SUBSTITUICAO')
    });
  }

  /**
   * Adiciona função callback que valida tamanho do texto da Justificativa.
   * 
   * @param event 
   */
  public onRedyCKJustificativa(event: CKEditor4.EventInfo){
    event.editor.on('key', function(event2) {
        let maxl = Constants.TAMANHO_MAXIMO_JUSTIFICATIVA_SUBSTITUICAO;
        let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim();
        
        if( !StringService.isLimitValid(simplesTexto, maxl) && StringService.isTextualCaracter(event2.data.keyCode)) {
            event2.cancel();
        }
    });

    event.editor.on('paste', function(event2) {
        let maxl = Constants.TAMANHO_MAXIMO_JUSTIFICATIVA_SUBSTITUICAO;
        let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim() + event2.data.dataValue;
        if(!StringService.isLimitValid(simplesTexto, maxl)) {
            event2.cancel();
        }                     
    });
  }

  /**
   * Responsavel por atualizar o limite maximo da Justificativa.
   * 
   * @param event 
   */
  public atualizaLimiteMaximoJustificativa(event: CKEditor4.EventInfo): any{
    let texto = StringService.getPlainText(event.editor.getData()).slice(0, -1);
    this.limiteMaximo = Constants.TAMANHO_MAXIMO_JUSTIFICATIVA_SUBSTITUICAO - texto.length;
  }

  /**
   * Realiza Upload de documento de declaração.
   *
   * @param arquivoEvent
   */
  public uploadDocumentoJustificativa(arquivoEvent): void {
    let arquivoTO = { "nome": arquivoEvent.name, "tamanho": arquivoEvent.size, "tipoValidacao": Constants.ARQUIVO_TAMANHO_10_MEGA };
    this.nomeDocumentoJustificativa = arquivoTO.nome;
    this.substituicaoChapaService.validarArquivoJustificativaSubstituicao(arquivoTO).subscribe(
      data => {
        this.documentoJustificativa = arquivoTO;
        this.documentoJustificativa.arquivo = arquivoEvent;
      },
      error => {
        this.nomeDocumentoJustificativa = '';
        this.messageService.addMsgDanger(error);
      }
    );
  }

  public onClickUpload( ): void{
    if(this.nomeDocumentoJustificativa && this.nomeDocumentoJustificativa.length > 0) {
      this.messageService.addMsgWarning('MSG_PERMISSAO_UPLOAD_UM_ARQUIVO');    
    }
  }

  /**
   * verifica se o campo esta com checked.
   */
  public hasCheck(key): boolean {
    return this.responsaveis[key];
  }

  /**
   * Verifica se os membros são do tipo Conselheiro Federal ou Representante IES.
   */
  public isConselheiroFederalRepresentanteIes(): boolean {
    let membros = this.membrosAtuais;
    let result;

    membros.forEach(element => {
      result = (element && element.numeroOrdem == 0) ? false : true;
    });

    return result;
  }

  /**
   * Salva os membros de chapa.
   * 
   * @param form
   */
  public salvar(form: NgForm): any {
    this.submittedFormSubstituirMembro = true;

    if (form.valid) {
      
        let dados = this.montarSubstituicao();

        this.substituicaoChapaService.salvarSubstituicao(dados).subscribe(
          data => {
            this.confirmacao = data;
            this.abrirModalConfirmacao(this.templateConfirmacaoSubstituicao);
          },
          error => {
            this.messageService.addMsgDanger(error);
          }
        );
      
    }
  }

  /**
   * Responsavel por montar o objeto da substituição.
   */
  public montarSubstituicao(): any {

    this.substitutoTitular.situacaoResponsavel = this.substitutoTitular.situacaoResponsavel != undefined ? this.substitutoTitular.situacaoResponsavel : false;
    this.substitutoSuplente.situacaoResponsavel = this.substitutoSuplente.situacaoResponsavel != undefined ? this.substitutoSuplente.situacaoResponsavel : false;

    let substituicao = {
      idChapaEleicao: this.chapaEleicao.id,
      justificativa: this.novoMembro.justificativa,
      nomeArquivo: this.documentoJustificativa.nome,
      arquivo: this.documentoJustificativa.arquivo,
      tamanho: this.documentoJustificativa.tamanho,
      membroSubstitutoTitular: this.substitutoTitular,
      membroSubstitutoSuplente: this.substitutoSuplente
    }

    return substituicao
  }

  /**
   * Salva os membros de chapa.
   */
  public cancelar(): any {
    this.isMembros = false;
    this.isTitular = false;
    this.isSuplente = false;

    this.novoMembro = {};
    this.nomeMembroChapa = '';
    this.substitutoTitular = {}
    this.substitutoSuplente = {}
    this.documentoJustificativa = {}
    this.documentoJustificativa = {};
    this.nomeDocumentoJustificativa = '';
  }

  /**
   * Responsavel por exluir o Upload.
   */
  public excluiUpload() {
    this.messageService.addConfirmYesNo('MSG_CONFIRMA_EXCLUSAO_ARQUIVO',
    () => {
      this.documentoJustificativa = {};
      this.nomeDocumentoJustificativa = '';
      this.messageService.addMsgSuccess('MSG_EXCLUSAO_COM_SUCESSO');
      }, () => {}
    );
  }

  /**
   * Recupera o arquivo upload
   *
   * @param event
   * @param resolucao
   */
  public download(event: EventEmitter<any>): void {
    event.emit(this.documentoJustificativa.arquivo);
    
  }

  /**
   * Exibe modal de confirmação da substituição.
   * 
   * @param template 
   */
  public abrirModalConfirmacao(template: TemplateRef<any>){
    this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-lg' }))
  }

  /**
   * Exibe modal de listagem de pendencias do profissional selecionado.
   * 
   * @param template 
   * @param element 
   */
  public abrirModalPendeciasMembro(template: TemplateRef<any>, element: any): void {
    this.membroChapaSelecionado = element;
    this.modalPendeciasMembro = this.modalService.show(template, Object.assign({}, { class: 'my-modal' }));
  }

  /**
   * Redireciona o usuário para a tela de visualizar pedido de substituicao
   */
  public redirecionaVisualizarPedido() {
    this.modalRef.hide();
    this.router.navigate([`/eleicao/substituicao/responsavel-chapa-detalhamento/${this.confirmacao.id}`]);
  }
}
