import { ActivatedRoute } from '@angular/router';
import { ControlContainer, NgForm } from '@angular/forms';
import { Component, OnInit, Output, EventEmitter, Input, OnChanges } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciadoSelectedInterface } from './denunciado-selected-interface';
import { CauUFService } from 'src/app/client/cau-uf-client/cau-uf-client.service';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';
import { ComissaoEleitoralService } from 'src/app/client/comissao-eleitoral-client/comissao-eleitoral-client.service';

import * as _ from 'lodash';

@Component({
  selector: 'app-selecao-denunciado',
  templateUrl: './selecao-denunciado.component.html',
  styleUrls: ['./selecao-denunciado.component.scss'],
  viewProviders: [{ provide: ControlContainer, useExisting: NgForm }]
})
export class SelecaoDenunciadoComponent implements OnInit, OnChanges {

  @Input('submitted') submitted;
  @Input('denunciado') denunciadoStorage;
  @Output('onSelect') denunciado: EventEmitter<any> = new EventEmitter();

  public ufs: any[];
  public chapas: any[];
  public filtroMembro: any;
  public tiposDenuncia: any[];
  public denunciadoSelected: DenunciadoSelectedInterface = {};

  constructor(
    private route: ActivatedRoute,
    private cauUFService: CauUFService,
    private messageService: MessageService,
    private chapaEleicaoService: ChapaEleicaoClientService,
    private comissaoEleitoralService: ComissaoEleitoralService,
  ) { }

  ngOnChanges() {
    this.initData();
  }

  ngOnInit() {
    this.filtroMembro = {};
    this.denunciadoSelected.uf = "";
    this.denunciadoSelected.chapa = "";
    this.denunciadoSelected.membro = "";
    this.denunciadoSelected.tipoDenuncia = "";
    this.initData();
    this.tiposDenuncia = this.route.snapshot.data['tiposDenuncia'];
  }

  private initData = () => {
    if (this.denunciadoStorage) {
      if (!this.ufs || this.ufs.length === 0) {
        this.ufs = [this.denunciadoStorage.uf];
      }

      if (!this.chapas || this.chapas.length === 0) {
        this.chapas = [this.denunciadoStorage.chapa];
      }

      this.filtroMembro.nomeRegistro = this.denunciadoStorage.membro.nome;
      if(this.denunciadoStorage.uf) {
        this.filtroMembro.idCauUf = this.denunciadoStorage.uf.idCauUf;
      }
      
      this.denunciadoSelected = this.denunciadoStorage;
    }
  }

  /**
   * Retorna se o tipo de denúncia é do tipo Chapa.
   */
  public isTipoDenunciaChapa = () => {
    return this.denunciadoSelected.tipoDenuncia == Constants.TIPO_DENUNCIA_CHAPA;
  }

  /**
   * Retorna se o tipo de denúncia é do tipo Membro Chapa.
   */
  public isTipoDenunciaMembroChapa = () => {
    return this.denunciadoSelected.tipoDenuncia == Constants.TIPO_DENUNCIA_MEMBRO_CHAPA;
  }

  /**
   * Retorna se o tipo de denúncia é do tipo Membro Comissão.
   */
  public isTipoDenunciaMembroComissao = () => {
    return this.denunciadoSelected.tipoDenuncia == Constants.TIPO_DENUNCIA_MEMBRO_COMISSAO;
  }

  /**
   * Atualiza o membro denunciado.
   *
   * @param membroSelecionado
   */
  public setMembroDenunciado = (membroSelecionado = null) => {
    if (null != membroSelecionado) {
      this.denunciadoSelected.membro = membroSelecionado.profissional;
      this.denunciadoStorage.membro.idMembro = membroSelecionado.id;
    }

    this.denunciado.emit(this.denunciadoSelected);
  }

  /**
   * Retorna o método de serviço para o autocomplete de Membros.
   *
   * @return string
   */
  public getMethodServiceAutocompleteMembro = () => {
    return this.isTipoDenunciaMembroComissao()
      ? 'getMembrosComissaoByUfAutocomplete'
      : 'getMembrosChapaByUfAutocomplete';
  }

