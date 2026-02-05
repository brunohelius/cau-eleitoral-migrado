import { Component, OnInit, Input, EventEmitter } from '@angular/core';
import { BsModalRef } from 'ngx-bootstrap';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { MessageService } from '@cau/message';

@Component({
  selector: 'app-modal-gerar-documento',
  templateUrl: './modal-gerar-documento.component.html',
  styleUrls: ['./modal-gerar-documento.component.scss']
})
export class ModalGerarDocumentoComponent implements OnInit {

  @Input() denuncia: any;
  
  public abas: any = [];
  public todasAbas: boolean = true;
  public submitted: boolean = false;
  public abasGeracaoDocumento: any = [];

  constructor(
    public modalRef: BsModalRef,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.getAbasDisponiveisDenuncia();
  }

  /**
   * Retorna as abas disponíveis para a denúncia.
   */
  public getAbasDisponiveisDenuncia = () => {
    this.denunciaService.getAbasDisponiveisByIdDenuncia(this.denuncia.id_denuncia).subscribe((data) => {
      this.setAbasFormatadas(data);
    }, error => {
        this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Gera o documento com os dados das abas disponíveis para denúncia.
   *
   * @param form
   */
  public gerar = (event: EventEmitter<any>) => {
    this.submitted = true;

    let sendGeracaoDocumento = this.getDadosGerarDocumento();

    if (this.verificaAbasSelecionadas().hasAbaSelecionada) {
      this.denunciaService.gerarDocumento(sendGeracaoDocumento).subscribe(
        (data: Blob) => {
            event.emit(data);
            this.modalRef.hide();
        }, error => {
            this.messageService.addMsgDanger(error);
        }
      );
    }
  }

  /**
   * Verifica o checkbox marcado.
   */
  public checarItem = () => {
    this.todasAbas = false;
    if(this.verificaAbasSelecionadas().isTodasAbas) {
      this.todasAbas = true;
    }
  }

  /**
   * Verifica os checkbox para marcação.
   */
  public checarTodos = () => {
    this.abas.map((aba) => {
      aba.ativo = false;
      
      if (this.todasAbas) {
        aba.ativo = true;
      }
    });
  }

  /**
   * Verifica se existem abas selecionadas.
   */
  public verificaAbasSelecionadas = () => {
    let dadosGeracaoDocumento = this.getDadosGerarDocumento();
    let abasGeracao = Object.values(dadosGeracaoDocumento);
    abasGeracao.shift();

    let abasSelecionadas = abasGeracao.filter(element => element);

    return {
      hasAbaSelecionada: abasSelecionadas.length > 0,
      isTodasAbas: abasGeracao.length == abasSelecionadas.length,
    };
  }

  /**
   * Formata as abas com os dados necessários para apresentação.
   * 
   * @param abas 
   */
  private setAbasFormatadas = (abas) => {
    let abasFormatadas: any = [
      {aba: 'hasAcompanharDenuncia', ativo: true, 
        nome: this.messageService.getDescription('LABEL_ACOMPANHAR_DENUNCIA') + ' (' +this.messageService.getDescription('LABEL_DADOS_CADASTRO') + ')'}
    ];

    if (abas.hasAnaliseAdmissibilidade) {
      abasFormatadas.push({aba: 'hasAnaliseAdmissibilidade', ativo: true, nome: this.messageService.getDescription('LABEL_ANALISE_ADMISSIBILIDADE')});
    }

    if (abas.hasJulgamentoAdmissibilidade) {
      abasFormatadas.push({aba: 'hasJulgamentoAdmissibilidade', ativo: true, nome: this.messageService.getDescription('LABEL_JULGAMENTO_ADMISSIBILIDADE')});
    }

    if (abas.hasRecursoAdmissibilidade) {
      abasFormatadas.push({aba: 'hasRecursoAdmissibilidade', ativo: true, nome: this.messageService.getDescription('LABEL_RECURSO_DE_ADMISSIBILIDADE')});
    }

    if (abas.hasJulgamentoRecursoAdmissibilidade) {
      abasFormatadas.push({aba: 'hasJulgamentoRecursoAdmissibilidade', ativo: true, nome: this.messageService.getDescription('LABEL_JULGAMENTO_RECURSO_ADMISSIBILIDADE')});
    }

    if (abas.hasDefesa) {
      abasFormatadas.push({aba: 'hasDefesa', ativo: true, nome: this.messageService.getDescription('LABEL_DEFESA')});
    }

    if (abas.hasParecer) {
      abasFormatadas.push({aba: 'hasParecer', ativo: true, nome: this.messageService.getDescription('LABEL_PARECER')});
    }

    if (abas.hasJulgamentoPrimeiraInstancia) {
      abasFormatadas.push({aba: 'hasJulgamentoPrimeiraInstancia', ativo: true, nome: this.messageService.getDescription('LABEL_JULGAMENTO_PRIMEIRA_INSTANCIA')});
    }

    if (abas.hasRecursoDenunciante) {
      abasFormatadas.push({aba: 'hasRecursoDenunciante', ativo: true, nome: this.messageService.getDescription('LABEL_ABA_RECURSO_DENUNCIANTE')});
    }

    if (abas.hasRecursoDenunciado) {
      abasFormatadas.push({aba: 'hasRecursoDenunciado', ativo: true, nome: this.messageService.getDescription('LABEL_ABA_RECURSO_DENUNCIADO')});
    }

    if (abas.hasJulgamentoSegundaInstancia) {
      abasFormatadas.push({aba: 'hasJulgamentoSegundaInstancia', ativo: true, nome: this.messageService.getDescription('LABEL_JULGAMENTO_SEGUNDA_INSTANCIA')});
    }

    this.abas = abasFormatadas;
  }

  /**
   * Retorna as abas selecionadas para geração do documento.
   *
   * @return sendJulgamento
   */
  private getDadosGerarDocumento = () => {
    let sendGeracaoDocumento: any = {
      idDenuncia: this.denuncia.id_denuncia
    };

    this.abas.forEach(element => {
      sendGeracaoDocumento[element.aba] = element.ativo;
    });


    return sendGeracaoDocumento;
  }
}
