import { NgForm } from '@angular/forms';
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit} from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from '@angular/router';
import { ConviteMembroComissaoService } from 'src/app/client/convite-membro-comissao-client/convite-membro-comissao-client.service';
import { ComissaoEleitoralService } from 'src/app/client/comissao-eleitoral-client/comissao-eleitoral-client.service';

@Component({
  selector: 'app-convite-membro-comissao',
  templateUrl: './convite-membro-comissao.component.html',
  styleUrls: ['./convite-membro-comissao.component.scss']
})

export class ConviteMembroComissaoComponent implements OnInit {

  public arquivo: any;
  public submitted = false;
  public dadosDeclaracao = [];
  public arquivoDeclaracao: any;

  public dadosFormulario = {
    arquivos: [],
    idDeclaracao: null,
    itensDeclaracao: [],
    idProfissional: null,
    isConviteAceito: null,
    idMembroComissao: null,
    isParticipanteComissao: null
  };

  public usuario;
  public declaracao: any;
  public statusConvite: number;
  public situacaoCalendario = null;
  private idMembroComissao: number;
  public controleProgresso: number;
  public auxControleProgresso: number;
  public situacaoMembroComissao = null;
  public controleProgressoRecusar = false;
  public dados: any;


  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private layoutsService: LayoutsService,
    private messageService: MessageService,
    private securityService: SecurityService,
    private conviteMembroService: ConviteMembroComissaoService,
    private comissaoEleitoralService: ComissaoEleitoralService,
  ) { }

  ngOnInit() {

    /**
    * Define ícone e título do header da página 
    */
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-wpforms',
      description: this.messageService.getDescription('MEMBROS_COMISSAO_ELEITORAL')
    });

    this.usuario = {};
    this.controleProgresso = 1;
    this.usuario = this.securityService.credential["_user"];
    this.dados = this.route.snapshot.data["declaracao"];
    this.verificaCalendarioAtivo();
  }


