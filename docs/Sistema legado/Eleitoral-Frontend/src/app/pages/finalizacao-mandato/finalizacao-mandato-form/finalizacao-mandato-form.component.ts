import { formatDate } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';

import { AcaoSistema } from 'src/app/app.acao';
import { TipoFinalizacaoMandatoService } from 'src/app/client/tipo-finalizacao-mandato/tipo-finalizacao-mandato.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-finalizacao-mandato-form',
  templateUrl: './finalizacao-mandato-form.component.html',
  styleUrls: ['./finalizacao-mandato-form.component.scss']
})
export class FinalizacaoMandatoFormComponent implements OnInit {

  constructor(
    private route: ActivatedRoute,
    private messageService: MessageService,
    private tipoFinalizacaoMandatoService: TipoFinalizacaoMandatoService,
    private securtyService: SecurityService,
  ) { 
    this.user = this.securtyService.credential["_user"];
  }

  public user: any;
  public acao: AcaoSistema;
  public tipoFinalizacao: any;
  public listaTipoFinalizacaoMandato: any;
  public itensLista: any;

  public tamanhoDescricao: number = 0;
  public alertaTamanhoDescricao: boolean = false;

  public submitted: boolean = false;

  public numeroRegistrosPaginacao: number = 10;

  public search: any;
  
  ngOnInit() {
    this.acao = new AcaoSistema(this.route);

    this.initiTipoFinalizacaoDefault();
    if (this.acao.isAcaoAlterar()) {
      this.initTipoFinalizacaoById();
    }
    this.validarPermissao();
    this.listaTipoFinalizacao();
  }

   /**
   * Cria o objeto Tipo de Finalização com valor filtrando ID
   */
  public initTipoFinalizacaoById(): void{
    this.tipoFinalizacaoMandatoService.getById(this.route.snapshot.params.id).subscribe(data => {
      this.tipoFinalizacao = {
        descricao: data.descricao,
        ativo: data.ativo,
        idUsuario: this.user.id
      };
      this.contarCaracteres();
     }, error => {
       this.messageService.addMsgDanger(error);
     });
  }

   /**
  * Valida se usuário logado tem permissão para acessar a página
  */
   public validarPermissao(): void {
    if (!this.user.roles.find(roles => roles == '01601020') &&
      !this.user.roles.find(roles => roles == '01601021') &&
      !this.user.roles.find(roles => roles == '01601022')
      ) {
        window.location.href = `${environment.urlLogout}/home`;
    }
  }

   /**
   * Cria o objeto Tipo de Finalização com valor default
   */
  public initiTipoFinalizacaoDefault(): void{
    this.tipoFinalizacao = {
      descricao: '',
      ativo: true,
      idUsuario: this.user.id
    };
  }

   /**
   * Busca a lista de Tipos de Finalização de Mandatos cadastrado
   */
  public listaTipoFinalizacao(): void {
    let params = {};
    this.tipoFinalizacaoMandatoService.getByFilter(params).subscribe(data => {
     this.listaTipoFinalizacaoMandato = data;
     this.itensLista = data;
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Contador de caracteres para o campo Descrição
   */
  public contarCaracteres(): void {
    this.tamanhoDescricao = this.tipoFinalizacao.descricao.length;
    this.validarTamanhoDescricao();
  }

  /**
   * Validação de limite de caracteres para campo descrição
   */
  public validarTamanhoDescricao(): boolean {
    if (this.tamanhoDescricao > 100) {
      this.messageService.addMsgDanger('MSG_TAMANHO_MAXIMO_CARACTERES');
      this.alertaTamanhoDescricao = true;
      return false;
    }
    this.alertaTamanhoDescricao = false;
    return true;
  }

  /**
   * Cadastrar Tipo de Finalização de Mandato
   */
  public salvar(formTipoFinalizacao: NgForm): void {
    this.submitted = true;
    if (formTipoFinalizacao.valid) {
      this.tipoFinalizacaoMandatoService.salvar(this.tipoFinalizacao).subscribe(data => {
        this.messageService.addMsgSuccess('MSG_REGISTRO_SALVO');
        location.reload();
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
   * Cadastrar Tipo de Finalização de Mandato
   */
  public alterar(formTipoFinalizacao: NgForm): void {
    this.submitted = true;
    if (formTipoFinalizacao.valid) {
      this.tipoFinalizacaoMandatoService.update(this.tipoFinalizacao, this.route.snapshot.params.id).subscribe(data => {
        this.messageService.addMsgSuccess('MSG_REGISTRO_SALVO');
        window.location.href = `${environment.urlLocal}/finalizacaoMandato/`;
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
  * Cancelar cadastro
  */
  public cancelar() {
    window.location.href = `${environment.urlLocal}/finalizacaoMandato/incluir`;
  }

  /**
  * Abrir alterar Tipo Finalização de Mandato
  */
  public abrirAlterar(id):void {
    window.location.href = `${environment.urlLocal}/finalizacaoMandato/${id}/alterar`;
  }

  /**
  * Deletar um Tipo Finalização Mandato
  */
  public excluir(id):void {
    this.messageService.addConfirmYesNo('MSG_EXCLUSAO_REGISTRO', () => {
      this.tipoFinalizacaoMandatoService.delete(id).subscribe(data => {
          this.messageService.addMsgSuccess('MSG_EXCLUSAO_EFETUADA_SUCESSO');
          window.location.href = `${environment.urlLocal}/finalizacaoMandato/incluir`;
      }, error => {
          this.messageService.addMsgDanger('MSG_EXCLUSAO_NAO_EFETUADA');
      });
    });
  }

  /**
   * Filtra os dados da grid conforme o valor informado na variável search.
   *
   * @param search
   */
  public filter(search:any) {
    if (search.length >= 2) {
      let filterItens = this.itensLista.filter((data) => {
        let textSearch = this.getSearchArray(data).join().toLowerCase();
        return textSearch.indexOf(search.toLowerCase()) !== -1
      });
      this.itensLista = filterItens;
      return;
    }
    
    this.itensLista = this.listaTipoFinalizacaoMandato;
  }

  /**
   * Cria array utilizado para buscar de termos na listagem.
   *
   * @param obj
   */
  private getSearchArray(obj: any): Array<any> {
    let values: Array<any> = [];
    values.push(formatDate(obj.dt_cadastro, 'dd/MM/yyyy mm:ss', 'en-US'));
    values.push(obj.descricao);
    values.push(obj.codigo);

    return values;
  }
}
