import { Component, OnInit, EventEmitter } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { CalendarioClientService } from 'src/app/client/calendario-client/calendario-client.service';
import { PublicacaoComissaoMembroClientService } from 'src/app/client/publicacao-comissao-membro-client/publicacao-comissao-membro-client.service';
import { MessageService } from '@cau/message';
import { DomSanitizer} from '@angular/platform-browser'

@Component({
  selector: 'app-visualizar-comissao-eleitoral',
  templateUrl: './visualizar-comissao-eleitoral.component.html',
  styleUrls: ['./visualizar-comissao-eleitoral.component.scss']
})
export class VisualizarComissaoEleitoralComponent implements OnInit {

  public calendario: any;
  public publicacoes: any = [];
  public nomeArquivoPdf: string;
  public nomeArquivoDoc: string;
  public numeroMembrosComissao: any;
  public documentoComissaoMembro: any;
  public numeroRegistrosPaginacao: number = 5;

  /**
   * Construtor da classe.
   *
   * @param router
   * @param route
   * @param messageService
   * @param calendarioService
   * @param publicacaoComissaoMembroService
   */
  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private calendarioService: CalendarioClientService,
    private publicacaoComissaoMembroService: PublicacaoComissaoMembroClientService,
    private sanitizer: DomSanitizer
  ) {
    this.documentoComissaoMembro = this.route.snapshot.data['documentoComissaoMembro'];

    this.publicacoes = this.documentoComissaoMembro.publicacoes ? this.documentoComissaoMembro.publicacoes : [];
    this.calendario = this.documentoComissaoMembro.informacaoComissaoMembro.atividadeSecundaria.atividadePrincipal.calendario;
    this.nomeArquivoPdf = this.calendario.eleicao.ano + '_' + String(this.calendario.eleicao.sequenciaAno).padStart(2, '0') + '_ComissaoEleitoral.pdf';
    this.nomeArquivoDoc = this.calendario.eleicao.ano + '_' + String(this.calendario.eleicao.sequenciaAno).padStart(2, '0') + '_ComissaoEleitoral.docx';
  }

  /**
   * Método executado quando inicializar o componente.
   */
  ngOnInit() {
    this.inicializarTabelaNumeroMembrosComissao();
  }

  /**
   * Realiza a publicação do documento de comissão membro.
   */
  public publicar(): void {
    let publicacao = {
      id: '',
      documentoComissaoMembro: {
        id: this.documentoComissaoMembro.id
      }
    };

    this.messageService.addConfirmYesNo('MSG_PUBLICAR_DOCUMENTO', () => {
      this.publicacaoComissaoMembroService.salvar(publicacao).subscribe(data => {
        this.publicacoes.push(data);
        this.router.navigate(['publicacao', 'comissao-eleitoral']);
        this.messageService.addMsgSuccess('MSG_DOCUMENTO_PUBLICADO');
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    });
  }

  /**
   * Recupera a quantidade total de registros de publicação.
   */
  public getTotalRegistrosPublicacoes(): number {
    return this.publicacoes.length;
  }

  /**
   * Retorna HTML Conviavel para exibição em tela.
   * Leia mais em: https://netbasal.com/angular-2-security-the-domsanitizer-service-2202c83bd90
   *
   */
  public getHtmlConfiavel(html: string): any {
    return this.sanitizer.bypassSecurityTrustHtml(html);
  }

  /**
   * Retorna para a listagem de publicações de comissão eleitoral.
   */
  public voltarLista(): void {
    this.router.navigate(['publicacao', 'comissao-eleitoral']);
  }

  /**
   * Retorna as ufs que serão exibidas na primeira tabela.
   *
   * @param cauUfs
   */
  public getUfsTabela1(cauUfs: any): any {
    cauUfs = cauUfs === undefined ? [] : cauUfs;
    let ufs = JSON.parse(JSON.stringify(cauUfs));
    let limiteInferior = 0;
    let limiteSuperior = Math.ceil(cauUfs.length / 4);

    limiteSuperior = limiteSuperior == 0 ? 1 : limiteSuperior;

    let ufsTabela1 = ufs.filter((uf, index) => {
      return index >= limiteInferior && index < limiteSuperior;
    });

    return ufsTabela1;
  }

  /**
   * Retorna as ufs que serão exibidas na segunda tabela.
   *
   * @param cauUfs
   */
  public getUfsTabela2(cauUfs: any): any {
    cauUfs = cauUfs === undefined ? [] : cauUfs;
    let ufs = JSON.parse(JSON.stringify(cauUfs));
    let limiteInferior = Math.ceil(cauUfs.length / 4);
    let limiteSuperior = Math.ceil(cauUfs.length / 4) * 2;

    limiteInferior = limiteInferior == 0 ? 1 : limiteInferior;
    limiteSuperior = limiteSuperior == 0 ? 2 : limiteSuperior;

    let ufsTabela2 = ufs.filter((uf, index) => {
      return index >= limiteInferior && index < limiteSuperior;
    });

    return ufsTabela2;
  }

  /**
   * Retorna as ufs que serão exibidas na terceira tabela.
   *
   * @param cauUfs
   */
  public getUfsTabela3(cauUfs: any): any {
    cauUfs = cauUfs === undefined ? [] : cauUfs;
    let ufs = JSON.parse(JSON.stringify(cauUfs));
    let limiteInferior = Math.ceil(cauUfs.length / 4) * 2;
    let limiteSuperior = Math.ceil(cauUfs.length / 4) * 3;

    limiteInferior = limiteInferior == 0 ? 2 : limiteInferior;
    limiteSuperior = limiteSuperior == 0 ? 3 : limiteSuperior;

    let ufsTabela3 = ufs.filter((uf, index) => {
      return index >= limiteInferior && index < limiteSuperior;
    });

    return ufsTabela3;
  }

  /**
   * Retorna as ufs que serão exibidas na quarta tabela.
   *
   * @param cauUfs
   */
  public getUfsTabela4(cauUfs: any): any {
    cauUfs = cauUfs === undefined ? [] : cauUfs;

    let ufs = JSON.parse(JSON.stringify(cauUfs));
    let limiteInferior = Math.ceil(cauUfs.length / 4) * 3;
    let limiteSuperior = Math.ceil(cauUfs.length / 4) * 4;

    limiteInferior = limiteInferior == 0 ? 3 : limiteInferior;
    limiteSuperior = limiteSuperior == 0 ? 4 : limiteSuperior;

    let ufsTabela4 = ufs.filter((uf, index) => {
      return index >= limiteInferior && index < limiteSuperior;
    });

    return ufsTabela4;
  }

  /**
   * Gera o pdf de acordo com o modelo de comissão eleitoral.
   *
   * @param event
   * @param comissaoMembro
   */
  public gerarPDF(event: EventEmitter<any>, comissaoMembro: any): void {
    this.publicacaoComissaoMembroService.gerarPDF(comissaoMembro.id).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Gera o "docx" de acordo com o modelo de comissão eleitoral.
   *
   * @param event
   * @param comissaoMembro
   */
  public gerarDocumento(event: EventEmitter<any>, comissaoMembro: any): void {
    this.publicacaoComissaoMembroService.gerarDocumento(comissaoMembro.id).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Realiza o download do pdf de publicação de comissão membro.
   *
   * @param event
   * @param idPublicacaoDocumento
   */
  public downloadPdf(event: EventEmitter<any>, idPublicacaoDocumento: number): void {
    this.publicacaoComissaoMembroService.downloadPdf(idPublicacaoDocumento).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Recupera o nome do arquivo.
   *
   * @param nomeArquivo
   */
  public getNomeArquivoFormatado(nomeArquivo): string {
    return nomeArquivo + '.pdf';
  }

  /**
   * Inicializa a tabela com a quantidade de membros da comissão.
   */
  private inicializarTabelaNumeroMembrosComissao(): void {
    this.calendarioService.getAgrupamentoNumeroMembrosComissao(this.calendario.id).subscribe(numeroMembros => {
      let numeroMembrosArr = [];

      numeroMembros.forEach(numeroMembro => {
        if (numeroMembro.idCauUf == Constants.INFORMACAO_COMISSAO_CAU_BR_ID) {
          numeroMembrosArr.push(numeroMembro);
        }
      });

      numeroMembros.forEach(numeroMembro => {
        if (numeroMembro.idCauUf != Constants.INFORMACAO_COMISSAO_CAU_BR_ID) {
          numeroMembrosArr.push(numeroMembro);
        }
      });

      this.numeroMembrosComissao = numeroMembrosArr;
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

}
