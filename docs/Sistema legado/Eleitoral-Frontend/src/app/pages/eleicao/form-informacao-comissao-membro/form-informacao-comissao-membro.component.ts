import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

import * as _ from "lodash";
import { MessageService } from '@cau/message';
import { AtividadeSecundariaClientService } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-client.service';

@Component({
  selector: 'app-form-informacao-comissao-membro',
  templateUrl: './form-informacao-comissao-membro.component.html',
  styleUrls: ['./form-informacao-comissao-membro.component.scss']
})
export class FormInformacaoComissaoMembroComponent implements OnInit {

  public tabs: any = {};
  public eleicao: any = {};
  public atividadeSecundaria: any = {};
  public atividadeSecundariaCopia: any = {};

  /**
   * Construtor da classe.
   *
   * @param route
   * @param messageService
   * @param atividadeSecundariaService
   */
  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private messageService: MessageService,
    private atividadeSecundariaService: AtividadeSecundariaClientService
  ) {
    
    this.atividadeSecundaria = this.route.snapshot.data["atividadeSecundaria"];

    if (this.atividadeSecundaria.informacaoComissaoMembro && !_.isEmpty(this.atividadeSecundaria.informacaoComissaoMembro)) {
      this.atividadeSecundaria.informacaoComissaoMembro = this.atividadeSecundaria.informacaoComissaoMembro;
    } else {
      this.atividadeSecundaria.informacaoComissaoMembro = {};
    }

    if (!_.isEmpty(this.atividadeSecundaria.informacaoComissaoMembro) && !_.isEmpty(this.atividadeSecundaria.informacaoComissaoMembro.documentoComissaoMembro)) {
      this.atividadeSecundaria.informacaoComissaoMembro.documentoComissaoMembro = this.atividadeSecundaria.informacaoComissaoMembro.documentoComissaoMembro;
    } else {
      this.atividadeSecundaria.informacaoComissaoMembro.documentoComissaoMembro = {
        descricaoCabecalho: undefined,
        descricaoTextoFinal: undefined,
        descricaoTextoRodape: undefined,
        descricaoTextoInicial: undefined,
        situacaoTextoFinal: true,
        situacaoTextoRodape: true,
        situacaoTextoInicial: true,
        situacaoCabecalhoAtivo: true,
        informacaoComissaoMembro: {
          id: this.atividadeSecundaria.informacaoComissaoMembro.id
        }
      };
    }

    this.atividadeSecundariaCopia = JSON.parse(JSON.stringify(this.atividadeSecundaria));
  }

  /**
   * Função inicializada quando o componente carregar.
   */
  ngOnInit() {
    this.inicializarTabs();
  }

  /**
   * Inicializa o objeto correspondentes as tabs.
   */
  public inicializarTabs(): void {
    this.tabs = {
      abaInformacoesIniciais: { ativa: true, nome: 'informacoes' },
      abaDocumento: { ativa: false, nome: 'documento' }
    };
  }

  /**
   * Método responsável por atualizar a aba.
   *
   * @param abas
   * @param nomeAba
   */
  public mudarAbaSelecionada(tabs: any, nomeAba: string): void {
    tabs.abaDocumento.ativa = tabs.abaDocumento.nome == nomeAba ? true : false;
    tabs.abaInformacoesIniciais.ativa = tabs.abaInformacoesIniciais.nome == nomeAba ? true : false;

    if (tabs.abaDocumento.nome == nomeAba && this.atividadeSecundaria.informacaoComissaoMembro && !this.atividadeSecundaria.informacaoComissaoMembro.id) {

      tabs.abaDocumento.ativa = false;
      tabs.abaInformacoesIniciais.ativa = true;
      this.messageService.addConfirmOk('MSG_DADOS_ABA_INFORMACAO_NAO_SALVOS');

    } else if (tabs.abaDocumento.nome == nomeAba && this.hasMudancasAbaInformacoesIniciais()) {

      tabs.abaDocumento.ativa = false;
      tabs.abaInformacoesIniciais.ativa = true;

      this.messageService.addConfirmYesNo('LABEL_MUDAR_ABA', () => {
        tabs.abaDocumento.ativa = true;
        tabs.abaInformacoesIniciais.ativa = false;
        this.atividadeSecundaria = JSON.parse(JSON.stringify(this.atividadeSecundariaCopia));
      });

    } else if (tabs.abaInformacoesIniciais.nome == nomeAba && this.hasMudancasAbaDocumentos()) {

      tabs.abaDocumento.ativa = true;
      tabs.abaInformacoesIniciais.ativa = false;

      this.messageService.addConfirmYesNo('LABEL_MUDAR_ABA', () => {
        tabs.abaDocumento.ativa = false;
        tabs.abaInformacoesIniciais.ativa = true;
        this.atividadeSecundaria = JSON.parse(JSON.stringify(this.atividadeSecundariaCopia));
      });

    }
  }

  /**
   * Recarrega o objeto de atividade secundária.
   */
  public recarregarAtividadeSecundaria(): void {
    this.atividadeSecundariaService.getPorId(this.atividadeSecundaria.id).subscribe(data => {
      this.atividadeSecundaria = JSON.parse(JSON.stringify(data));
      this.atividadeSecundaria.informacaoComissaoMembro = this.atividadeSecundaria.informacaoComissaoMembro;
      this.atividadeSecundaria.informacaoComissaoMembro.documentoComissaoMembro = this.atividadeSecundaria.informacaoComissaoMembro.documentoComissaoMembro;

      if (this.atividadeSecundaria.informacaoComissaoMembro && this.atividadeSecundaria.informacaoComissaoMembro.email) {
        this.atividadeSecundaria.informacaoComissaoMembro.email = { id: undefined };
      }

      this.atividadeSecundariaCopia = JSON.parse(JSON.stringify(this.atividadeSecundaria));
    });
  }

  /**
   * Retorna para a tela de listagem.
   *
   * @param dsAba
   */
  public voltarLista(dsAba: string): void {

    let idCalendario = this.atividadeSecundaria.atividadePrincipalCalendario.calendario.id;

    if (dsAba == 'informacoesIniciais' && this.hasMudancasAbaInformacoesIniciais()) {
      this.messageService.addConfirmYesNo('LABEL_VOLTAR', () => {
        this.router.navigate(['eleicao', idCalendario, 'atividade-principal', 'lista']);
      });
    } else if (dsAba == 'documentos' && this.hasMudancasAbaDocumentos()) {
      this.messageService.addConfirmYesNo('LABEL_VOLTAR', () => {
        this.router.navigate(['eleicao', idCalendario, 'atividade-principal', 'lista']);
      });
    } else {
      this.router.navigate(['eleicao', idCalendario, 'atividade-principal', 'lista']);
    }
  }

  /**
   * Válida se existem mudanças nas informações iniciais.
   */
  private hasMudancasAbaInformacoesIniciais(): boolean {
    let hasMudancas = false;
    let informacaoComissaoMembro = JSON.parse(JSON.stringify(this.atividadeSecundaria.informacaoComissaoMembro));
    let informacaoComissaoMembroCopia = JSON.parse(JSON.stringify(this.atividadeSecundariaCopia.informacaoComissaoMembro));

    if (parseInt(informacaoComissaoMembro.quantidadeMaxima) != parseInt(informacaoComissaoMembroCopia.quantidadeMaxima)) {
      hasMudancas = true;
    }

    if (parseInt(informacaoComissaoMembro.quantidadeMinima) != parseInt(informacaoComissaoMembroCopia.quantidadeMinima)) {
      hasMudancas = true;
    }

    if (informacaoComissaoMembro.situacaoConselheiro != informacaoComissaoMembroCopia.situacaoConselheiro) {
      hasMudancas = true;
    }

    if (informacaoComissaoMembro.situacaoMajoritario != informacaoComissaoMembroCopia.situacaoMajoritario) {
      hasMudancas = true;
    }

    if (parseInt(informacaoComissaoMembro.tipoOpcao) != parseInt(informacaoComissaoMembroCopia.tipoOpcao)) {
      hasMudancas = true;
    }

    return hasMudancas;
  }

  /**
   * Válida se existem mudanças nas informações iniciais.
   */
  private hasMudancasAbaDocumentos(): boolean {
    let hasMudancas = false;

    if (!this.atividadeSecundaria.informacaoComissaoMembro.documentoComissaoMembro) {
      return hasMudancas;
    }

    let documentoComissaoMembro = JSON.parse(JSON.stringify(this.atividadeSecundaria.informacaoComissaoMembro.documentoComissaoMembro));
    let documentoComissaoMembroCopia = JSON.parse(JSON.stringify(this.atividadeSecundariaCopia.informacaoComissaoMembro.documentoComissaoMembro));

    if (documentoComissaoMembro.descricaoCabecalho != documentoComissaoMembroCopia.descricaoCabecalho) {
      hasMudancas = true;
    }

    if (documentoComissaoMembro.descricaoTextoFinal != documentoComissaoMembroCopia.descricaoTextoFinal) {
      hasMudancas = true;
    }

    if (documentoComissaoMembro.descricaoTextoInicial != documentoComissaoMembroCopia.descricaoTextoInicial) {
      hasMudancas = true;
    }

    if (documentoComissaoMembro.descricaoTextoRodape != documentoComissaoMembroCopia.descricaoTextoRodape) {
      hasMudancas = true;
    }

    if (documentoComissaoMembro.situacaoCabecalhoAtivo != documentoComissaoMembroCopia.situacaoCabecalhoAtivo) {
      hasMudancas = true;
    }

    if (documentoComissaoMembro.situacaoTextoFinal != documentoComissaoMembroCopia.situacaoTextoFinal) {
      hasMudancas = true;
    }

    if (documentoComissaoMembro.situacaoTextoInicial != documentoComissaoMembroCopia.situacaoTextoInicial) {
      hasMudancas = true;
    }

    if (documentoComissaoMembro.situacaoTextoRodape != documentoComissaoMembroCopia.situacaoTextoRodape) {
      hasMudancas = true;
    }

    return hasMudancas;
  }

}