/**
 * Vierifica a situação do calendário. Caso seja inativo retorna para o status
 * de falta de permissão para acesso ao menu.
 */
  public verificaCalendarioAtivo() {
      this.comissaoEleitoralService.getAceiteComissaoMembro(this.usuario.idProfissional).subscribe(data => {
        this.situacaoCalendario = data.situacaoCalendario;
        this.isCalendarioInativo();
      });
  }
  
  /**
   * Retorna a declaração de membro de comissão pelo id do profissional
   * 
   * @param id 
   */
  public inicializaDeclaracaoMembroComissao(dados: any): void {
        this.declaracao = dados.declaracao;
        this.idMembroComissao = dados.idMembroComissao;
        this.situacaoMembroComissao = dados.situacaoMembroComissao;
        this.isConviteRecusado();
  }

  /**
   * Método responsável por validar se cada arquivo submetido a upload
   * atende os critérios definidos para salvar os binários.
   * 
   * @param arquivoEvent
   * @param calendario
   */
  public uploadDocumento(arquivoEvent: any): void {
    if (this.declaracao) {
      let arquivoUpload = {
        "membroComissao": {},
        "arquivo": arquivoEvent, 
        "nome": arquivoEvent.name, 
        "tamanho": arquivoEvent.size, 
        "nomeFisico": arquivoEvent.name,
        "idDeclaracao": this.declaracao['id']
      };

      if (this.dadosFormulario.arquivos.length < 10) {
          this.conviteMembroService.validarArquivoViaDeclaracao(arquivoUpload).subscribe( data => {
            this.arquivo = arquivoEvent.name;
            this.dadosFormulario.arquivos.push(arquivoUpload);
            this.submitted = false;
            this.arquivo = null;
          }, 
          error => {
            this.messageService.addMsgWarning(error.message);
          });
      } else {
        this.messageService.addMsgWarning('MSG_QTD_MAXIMA_UPLOAD');
      }
    }
  }

    /**
   * Exclui a receita da variável itensReceita passando o indice como parâmetro
   * @param indice 
   */
  public excluiUpload(indice) {
    this.dadosFormulario.arquivos.splice(indice,1);
  }

  /**
   * Adiciona os itens marcados no formulário na variável
   * dadosFormulario
   * @param item 
   */
  public addItemDeclaracao(item: any, tipoResposta: any, index?: any) {

    if(tipoResposta == Constants.TIPO_RESPOSTA_UNICA ) {
      this.dadosFormulario.itensDeclaracao[0] = { 'id': item.id, 'descricao': item.descricao };
    }

    if(tipoResposta == Constants.TIPO_RESPOSTA_MULTIPLA ) {
      let existe = false;
      for(let i = 0; i < this.dadosFormulario.itensDeclaracao.length; i += 1 ) {
        if(this.dadosFormulario.itensDeclaracao[i].id === item.id) {
          this.dadosFormulario.itensDeclaracao.splice(i,1);
          existe = true;
        }
      }

      if(!existe) {
        this.dadosFormulario.itensDeclaracao.push({ 'id': item.id, 'descricao': item.descricao });
      }
    }
  }


  /**
   * Verifica se o campo de upload é obrigatório
   */
  public isUploadValido() {
    
    let isValido = false;
    if((this.declaracao.uploadObrigatorio == 1 || this.declaracao.uploadObrigatorio == 1 ) && this.dadosFormulario.arquivos.length < 1 ) {
      isValido = false;
    } else {
      isValido = true;
    }
    return isValido;
  }

  /**
   * Verifica se todos os itens da declaração foram respondidos de acordo com cada critério de formulário
   */
  public isItensRespondidos() {
    let isValido = false;
    
    if (this.declaracao.tipoResposta == Constants.TIPO_RESPOSTA_MULTIPLA ) {
      if(this.declaracao.itensDeclaracao.length != this.dadosFormulario.itensDeclaracao.length) {
        isValido = false;
      } else {
        isValido = true;
      }
    } else if (this.declaracao.tipoResposta == Constants.TIPO_RESPOSTA_UNICA ) {
      if(this.dadosFormulario.itensDeclaracao.length < 1) {
        isValido = false;
      } else {
        isValido = true;
      }
    }

    return isValido;
  }

  /**
   * Valida os dados e envia o convite
   */
  public salvarRespostaConvite(acaoConvite: boolean) {
    
    this.submitted = true;
    if(this.dadosFormulario.itensDeclaracao.length < 1 && this.controleProgresso === 3) {
      this.messageService.addMsgWarning('MSG_SELECAO_RESPOSTA');
    }

    if(this.declaracao && this.isUploadValido() && this.isItensRespondidos() || !acaoConvite) {
      this.dadosFormulario.isConviteAceito = acaoConvite;
      this.dadosFormulario.isParticipanteComissao = true;
      this.dadosFormulario.idDeclaracao = this.declaracao['id'];
      this.dadosFormulario.idMembroComissao = this.idMembroComissao;
      this.dadosFormulario.idProfissional = this.usuario.idProfissional;

        this.conviteMembroService.aceitarConviteMembroComissao(this.dadosFormulario).subscribe(
          data => {
            if( this.dadosFormulario.isConviteAceito ) {
              this.messageService.addMsgSuccess('MSG_SUCESSO_PARTICIPACAO');
              this.controleProgresso = 4;
            }
            else if(!this.dadosFormulario.isConviteAceito ) {
              this.router.navigate(['/']);
            }
          },
          error => {
            this.messageService.addMsgDanger(error);
        });
    }

  }

  /**
   * Método responsável definir o convite aceito
   */
  public acaoAceitarConvite() {
    let acaoConvite = true;
    this.salvarRespostaConvite(acaoConvite);
  }

  /**
   * Método responsável definir o convite recusado
   */
  public acaoRecusarConvite() {
    let acaoConvite = false;
    this.salvarRespostaConvite(acaoConvite);
  }

  /**
  * Volta os formulários de escolha
  */
  public voltarNivelProgresso() {

    if (this.controleProgresso > 1) {
      if(this.controleProgresso === 3) {
        if(this.dadosFormulario.arquivos.length > 0 || this.dadosFormulario.itensDeclaracao.length > 0) {
          this.messageService.addConfirmYesNo('MSG_CONFIRMA_VOLTAR',
         () => {
              this.resetarFormulario();
            }, () => {
              this.controleProgresso = 3;    
          });
        } else {
          this.controleProgresso = 1;
        }
      } else {
        this.controleProgresso -= 1;
      }
    }
  }

  /**
   * Avança os formulários de escolha
   */
  public avancarNivelProgresso() {
    this.controleProgresso += 1;
  }

  /**
   * Método que mostra na tela o popup de recusar convite
   */
  public recusarConvite() {
    this.controleProgresso = 0;
    this.controleProgressoRecusar = true;
    this.auxControleProgresso = this.controleProgresso;
  }

  /**
   * Tira o formulário de recusar convite da tela e 
   * mostra o formulário no ultimo estado.
   */
  public cancelarRecusarConvite() {
    this.auxControleProgresso = 1;
    this.controleProgressoRecusar = false;
    this.controleProgresso = this.auxControleProgresso;
  }

  /**
   * Caso o Calendário esteja inativo mostra mensagem de falta de permisão para acessar 
   * o menu.
   */
  public isCalendarioInativo() {
    if(this.situacaoCalendario === false ) {
      this.statusConvite = 0
    } else {
      this.inicializaDeclaracaoMembroComissao(this.dados);
    }
  }

  /**
   * Verifica a situação do membro da comissão e executa ações de acordo com o valor
   */
  public isConviteRecusado() {

    if(this.situacaoMembroComissao === Constants.SITUACAO_MEMBRO_PENDENTE) {
      this.statusConvite = 1;
    
    } else if(this.situacaoMembroComissao === Constants.SITUACAO_MEMBRO_CONFIRMADO) {
      this.redirecionaListaMembrosComissao();

    } else if(this.situacaoMembroComissao === Constants.SITUACAO_MEMBRO_REJEITADO){
      this.statusConvite = 3
    } else {
      this.statusConvite = 0
    }

  }

  /**
   * Rdireciona o usuário para a tela de listar membros de comissão
   */
  public redirecionaListaMembrosComissao() {
    this.router.navigate(['/eleicao/comissao-eleitoral']);
  }


  /**
   * Método responsável por limpar todos os dados preenchidos no formulário
   */
  public resetarFormulario() {
    this.dadosFormulario = {
      arquivos: [],
      idDeclaracao: null,
      itensDeclaracao: [],
      idProfissional: null,
      isConviteAceito: null,
      idMembroComissao: null,
      isParticipanteComissao: null
    }
    this.controleProgresso = 1;
    this.submitted = false;
  }

}
