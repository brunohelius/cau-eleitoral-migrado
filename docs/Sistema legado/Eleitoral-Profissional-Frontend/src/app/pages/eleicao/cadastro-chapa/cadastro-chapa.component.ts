import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { MessageService } from '@cau/message';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';
import { AbaPlataformaEleitoralRedesSociaisComponent } from './abas-cadastro-chapa/aba-plataforma-eleitoral-redes-sociais/aba-plataforma-eleitoral-redes-sociais.component';
import { AbaMembrosChapaComponent } from './abas-cadastro-chapa/aba-membros-chapa/aba-membros-chapa.component';
import { AbaDeclaracaoComponent } from './abas-cadastro-chapa/aba-declaracao/aba-declaracao.component';

@Component({
  selector: 'app-cadastro-chapa',
  templateUrl: './cadastro-chapa.component.html',
  styleUrls: ['./cadastro-chapa.component.scss']
})
export class CadastroChapaComponent implements OnInit {

  public abas: any = {};
  public chapaEleicao: any = {};
  public eleicaoVigente: any = {};

  @ViewChild(AbaPlataformaEleitoralRedesSociaisComponent, null) abaPlataformaEleitoral: AbaPlataformaEleitoralRedesSociaisComponent;
  @ViewChild(AbaMembrosChapaComponent, null) abaMembrosChapa: AbaMembrosChapaComponent;
  @ViewChild(AbaDeclaracaoComponent, null) abaDeclaracao: AbaDeclaracaoComponent;

  /**
   * Construtor da classe.
   * 
   * @param route 
   * @param router
   * @param messageService 
   * @param chapaEleicaoService 
   */
  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private messageService: MessageService,
    private chapaEleicaoService: ChapaEleicaoClientService
  ) {
    let dadosChapa = this.route.snapshot.data['chapaEleicao'];
    this.chapaEleicao = dadosChapa.chapaEleicao;
    this.eleicaoVigente = dadosChapa.eleicaoVigente;
  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
    this.inicializaAbas();

    if (this.chapaEleicao) {
      this.continuarPrenchimento();
    }
  }

  /**
   * Muda a aba selecionada de acordo com a seleção.
   *
   * @param abas
   * @param nomeAba
   */
  public mudarAbaSelecionada(aba: number): void {
    if (this.isCamposAlterados()) {   
      this.messageService.addConfirmYesNo('MSG_CONFIRMA_MUDAR_ABA', () => {
        if(aba == Constants.ABA_VISAO_GERAL) {
          this.router.navigate(['/']);
        } else {
          this.mudarAba(aba);
        }        
      });
    } else {
      if(aba == Constants.ABA_VISAO_GERAL) {
        this.router.navigate(['/']);
      } else {
        this.mudarAba(aba);
      }  
    }
  }

  /**
   * Muda a aba para a aba selecionada.
   */
  private mudarAba(aba: number): void {    
    this.abas.abaVisaoGeral.ativa = this.abas.abaVisaoGeral.id == aba;
    this.abas.abaPlataformaEleitoral.ativa = this.abas.abaPlataformaEleitoral.id == aba;
    this.abas.abaMembrosChapa.ativa = this.abas.abaMembrosChapa.id == aba;
    this.abas.abaDeclaracao.ativa = this.abas.abaDeclaracao.id == aba;
  }

  /**
   * Verifica se houve alteração no formulário da aba atual.
   */
  public isCamposAlterados(): boolean {
    if (this.abaPlataformaEleitoral) {
      return this.abaPlataformaEleitoral.isCamposAlterados();
    }
    if (this.abaDeclaracao) {
      return this.abaDeclaracao.isCamposAlterados();
    }

    if (this.abaMembrosChapa) {
      return this.abaMembrosChapa.isCamposAlterados();
    }
  }

  /**
   * Realiza a troca entre abas.
   *
   * @param aba
   */
  public avancar(aba: number): void {
    if (this.chapaEleicao.idEtapa >= Constants.STATUS_CHAPA_ETAPA_CONCLUIDO) {
      this.router.navigate(['eleicao', 'convite-chapa']);
    } else {
      if (this.chapaEleicao.idEtapa == Constants.STATUS_CHAPA_ETAPA_REDES_SOCIAIS_INCLUIDA && aba == Constants.ABA_DECLARACAO) {
        this.chapaEleicao.idEtapa = Constants.STATUS_CHAPA_ETAPA_MEMBROS_CHAPA_INCLUIDA;
      }
      this.mudarAba(aba);
    }  
  }

  /**
  * Responsável por cancelar o cadastro na aba.
  */
  public cancelar(): void {
    this.messageService.addConfirmYesNo('MSG_CANCELAR_CADASTRO_CHAPA', () => {
      if (this.chapaEleicao.id) {
        this.chapaEleicaoService.excluir(this.chapaEleicao.id).subscribe(data => {
          this.messageService.addMsgSuccess(data);
        }, error => {
          this.messageService.addMsgDanger(error);
        });
      }
      this.router.navigate(['/']);
    });
  }

  /**
   * Retorna para aba anterior.
   * 
   * @param controle
   */
  public retroceder(controle: any): void {
    if (controle.isAlterado) {
      this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_VOLTAR', () => {
        if(controle.aba == Constants.ABA_VISAO_GERAL) {
          this.router.navigate(['/']);
        } else {
          this.mudarAba(controle.aba);
        }        
      });
    }
    else {
      if(controle.aba == Constants.ABA_VISAO_GERAL) {
        this.router.navigate(['/']);
      } else {
        this.mudarAba(controle.aba);
      } 
    }
  }

  public voltar(isAterado: boolean): void {
    if (isAterado) {
      this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_VOLTAR', () => {
        this.router.navigate(['/']);
      });
    }
    else {
      this.router.navigate(['/']);
    }
  }

  /**
   * Verifica se a aba declaração está visível.
   */
  public isAbaDeclaracaoVisivel(): boolean {
    return this.chapaEleicao.idEtapa >= Constants.STATUS_CHAPA_ETAPA_MEMBROS_CHAPA_INCLUIDA;
  }

  /**
   * Inicializa o objeto de abas.
   */
  private inicializaAbas(): void {
    this.abas = {
      abaVisaoGeral: { id: Constants.ABA_VISAO_GERAL, nome: 'visaoGeral', ativa: true },
      abaDeclaracao: { id: Constants.ABA_DECLARACAO, nome: 'declaracao', ativa: false },
      abaMembrosChapa: { id: Constants.ABA_MEMBROS_CHAPA, nome: 'membrosChapa', ativa: false },
      abaPlataformaEleitoral: { id: Constants.ABA_PLATAFORMA_ELEITORAL_REDES_SOCIAIS, nome: 'plataformaEleitoral', ativa: false },
    };
  }

  /**
   * Avança as abas até a aba que estava sendo preenchida.
   */
  private continuarPrenchimento(): void {
    if (this.chapaEleicao.id) { 
      this.mudarAba(this.chapaEleicao.idEtapa + 2);
    } else {
      this.mudarAba(Constants.ABA_VISAO_GERAL);
    }
  }

}
