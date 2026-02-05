import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { SecurityService } from '@cau/security';
import { AbaPlataformaEleitoralRedeSocialVisualizarComponent } from './abas-visualizar-chapa/aba-plataforma-eleitoral-rede-social-visualizar/aba-plataforma-eleitoral-rede-social-visualizar.component';
import { MessageService } from '@cau/message';
import { AbaMembrosChapaVisualizarComponent } from './abas-visualizar-chapa/aba-membros-chapa-visualizar/aba-membros-chapa-visualizar.component';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-client/chapa-eleicao-client.service';

@Component({
  selector: 'app-visualizar-chapa',
  templateUrl: './visualizar-chapa.component.html',
  styleUrls: ['./visualizar-chapa.component.scss']
})
export class VisualizarChapaComponent implements OnInit {

  public eleicao: any;
  public abas: any = {};
  public chapaEleicao: any;
  public usuario: any;
  public possuiRetificacao: any;

  @ViewChild(AbaPlataformaEleitoralRedeSocialVisualizarComponent, null) AbaPlataformaEleitoral: AbaPlataformaEleitoralRedeSocialVisualizarComponent;
  @ViewChild(AbaMembrosChapaVisualizarComponent, null) AbaMembrosChapaVisualizarComponent: AbaMembrosChapaVisualizarComponent;


  /**
   * Construtor da classe.
   *
   * @param route
   * @param Router
   * @param SecurityService
   */
  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private securtyService: SecurityService,    
    private messageService: MessageService,
    private chapaEleitoralService: ChapaEleicaoClientService

  ) {
    this.eleicao = this.route.snapshot.data['eleicao'];
    this.chapaEleicao = this.route.snapshot.data['chapaEleicao'];
    this.possuiRetificacao = this.route.snapshot.data['possuiRetificacao'];
  }

  /**
   * Executado quando o método componente for carregado.
   */
  ngOnInit() {
    this.usuario = this.securtyService.credential["_user"];
    this.inicializaAbas();
  }

  /**
   * Valida e realiza a mudança de aba.
   *
   * @param nomeAba
   */
  public mudarAbaSelecionada(nomeAba: string): void {
    if(this.isCamposAlterados()) {
      if(this.AbaMembrosChapaVisualizarComponent && this.AbaMembrosChapaVisualizarComponent.isCamposAlterados()) {
        this.messageService.addConfirmYesNo('MSG_CONFIRMA_MUDAR_ABA_AGUARDANDO_CONCLUSAO', () => {
          this.mudarAba(nomeAba);
        });
      } else {
        this.messageService.addConfirmYesNo('MSG_CONFIRMA_MUDAR_ABA', () => {
          this.mudarAba(nomeAba);
        });
      }      
    } else {
      this.mudarAba(nomeAba);
    }
  }

  /**
   * Muda a aba selecionada de acordo com a seleção.
   * 
   * @param nomeAba 
   */
  private mudarAba(nomeAba: string): void {
    this.abas = {
      abaMembrosChapa: { nome: 'membrosChapa', ativa: false },
      abaPlataformaEleitoral: { nome: 'plataformaEleitoral', ativa: false },
    };

    if (this.abas.abaMembrosChapa.nome == nomeAba) {
      this.abas.abaMembrosChapa.ativa = true;
    }

    if (this.abas.abaPlataformaEleitoral.nome == nomeAba) {
      this.abas.abaPlataformaEleitoral.ativa = true;
    }
  }


  /**
   * Redireciona para a pagina inicial.
   */
  public home(): void{
    if(this.isCamposAlterados()) {
      this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_SAIR', () => {
        this.router.navigate(['/']);
      });
    } else {
      this.router.navigate(['/']);
    }
  }

  /**
   * Verifica se houve alteração no formulário da aba atual.
   */
  public isCamposAlterados(): boolean {
    if(this.AbaPlataformaEleitoral) {
      return this.AbaPlataformaEleitoral.isCamposAlterados();
    }
    if(this.AbaMembrosChapaVisualizarComponent){
      return this.AbaMembrosChapaVisualizarComponent.isCamposAlterados();
    }
    return false;
  }

  /**
   * Inicializa o objeto de abas.
   */
  private inicializaAbas(): void {
    this.abas = {
      abaMembrosChapa: { nome: 'membrosChapa', ativa: true },
      abaPlataformaEleitoral: { nome: 'plataformaEleitoral', ativa: false },
    };
  }

  /**
   * Realiza a troca entre abas.
   *
   * @param aba
   */
  public avancar(aba: number): void {

    if (Constants.ABA_VISAO_GERAL == aba) {
      this.abas = {
        abaVisaoGeral: { nome: 'visaoGeral', ativa: false },
        abaDeclaracao: { nome: 'declaracao', ativa: false },
        abaMembrosChapa: { nome: 'membrosChapa', ativa: false },
        abaPlataformaEleitoral: { nome: 'plataformaEleitoral', ativa: true },
      }
    }

    if (Constants.ABA_PLATAFORMA_ELEITORAL_REDES_SOCIAIS == aba) {
      this.abas = {
        abaVisaoGeral: { nome: 'visaoGeral', ativa: false },
        abaDeclaracao: { nome: 'declaracao', ativa: false },
        abaMembrosChapa: { nome: 'membrosChapa', ativa: true },
        abaPlataformaEleitoral: { nome: 'plataformaEleitoral', ativa: false },
      }
    }

    if (Constants.ABA_MEMBROS_CHAPA == aba) {
      this.chapaEleitoralService.getIsPossuiRetificacao(this.chapaEleicao.id).subscribe((data)=>{
        this.possuiRetificacao = data;
      });
      this.abas = {
        abaVisaoGeral: { nome: 'visaoGeral', ativa: false },
        abaDeclaracao: { nome: 'declaracao', ativa: false },
        abaMembrosChapa: { nome: 'membrosChapa', ativa: true },
        abaPlataformaEleitoral: { nome: 'plataformaEleitoral', ativa: false },
      }
    }
  }

  /**
   * Verifica se a eleição está concluída.
   */
  public isChapaConcluida(): boolean {
    if(this.chapaEleicao.statusChapaVigente) {
      return this.chapaEleicao.statusChapaVigente.id == Constants.STATUS_CHAPA_CONCLUIDO;
    }
    return false;
  }

  /**
   * Verifica se o usuário é Acessor CEN.
   */
  public isUsuarioCEN(): boolean {
    return this.securtyService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
  }

  /**
   * Verifica se o usuário é Acessor CE.
   */
  public isUsuarioCE(): boolean {
    return this.securtyService.hasRoles([Constants.ROLE_ACESSOR_CE]) && this.usuario.cauUf.id == this.chapaEleicao.cauUf.id;
  }

  /**
   * Verifica se a eleição está dentro do prazo de cadastro definido pela atividade 2.1.
   */
  public isEleicaoDentroDoPrazoCadastro(): boolean{
    let dataInicio = this.chapaEleicao.atividadeSecundariaCalendario.dataInicio;
    let dataFim = this.chapaEleicao.atividadeSecundariaCalendario.dataFim;
    dataFim = new Date(dataFim);
    let hoje = new Date();
    hoje.setHours(0,0,0,0);
    dataFim.setHours(23,59,59,999);
    return hoje <= dataFim;
  }
  
}
