import { NgForm } from '@angular/forms';
import { LayoutsService } from '@cau/layout';
import { CKEditor4 } from 'ckeditor4-angular';
import { MessageService } from '@cau/message';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, TemplateRef, ViewChild, EventEmitter } from '@angular/core';

import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';

import { ImpugnacaoCandidaturaClientService } from 'src/app/client/impugnacao-candidatura-client/impugnacao-candidatura-client.service';


@Component({
  selector: 'app-cadastro-impugnacao',
  templateUrl: './cadastro-impugnacao.component.html',
  styleUrls: ['./cadastro-impugnacao.component.scss']
})
export class CadastroImpugnacaoComponent implements OnInit {

  @ViewChild('templateConfirmacao', null) templateConfirmacao: TemplateRef<any>;

  public isProfissional: boolean;
  public submitted: boolean;

  public idAtividade: any;
  public justificativa: any;
  public membroChapaSelecionado: any;

  public modalRef: BsModalRef;

  public impugnado: any = {};
  public atividades: any = [];
  public confirmacao: any = {};
  public configuracaoCkeditor: any = {};

  public dadosFormulario = {
    descricao: "",
    membroChapa: {},
    arquivosPedidoImpugnacao: [],
    respostasDeclaracao: [],
  };

  /**
   * Construtor da classe.
   */
  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private modalService: BsModalService,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private ImpugnacaoService: ImpugnacaoCandidaturaClientService) {

      this.idAtividade = route.snapshot.data['atividades'].id;
  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
    this.isProfissional = false;
    this.submitted = false;
    this.inicializaIconeTitulo();
    this.inicializaConfiguracaoCkeditor();
    this.IniciaDeclaracaoAtividade(this.idAtividade);
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
   * Resoponsavel por salvar os arquivos que foram submetidos no componete arquivo.
   *
   * @param arquivos
   */
  public salvarArquivos(arquivos): void {
    this.dadosFormulario.arquivosPedidoImpugnacao = arquivos;
  }

  /**
   * Resoponsavel por adicionar a descricao que fora submetido no compomente editor de texto.
   *
   * @param descricao
   */
  public adicionarDescricao(descricao): void {
    this.dadosFormulario.descricao = descricao;
  }

  /**
   * Incluir membro chapa selecionado.
   *
   * @param event
   */
  public adicionarProfissional(profissional): void {
    let idProfissional = profissional.profissional.id;
    
    this.ImpugnacaoService.getProfissionalImpugnado(idProfissional).subscribe(
      data => {
        this.impugnado = data;
        if (data) {
          this.isProfissional = true;
          this.dadosFormulario.membroChapa = this.impugnado;
        }
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }

  /**
   * Realiza a busca da declaração a partir do 'id' da atividade secundaria.
   * 
   * @param atividade 
   */
  public IniciaDeclaracaoAtividade(idAtividade): void {
    this.ImpugnacaoService.getDeclaracoesAtividade(idAtividade).subscribe(
      data => {
        this.atividades = data;
        this.inicializaRespostas();
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }
    
  /**
   * Retorna para a tela anterior ou a tela inicial do sistema.
   */
  public voltar(): void {
    if (this.isProfissional) {

      this.messageService.addConfirmYesNo('MSG_CONFIRMA_VOLTAR',
      () => {
        this.isProfissional = false;
        this.submitted = false;
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
        description: this.messageService.getDescription('LABEL_PEDIDO_IMPUGNACAO')
    });
  }

  /**
   * Inicializa as respostas do formulário.
   */
  public inicializaRespostas() {
    this.atividades.forEach((atividade, index) => {
      let respostaVazia = { 'idDeclaracao': atividade.declaracao.id, 'itensRespostaDeclaracao': [] };
      atividade.declaracao.itensDeclaracao.forEach(item => {
        let itemResposta = { 'idItemDeclaracao': item.id, 'situacaoResposta': false };
        respostaVazia.itensRespostaDeclaracao.push(itemResposta);
      });
      this.dadosFormulario.respostasDeclaracao.push(respostaVazia);
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
   * Salva os membros de chapa.
   * 
   */
  public salvar(): any {
    this.submitted = true;
    if (this.hasArquivos() && this.hasDescricao() && this.hasItensRespondidos()) {
      this.ImpugnacaoService.salvar(this.dadosFormulario).subscribe(
        data => {
          this.confirmacao = data;
          this.abrirModalConfirmacao(this.templateConfirmacao);
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    } else {
      if (!this.hasItensRespondidos()) {
        this.messageService.addMsgDanger(this.messageService.getDescription('MSG_OBRIGATORIEDADE_ITEM_DECLARACAO'));
      } else if (!this.hasArquivos()) {
        this.messageService.addMsgDanger(this.messageService.getDescription('MSG_OBRIGATORIEDADE_DOCUMENTO_COMPROBATORIO'));
      }
    }
  }

  /**
   * Verifica se todos os itens da declaração foram respondidos de acordo com cada critério de formulário.
   */
  public hasItensRespondidos() {
    let isValido = this.atividades.length == 0 ? true : false;

    this.dadosFormulario.respostasDeclaracao.forEach(resposta => {

      resposta.itensRespostaDeclaracao.forEach(itemResposta => {

        if (itemResposta.situacaoResposta) {
          isValido = true;
        }
      });

    });

    return isValido;
  }

  /**
   * Verifica se a discricao foi preencida.
   */
  public hasDescricao(): any {
    let isValido = false;
    
    if (this.dadosFormulario.descricao){
      isValido = true;
    }

    return isValido;
  }


  /**
   * Verifica se existe ao menos um arquivo submetido.
   */
  public hasArquivos(): any {
    let isValido = false;

    if (this.dadosFormulario.arquivosPedidoImpugnacao[0]){
      isValido = true;
    }

    return isValido;
  }

  /**
   * Adiciona os itens marcados no formulário na variável dadosFormulario.
   *
   * @param item
   * @param declaracao
   * @param index
   * @param indexAtividade
   */
  public alterarItemDeclaracao(item: any, declaracao: any) {

    this.dadosFormulario.respostasDeclaracao.forEach(resposta => {

      if (resposta.idDeclaracao == declaracao.id) {

        resposta.itensRespostaDeclaracao.forEach(itemResposta => {

          if (itemResposta.idItemDeclaracao == item.id) {
            itemResposta.situacaoResposta = !itemResposta.situacaoResposta;
          } else if (declaracao.tipoResposta == Constants.TIPO_RESPOSTA_UNICA) {
            itemResposta.situacaoResposta = false;
          }
        });
      }
    });
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
   * Retorna a opsição formatada
   *
   * @param numeroOrdem
   */
  public getPosicaoFormatada(numeroOrdem) {
    return numeroOrdem > 0 ? numeroOrdem: '-';
  }

  /**
   * Redireciona o usuário para a tela de visualizar pedido de substituicao
   */
  public redirecionaVisualizarPedido() {
    this.modalRef.hide();
    this.router.navigate([`/eleicao/impugnacao/${this.confirmacao.id}/detalhar-solicintante/`]);
  }

  /**
     * Retorna o número da chapa eleição.
     *
     * @param chapaEleicao
     */
    public getNumeroChapa(chapaEleicao: any): string {
      return chapaEleicao.numeroChapa != undefined && chapaEleicao.numeroChapa ? chapaEleicao.numeroChapa : this.messageService.getDescription('LABEL_NAO_APLICADO');
  }

  /**
     * Retorna o descrição da ud da chapa eleição.
     *
     * @param chapaEleicao
     */
    public getDescricaoUf(chapaEleicao: any): string {
      return chapaEleicao.tipoCandidatura.id != Constants.TIPO_CONSELHEIRO_IES ? chapaEleicao.cauUf.prefixo : Constants.IES;
  }


    /**
   * Responsavel por fazer o download do arquivo.
   */
  public downloadArquivo(params: any): void {
    if(params.arquivo.id) {
      this.ImpugnacaoService.downloadArquivoImpugnacao(params.arquivo.id).subscribe(
        data => {
          params.evento.emit(data);
      }, error => {
          this.messageService.addMsgDanger(error);
      });
    } else {
      params.evento.emit(params.arquivo);
    }
  }

  /**
 * Retorna o registro com a mascara 
 * @param str 
 */
  public getRegistroComMask(str) {
    return StringService.maskRegistroProfissional(str);
  }

}