  /**
   * Trigger que é executada quando o tipo de denúncia é alterado.
   */
  public tipoDenunciaChanged = () => {
    this.ufs = [];
    this.denunciadoSelected.uf = '';
    this.denunciadoSelected.chapa = '';
    this.denunciadoSelected.membro = '';

    switch (Number(this.denunciadoSelected.tipoDenuncia)) {
      case Constants.TIPO_DENUNCIA_CHAPA:
      case Constants.TIPO_DENUNCIA_MEMBRO_CHAPA:
        this.getUfsByChapa();
        break;

      case Constants.TIPO_DENUNCIA_MEMBRO_COMISSAO:
        this.getUfsByMembroComissao();
        break;

      case Constants.TIPO_DENUNCIA_OUTROS:
        this.getUfsByOutros();
        break;
    }
    this.denunciado.emit(this.denunciadoSelected);
  }

  /**
   * Trigger que é executada quando a UF é alterada.
   */
  public ufDenunciadoChanged = () => {
    this.filtroMembro = {};
    this.denunciadoSelected.chapa = '';
    this.denunciadoSelected.membro = '';

    if (this.denunciadoSelected.uf != '') {
      switch (Number(this.denunciadoSelected.tipoDenuncia)) {
        case Constants.TIPO_DENUNCIA_CHAPA:
          this.getChapasByUf();
          break;

        case Constants.TIPO_DENUNCIA_MEMBRO_CHAPA:
        case Constants.TIPO_DENUNCIA_MEMBRO_COMISSAO:
          this.filtroMembro.idCauUf = this.denunciadoSelected.uf.idCauUf;
          break;
      }
    }

    this.setMembroDenunciado();
  }

  /**
   * Trigger que é executada quando a chapa denunciada é alterada.
   */
  public chapaDenunciadaChanged = () => {
    this.denunciadoSelected.membro = '';

    if (this.denunciadoSelected.tipoDenuncia == Constants.TIPO_DENUNCIA_CHAPA) {
      this.denunciado.emit(this.denunciadoSelected);
    }
  }

  /**
   * Retorna o placeholder do autocomplete de Membros.
   */
  public getPlaceholderAutoCompleteMembro = () => {
    return this.messageService.getDescription('LABEL_INSIRA_NOME_OU_REGISTRO');
  }

  /**
   * Busca as UFs de acordo com o tipo de denúncia Chapa.
   */
  private getUfsByChapa = () => {
    this.chapaEleicaoService.getUfs().subscribe(
      data => {
        if (data.length < 1) {
          this.messageService.addMsgInf('MSG_NAO_ENCONTRADOS_REGISTROS_CADASTRADOS_TIPO_DENUNCIA');
          this.denunciadoSelected.tipoDenuncia = "";
          return;
        }

        this.ufs = data;
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }

  /**
   * Busca as UFs de acordo com o tipo de denúncia Membro Comissão.
   */
  private getUfsByMembroComissao = () => {
    this.comissaoEleitoralService.getUfs().subscribe(
      data => {
        if (data.length < 1) {
          this.messageService.addMsgInf('MSG_NAO_ENCONTRADOS_REGISTROS_CADASTRADOS_TIPO_DENUNCIA');
          this.denunciadoSelected.tipoDenuncia = "";
          return;
        }

        this.ufs = data;
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }

  /**
   * Busca as chapas de acordo com a UF selecionada.
   */
  private getChapasByUf = () => {
    let uf = this.denunciadoSelected.uf.idCauUf;

    if (uf != '') {
      this.chapaEleicaoService.getChapasPorUf(uf).subscribe(
        data => {
          this.chapas = _.orderBy(data, ['numeroChapa'], ['asc']);
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    }
  }

  /**
   * Busca as UFs de acordo com o tipo de denúncia Outros.
   */
  private getUfsByOutros = () => {
    this.cauUFService.getCauUFsComIES().subscribe(
      data => {
        if (data.length < 1) {
          this.messageService.addConfirmOk('MSG_NAO_ENCONTRADOS_REGISTROS_CADASTRADOS_TIPO_DENUNCIA');
          return;
        }

        this.ufs = data;
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }
}
