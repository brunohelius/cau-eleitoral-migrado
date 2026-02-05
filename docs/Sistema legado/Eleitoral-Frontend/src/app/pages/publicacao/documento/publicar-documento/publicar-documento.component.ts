import * as _ from "lodash";
import { MessageService } from '@cau/message';
import { AcaoSistema } from 'src/app/app.acao';
import { SecurityService } from '@cau/security';
import { ActivatedRoute } from '@angular/router';
import { DatePipe, Location } from '@angular/common'
import { Component, OnInit, EventEmitter } from '@angular/core';

import { Constants } from 'src/app/constants.service';
import { CalendarioClientService } from 'src/app/client/calendario-client/calendario-client.service';
import { DocumentoEleicaoClientService } from 'src/app/client/documento-eleicao/documento-eleicao-client.service';

/**
 * Componente referente à Publicação de Documentos.
 *
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'app-publicar-documento',
  templateUrl: './publicar-documento.component.html',
  styleUrls: ['./publicar-documento.component.scss']
})
export class PublicarDocumentoComponent implements OnInit {

  public usuario: any;
  public search: string;
  public calendario: any;
  public documentoEleicao: any;
  public acaoSistema: AcaoSistema;
  public submitted: boolean = false;
  public showMessageFilter: boolean;
  public documentosPublicados: any[];
  public documentosPublicadosExibicao: any[];
  public numeroRegistrosPaginacao: number = 10;

  /**
   * Construtor da Classe.
   * 
   * @param location
   * @param route
   * @param messageService
   * @param securityService
   * @param documentoEleicaoService
   */
  constructor(
    private location: Location,
    private datePipe: DatePipe,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private securityService: SecurityService,
    private calendarioService: CalendarioClientService,
    private documentoEleicaoService: DocumentoEleicaoClientService,
  ) {
    this.acaoSistema = new AcaoSistema(route);
  }

  /**
   * Metodo inicial do componente 
   */
  ngOnInit() {
    this.usuario = this.securityService.credential['_user'];
    this.calendario = this.route.snapshot.data['calendario'];
    this.documentosPublicados = this.route.snapshot.data['documentosPublicados'];
    this.documentosPublicadosExibicao = JSON.parse(JSON.stringify(this.documentosPublicados));

    this.initDocumentoEleicao();

    
  }

  /**
   * Atribui as informações do arquivo ao documento da eleição.
   * 
   * @param arquivo
   */
  public selecionarArquivo(arquivo: any): void {
    let arquivoTO = {
      nomeArquivo: arquivo.name,
      tamanhoArquivo: arquivo.size
    }
    this.documentoEleicaoService.validarArquivoCalendario(arquivoTO).subscribe(data => {
      this.documentoEleicao.arquivo = arquivo;
      this.documentoEleicao.tamanho = arquivo.size;
      this.documentoEleicao.nomeArquivo = arquivo.name;
    },
    error => {
      this.messageService.addMsgWarning(error.message);
    });
  }




  /**
   * Realiza a publicação do documento da eleição.
   */
  public publicarDocumento(): void {
    this.submitted = true;
    if (this.isLocaisPublicacaoPreenchido() && this.documentoEleicao.arquivo) {
      this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_PUBLICAR_ARQUIVO', () => {

        this.documentoEleicaoService.salvar(this.documentoEleicao).subscribe(() => {
          this.submitted = false;
          this.initDocumentoEleicao();
          this.atualizarDocumentosPublicados();
          this.messageService.addMsgSuccess('MSG_ARQUIVO_PUBLICADO_SUCESSO');
        }, () => {
          this.submitted = false;
          this.messageService.addMsgDanger('MSG_ERRO_PUBLICACAO_DOCUMENTO_ELEICAO');
        });
      }, () => this.submitted = false);
    }
  }

  /**
   * Filtra os dados da grid conforme o valor informado na variável search.
   *
   * @param search
   */
  public filtroGrid(search: string): void {
    search = search.toLowerCase();
    
    this.documentosPublicadosExibicao = this.documentosPublicados.filter((documento: any) => {
      let locaisPublicacao = this.getLocaisPublicacaoPorDocumento(documento);
      let dataPublicacao = this.datePipe.transform(documento.dataPublicacao, 'dd/MM/yyyy HH:mm', Constants.TIMEZONE);
      return (
        documento.sequencial.toString().includes(search) ||
        this.calendario.eleicao.descricao.toLowerCase().includes(search) ||
        this.calendario.eleicao.tipoProcesso.descricao.toLowerCase().includes(search) ||
        documento.nomeUsuario.toLowerCase().includes(search) ||
        dataPublicacao.includes(search) ||
        locaisPublicacao.toLowerCase().includes(search)
      );
    });

    if(this.documentosPublicadosExibicao.length < 1) {
      this.showMessageFilter = true;
    } else {
      this.showMessageFilter = false;
    }
  }

  /**
   * Recupera o arquivo referente ao documento publicado, conforme o ID informado.
   *
   * @param event
   * @param idDocumentoEleicao
   */
  public downloadDocumento(event: EventEmitter<any>, idDocumentoEleicao: any): void {
    this.documentoEleicaoService.download(idDocumentoEleicao).subscribe((data: Blob) => {
      event.emit(data);

    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Recupera o arquivo conforme a id da entidade 'resolucao' informada.
   *
   * @param event
   * @param idResolucao
   */
  public downloadResolucao(event: EventEmitter<any>, idResolucao: any): void {
    this.calendarioService.downloadArquivo(idResolucao).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Verifica se a extensão do arquivo refere-se à extensão DOC/DOCX.
   * 
   * @param nomeArquivo
   */
  public isArquivoExtensaoWord(nomeArquivo: string): boolean {
    const partes = nomeArquivo.split('.');
    const extensao = partes[partes.length - 1];
    return (
      extensao.toLowerCase() === Constants.EXTENSAO_DOC ||
      extensao.toLowerCase() === Constants.EXTENSAO_DOCX
    );
  }

  /**
   * Retorna para a tela anterior.
   */
  public voltar(): void {
    if (this.documentoEleicao.arquivo === undefined) {
      this.location.back();
    } else {
      this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_VOLTAR_DADOS_NAO_PUBLICADOS_SERAO_PERDIDOS', () => {
        this.location.back();
      });
    }
  }

  /**
   * Verifica se os locais de publicação foram definidas.
   */
  public isLocaisPublicacaoPreenchido(): boolean {
    return (
      this.documentoEleicao.publico ||
      this.documentoEleicao.corporativo ||
      this.documentoEleicao.profissional
    );
  }

  /**
   * Inicializa o 'Documento' da eleição.
   */
  private initDocumentoEleicao(): void {
    this.documentoEleicao = {};
    this.documentoEleicao.publico = true;
    this.documentoEleicao.corporativo = false;
    this.documentoEleicao.arquivo = undefined;
    this.documentoEleicao.tamanho = undefined;
    this.documentoEleicao.profissional = false;
    this.documentoEleicao.nomeArquivo = undefined;

    this.documentoEleicao.idUsuario = this.usuario.id;
    this.documentoEleicao.nomeUsuario = this.usuario.name;
    this.documentoEleicao.eleicao = this.calendario.eleicao;
  }

  /**
   * Atualiza a lista de documentos publicados.
   */
  private atualizarDocumentosPublicados(): void {
    this.documentoEleicaoService.getDocumentosPorEleicao(this.calendario.id).subscribe(data => {
      this.documentosPublicados = data;
      this.documentosPublicadosExibicao = JSON.parse(JSON.stringify(data));
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Retorna os locais de publicação do documento, em formato de 'string'.
   * 
   * @param documento
   */
  private getLocaisPublicacaoPorDocumento(documento: any): string {
    let locais = '';
    locais += documento.corporativo ? 'Corporativo;' : '';
    locais += documento.profissional ? 'Profissional;' : '';
    locais += documento.publico ? 'Público' : '';
    return locais;
  }

}
