import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { MessageService } from '@cau/message';
import { EleicaoClientService } from 'src/app/client/eleicao-client/eleicao-client.service';
import { SecurityService } from '@cau/security';

@Component({
  selector: 'aba-cadastro-historico-comissao-membro',
  templateUrl: './aba-cadastro-historico-comissao-membro.component.html',
  styleUrls: ['./aba-cadastro-historico-comissao-membro.component.scss']
})
export class AbaCadastroHistoricoComissaoMembroComponent implements OnInit {
  @Input() eleicoes: any;
  @Input() anosEleicoes: any;
  @Input() cauUfs: any;
  @Input() configuracaoEleicao: any;
  @Input() tiposParticipacao: any;
  
  public editable: boolean;
  public eleicao: any;
  public numeroMembros: any;
  public cauUfSelecionada: string;
  public ocorrencias: Array<any> = [];
  public qtdMembros: number;
  public usuario: any;

/**
   * Construtor da classe
   * @param route 
   * @param messageService 
   * @param eleicaoClientService 
   */
  constructor(
    private route: ActivatedRoute,
    private messageService: MessageService,
    private eleicaoClientService: EleicaoClientService,
    private securityService: SecurityService
  ) { }

  ngOnInit() {
    this.usuario = {};
    this.usuario = this.securityService.credential["_user"];
    let calendario = this.configuracaoEleicao.atividadeSecundaria.atividadePrincipalCalendario.calendario;
    this.setObjEleicao({
      id: this.configuracaoEleicao.id,
      eleicao: calendario.eleicao,
      anoEleicao: calendario.ano,
      cauUf: this.usuario.cauUf.id,
      minimoMembrosComissao: this.configuracaoEleicao.quantidadeMinima,
      maximoMembrosComissao: this.configuracaoEleicao.quantidadeMaxima,
      tipoOpcao: this.configuracaoEleicao.tipoOpcao
    });
    this.setCauUfSelecionada('CAU/BR');
    this.getHistoricoComissao();
    this.getMembrosComissao();
  }

  /**
   * Define a descrição da CAU/UF selecionada
   */
  public setCauUfSelecionada(cauUf: string) {
    this.cauUfSelecionada = cauUf;
  }

  /**
   * setNumeroMembrosComissao
   */
  public setNumeroMembrosComissao(numberoMembros: number) {
    this.numeroMembros = numberoMembros;
  }

  /**
   * Define os dados do objeto eleição
   * objEleicao: any   
   */
  public setObjEleicao(objEleicao: any) {
    this.eleicao = objEleicao;
  }

  /**
   * Requisita a lista de historico dos membros da comissão
   */
  public getHistoricoComissao(){
    this.eleicaoClientService.getHistoricoMembrosComissao(this.eleicao.id).subscribe(data => {
      this.ocorrencias = data;
    });
  }

  /**
   * Metodo que requisita a lista de membros das comissões agrupado por CAU/UF
   */
  public getMembrosComissao(): void {
    this.eleicaoClientService.getMembroComissoes(this.eleicao.id, this.eleicao.cauUf).subscribe(
      data => {
        this.qtdMembros = data.qtdMembros;
      },
      error => {
        this.messageService.addMsgDanger(error.message);
      }
    );
  }

  /**
   * Método utilizado pelo 'select' para comparar values de 'options'.
   * @param optionValue
   * @param selectedValue
   */
  public compareFn(optionValue, selectedValue) {
    return optionValue && selectedValue ? optionValue.id === selectedValue.id : optionValue === selectedValue;
  }

}
