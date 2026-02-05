import { Component, OnInit, ViewChild } from '@angular/core';
import { LayoutsService } from '@cau/layout';
import { ActivatedRoute, Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { SecurityService } from '@cau/security';
import { AbaPlataformaEleitoralRedeSocialVisualizarComponent } from './abas-visualizar-chapa/aba-plataforma-eleitoral-rede-social-visualizar/aba-plataforma-eleitoral-rede-social-visualizar.component';
import { MessageService } from '@cau/message';

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

  @ViewChild(AbaPlataformaEleitoralRedeSocialVisualizarComponent, null) AbaPlataformaEleitoral: AbaPlataformaEleitoralRedeSocialVisualizarComponent ;

  /**
   * Construtor da classe.
   *
   * @param route
   * @param Router
   * @param SecurityService
   */
  constructor(
    private layoutsService: LayoutsService,
    private route: ActivatedRoute,
    private router: Router,
    private securtyService: SecurityService,
    private messageService: MessageService,

  ) {
    let dadosChapa = this.route.snapshot.data['chapaEleicao'];
    this.eleicao = dadosChapa.eleicaoVigente;
    this.chapaEleicao = dadosChapa.chapaEleicao;
  }

  /**
   * Executado quando o método componente for carregado.
   */
  ngOnInit() {
    
    /**
    * Define ícone e título do header da página 
    */
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-wpforms',
      description: this.messageService.getDescription('TITLE_CADASTRO_CHAPAS')
    });

    this.usuario = this.securtyService.credential["_user"];
    this.checkChapa();
    if (!this.chapaEleicao.id) {
      let atividadePrincipal = JSON.parse(JSON.stringify(this.eleicao.calendario.atividadesPrincipais)).shift();
      let atividadeSecundaria = JSON.parse(JSON.stringify(atividadePrincipal.atividadesSecundarias)).shift()
      this.chapaEleicao.atividadeSecundariaCalendario = atividadeSecundaria;
      this.chapaEleicao.tipoCandidatura = { id: 1, descricao: "Conselheiros UF-BR" };
    }
    this.inicializaAbas();
  }

  /**
   * Valida e realiza a mudança de aba.
   *
   * @param nomeAba
   */
  public mudarAbaSelecionada(nomeAba: string): void {
    if(this.isCamposAlterados()) {
      this.messageService.addConfirmYesNo('MSG_CONFIRMA_MUDAR_ABA', () => {
        this.mudarAba(nomeAba);
      });
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
      abaVisaoGeral:{ nome: 'visaoGeral', ativa: false },
    };

    if (this.abas.abaMembrosChapa.nome == nomeAba) {
      this.abas.abaMembrosChapa.ativa = true;
    }

    if (this.abas.abaPlataformaEleitoral.nome == nomeAba) {
      this.abas.abaPlataformaEleitoral.ativa = true;
    }

    if (this.abas.abaVisaoGeral.nome == nomeAba) {
      this.abas.abaVisaoGeral.ativa = true;
      this.abas.abaPlataformaEleitoral.ativa = true;
    }
  }

  /**
   * Verifica se houve alteração no formulário da aba atual.
   */
  public isCamposAlterados(): boolean {
    if(this.AbaPlataformaEleitoral) {
      return this.AbaPlataformaEleitoral.isCamposAlterados();
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
      abaVisaoGeral: { nome: 'visaoGeral', ativa: false }
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
      this.abas = {
        abaVisaoGeral: { nome: 'visaoGeral', ativa: false },
        abaDeclaracao: { nome: 'declaracao', ativa: false },
        abaMembrosChapa: { nome: 'membrosChapa', ativa: true },
        abaPlataformaEleitoral: { nome: 'plataformaEleitoral', ativa: false },
      }
    }
  }

  /**
   * Verifica se o cadastro da chapa foi concluído. 
   */
  public checkChapa(): void {
    if (this.chapaEleicao.id == undefined || this.chapaEleicao.idEtapa < Constants.STATUS_CHAPA_ETAPA_CONCLUIDO) {
      this.router.navigate(['eleicao', 'cadastro-chapa']);
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
   * Verifica se o usuário pode editar a chapa.
   */
  public isUsuarioEditor(): boolean {
    return (this.isUsuarioCriador() || this.isUsuarioResponsavelConfirmado());
  }

  /**
   * Verifica se a eleição está dentro do prazo de cadastro definido pela atividade 2.1.
   */
  public isEleicaoDentroDoPrazoCadastro(): boolean{
    let dataInicio = this.chapaEleicao.atividadeSecundariaCalendario.dataInicio;
    let dataFim = this.chapaEleicao.atividadeSecundariaCalendario.dataFim;

    dataFim = new Date(dataFim);
    dataFim.setDate(dataFim.getDate() + 1);

    let hoje = new Date();
    hoje.setHours(0,0,0,0);
    dataFim.setHours(23,59,59,999);
    return hoje <= dataFim;
  }

  /**
   * Verifica se o usuário é o criador da chapa.
   */
  private isUsuarioCriador(): boolean {
    let idProfissionalCriadorChapa = this.chapaEleicao.idProfissionalInclusao;
    return this.usuario.idProfissional == idProfissionalCriadorChapa;
  }

  /**
   * Verifica se o usuário é responsavel com participação confirmada.
   */
  private isUsuarioResponsavelConfirmado(): boolean {
    let responsavelConfirmado = this.chapaEleicao.membrosChapa.find( membroChapa => {
      if (membroChapa.profissional.id == this.usuario.idProfissional) {
        let isUsuarioResponsavel: boolean = membroChapa.situacaoResponsavel;
        let isParticipacaoConfirmada: boolean = membroChapa.statusParticipacaoChapa.id == Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_CONFIRMADO;
        return  isUsuarioResponsavel && isParticipacaoConfirmada;
      }
      return false;
    });
    return responsavelConfirmado != undefined;
  }
  
}
