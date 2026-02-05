import { Component, OnInit } from '@angular/core';
import * as _ from "lodash";
import { MessageService } from '@cau/message';
import { NgForm } from '@angular/forms';
import { SecurityService } from '@cau/security';
import { Router} from "@angular/router";
import { LayoutsService } from '@cau/layout';

import { CalendarioClientService } from '../../../client/calendario-client/calendario-client.service';
import { UFClientService } from '../../../client/uf-client/uf-client.service';
import { DiplomaEleitoralService } from '../../../client/diploma-eleitoral-client/diploma-eleitoral-client.service';
import { ProfissionalClientService } from '../../../client/profissional-client/profissional-client.service';
import { PortalClientService } from '../../../client/portal-client/portal-client.service';
import { Constants } from 'src/app/constants.service';

@Component({
  selector: 'app-form-diploma-eleitoral',
  templateUrl: './form-diploma-eleitoral.component.html',
  styleUrls: ['./form-diploma-eleitoral.component.scss']
})
export class FormDiplomaEleitoralComponent implements OnInit {
  public diploma: any;

  public dias: any = [];
  public meses: any;
  public anos: any;
  public ufs: any;
  public anoHoje: any = new Date().getFullYear();
  public anoResolucao: any = [];
  public membro: any;

  public submitted: boolean = false;
  public submittedAssinatura: boolean = false;

  public user: any;

  public textoDiploma: any = '';
  public textoPadrao: any = '';
  /**
   * Tipos suportados de arquivo
   */
  public supportedTypes = [
    'image/gif',
    'image/png',
    'image/jpeg',
    'image/jpg'
  ];

  /**
   * Tamanho maximo suportado de arquivo
   */
  public MAX_SIZE = 20480; // 20 mb

  /**
   * Construtor da classe.
   *
   * @param messageService
   * @param calendarioClientService
   */
  constructor(
    private messageService: MessageService,
    private calendarioClientService: CalendarioClientService,
    private ufService: UFClientService,
    private router: Router,
    private diplomaEleitoralService: DiplomaEleitoralService,
    private profissionalClientService: ProfissionalClientService,
    private securityService: SecurityService,
    private layoutsService: LayoutsService,
    private portalClientService: PortalClientService
  ) {
    if (this.router.getCurrentNavigation().extras.state) {
      this.membro = this.router.getCurrentNavigation().extras.state.membro;
      localStorage.setItem('membro', JSON.stringify(this.membro));
    } else {
      this.membro = JSON.parse(localStorage.membro);
    }
  }

  /**
   * Função inicializada quando o componente carregar.
   */
  ngOnInit() {
    this.layoutsService.onLoadTitle.emit({
      description: this.messageService.getDescription(
          'LABEL_EMITIR_DIPLOMA'
      ),
    });

    this.user = this.securityService.credential["_user"];
    this.diploma = {
      diaRealizacao: 17,
      mesRealizacao: 10,
      anoRealizacao: 2023,
      diaRealizacao2: '',
      numeroResolucao: 179,
      diaResolucao: 22,
      mesResolucao: 8,
      anoResolucao: this.anoHoje,
      cpfConselheiro: this.membro.cpf ? this.membro.cpf : null,
      nomeConselheiro: this.membro.nome ? this.membro.nome : null,
      UfConselheiro: this.membro.uf ? this.membro.uf : '',
      tipoConselheiro:  this.membro.idTipo ? this.membro.idTipo : '',
      idConselheiro: null,
      diaEmissao: 11,
      mesEmissao: 12,
      anoEmissao: 2023,
      cidadeEmissao: this.user.cauUf.endereco.cidade,
      UfEmissao: this.user.cauUf.endereco.uf,
      cpfCoordenador: null,
      nomeCoordenador: null,
      UfComissao: '',
      assinatura: {
        arquivo: null,
        nome: null
      }
    }

    if (this.membro.diploma) {
      this.initDiploma();
    }

    this.initDias();
    this.initMeses();
    this.initAnos();
    this.initAnoResolucao();
  }

  /**
   * Inicia dias do ano
   */
  public initDiploma(): void {
    this.diplomaEleitoralService.getById(this.membro.diploma).subscribe(
      data => {
        let dataRealizacao = data.diploma.dt_eleicao.split(' ');
        dataRealizacao = dataRealizacao[0].split('-');

        if (data.diploma.dt_eleicao_2) {
          let dataRealizacao2 = data.diploma.dt_eleicao_2.split(' ');
          dataRealizacao2 = dataRealizacao2[0].split('-');
          this.diploma.diaRealizacao2 = parseInt(dataRealizacao2[2]);
        }
      
        let dataResolucao = data.diploma.dt_resolucao.split(' ');
        dataResolucao = dataResolucao[0].split('-');

        let dataEmissao = data.diploma.dt_emissao.split(' ');
        dataEmissao = dataEmissao[0].split('-');

        this.diploma.diaRealizacao = parseInt(dataRealizacao[2]);
        this.diploma.mesRealizacao = parseInt(dataRealizacao[1]);
        this.diploma.anoRealizacao = parseInt(dataRealizacao[0]);        
        this.diploma.numeroResolucao = data.diploma.numero_resolucao;
        this.diploma.diaResolucao = parseInt(dataResolucao[2]);
        this.diploma.mesResolucao = parseInt(dataResolucao[1]);
        this.diploma.anoResolucao = parseInt(dataResolucao[0]);
        this.diploma.idConselheiro = data.diploma.conselheiro_id
        this.diploma.diaEmissao = parseInt(dataEmissao[2]);
        this.diploma.mesEmissao = parseInt(dataEmissao[1]);
        this.diploma.anoEmissao = parseInt(dataEmissao[0]);
        this.diploma.cidadeEmissao = data.diploma.cidade_emissao;
        this.diploma.UfEmissao = data.diploma.uf_emissao;
        this.diploma.cpfCoordenador = data.diploma.cpf_coordenador;
        this.diploma.nomeCoordenador = data.diploma.nome_coordenador;
        this.diploma.UfComissao = data.diploma.uf_comissao == 'BR' ? 'CAU/BR' : data.diploma.uf_comissao;
        this.diploma.assinatura.arquivo = data.assinatura.base64;
        this.diploma.assinatura.nome = data.assinatura.nome;    
      },
      error => {
        const message = error.message || error.description;
        this.messageService.addMsgDanger(message);
      }
    );
    
  }
  
  /**
   * Inicia dias do ano
   */
  public initDias(): void {
    let dia = 1;
    while (dia <= 31) {
      this.dias.push(dia);
      dia++;
    }
  }

  /**
   * Inicia os meses do ano
   */
  public initMeses(): void {
    this.meses = [
      { name:"Janeiro", index:1},
      { name:"Fevereiro", index:2},
      { name:"Março", index:3},
      { name:"Abril", index:4},
      { name:"Maio", index:5},
      { name:"Junho", index:6},
      { name:"Julho", index:7},
      { name:"Agosto", index:8},
      { name:"Setembro", index:9},
      { name:"Outubro", index:10},
      { name:"Novembro", index:11},
      { name:"Dezembro", index:12}
    ];
  }

  /**
   * Inicia anos de eleição
   */
  public initAnos(): void {
    this.calendarioClientService.getAnos().subscribe(data => {
        this.anos = data;
        this.initUfs();
    }, error => {
        this.messageService.addMsgDanger(error);
    })
  }

  /**
   * Inicia anos de eleição
   */
  public initAnoResolucao(): void {
    let ano = 2019;
    while (ano <= this.anoHoje) {
      this.anoResolucao.push(ano);
      ano++;
    }
  }

  /**
   * Inicia Unidades federativas 
   */
  public initUfs(): void {
    let filtroFilial = {
        tipoFilial: 7
      };
      this.ufService.getFilial(filtroFilial).subscribe(
        data => {
            this.ufs = data;
            this.initTexto();
        },
        error => {
          const message = error.message || error.description;
          this.messageService.addMsgDanger(message);
        }
      );
  }

  /**
   * Salva o diploma eleitoral 
   */
  public salvar(form: NgForm): void {
    this.submitted = true;
    if (!form.valid) {
      this.messageService.addMsgDanger('MSG_CAMPOS_OBRIGATORIOS');
      return;
    }

    let dados = {
      membro: this.membro,
      diploma: this.diploma,
      cen: this.securityService.hasRoles(Constants.ROLE_ACESSOR_CEN) ? true : false,
      ufLogado: !this.securityService.hasRoles(Constants.ROLE_ACESSOR_CEN) ? this.user.cauUf.endereco.uf : null
    }

    if (!this.membro.diploma) {
      this.create(dados);
      return;
    }

    this.update(dados);
  }

   /**
   * Cria um novo diploma
   */
  public create(dados): void {
    this.diplomaEleitoralService.create(dados).subscribe(
      data => {
        this.messageService.addMsgSuccess('LABEL_PROGRESSBAR_NIVEL_CADASTRO');
        this.imprimir(data.id);
      },
      error => {
        const message = error.message || error.description;
        this.messageService.addMsgDanger(message);
      }
    );  
  }

  /**
   * Cria um novo diploma
   */
  public update(dados): void {
    this.diplomaEleitoralService.update(dados, this.membro.diploma).subscribe(
      data => {
        this.messageService.addMsgSuccess('LABEL_PROGRESSBAR_NIVEL_CADASTRO');
        this.imprimir(this.membro.diploma);
      },
      error => {
        const message = error.message || error.description;
        this.messageService.addMsgDanger(message);
      }
    );  
  }

  /**
   * Imprimir Diploma Eleitoral
   */
  public imprimir(idDiploma): void {
    this.diplomaEleitoralService.imprimir(idDiploma).subscribe(
      data => {
        var file = new Blob([data.body], { type: 'application/pdf' });
        var fileURL = window.URL.createObjectURL(file);
        window.open(fileURL, '_blank');
        this.router.navigate(["/eleicao/listar-eleitos"]);
      },
      error => {
        const message = error.message || error.description;
        this.messageService.addMsgDanger(message);
      }
    );
  }

  /**
   * Retorna para página anterior
   */
  public cancelar(): void {
    this.router.navigate(["/eleicao/listar-eleitos"]);
  }

  /**
   * Retorna dados do coordenador
   */
  public buscarCoordenador(): void {
    if (this.diploma.cpfCoordenador) {
      this.profissionalClientService.getProfissional(this.diploma.cpfCoordenador).subscribe(
        data => {
          this.diploma.nomeCoordenador = data.nomeCompleto;
          this.diploma.UfComissao = data.pessoa.endereco.uf;
          this.atualizarTexto();
        },
        error => {
          const message = error.message || error.description;
          this.messageService.addMsgDanger(message);
        }
      );  
    }
  }

  /**
   * Método responsável por validar se cada arquivo submetido a upload
   * atende os critérios definidos para salvar os binários.
   *
   * @param arquivoEvent
   */
  public uploadDocumento (arquivoEvent: any) {
    const file: File = arquivoEvent;
    if (this.validateUpload(file)) {  
      if (this.isImage(file)) {
        const img = new Image;
        img.src = window.URL.createObjectURL(file);
        this.diploma.assinatura.image = img.src;
        this.diploma.assinatura.nome = file.name;
        this.loadFiles(file);
      }
    }
  }

  /**
   * Auxilia na validação dos arquivos associados ao e-mail
   * 
   * @param file File
   */
  public validateUpload(file: File): boolean {
    return this.validateFileTypeEmbed(file) &&
      this.validateFileSizeEmbed(file)
  }

  /**
   * Valida se o tipo de arquivo associado ao e-mail corresponde aos tipos permitidos de arquivo.
   * 
   * @param file File
   * @return boolean TRUE caso o formato exista na lista de formatos suportados.
   */
  public validateFileTypeEmbed(file: File): boolean {
    const supported = this.supportedTypes.find(element => element == file.type) != undefined;

    if (!supported) {
        this.messageService.addMsgDanger('MSG_ARQUIVO_NAO_SUPORTADO_NOTICIA');
        return false;
    }

    return true;
  }

  /**
   * Responsavel por validar se o tamanho do arquivo associado
   * esta dentro do limite maximo.
   * 
   * @param file File
   * @return TRUE caso o valor esteja dentro padrao.
   */
  public validateFileSizeEmbed(file: File): boolean {
    const supportedSize = (file.size / 1024) <= this.MAX_SIZE;

    if (!supportedSize) {
        this.messageService.addMsgDanger('MSG_ARQUIVO_TAMANHO_MAXIMO');
        return false;
    }
    
    return true;
  }

  /**
   * Responsavel por carregar o arquivo no objeto
   * @param file File
   */
  public loadFiles(file: File): void {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
      this.diploma.assinatura.arquivo = reader.result;
      this.atualizarTexto();
    };
  }

  /**
   * Verifica se o arquivo passado como argumento é uma imagem
   * dentro dos formatos validos.
   * 
   * @param file File
   */
  public isImage(file: File): boolean {
    const validImages = [
        'image/png',
        'image/jpeg'
    ];

    return validImages.find(image => image == file.type) != undefined;
  }

  /**
   * Busca texto parametrizado
   * 
   */
  public initTexto(): void {
    this.portalClientService.getDeclaracoesById(43).subscribe(
      data => {
        this.textoPadrao = data.textoInicial;
        this.atualizarTexto();
      },
      error => {
        const message = error.message || error.description;
        this.messageService.addMsgDanger(message);
      }
    );      
  }

  /**
   * Atualiza dados do texto parametrizado
   * 
   */
  public atualizarTexto(): void {
    this.textoDiploma = this.textoPadrao;
    this.textoDiploma = this.textoDiploma.replace('{Nacional ou Unidade da Federação}', this.diploma.UfComissao != '' ?
      this.getComissaoEleitoral(this.diploma) : '{Nacional ou Unidade da Federação}');
    this.textoDiploma = this.textoDiploma.replace('{CEN ou CE-UF}', this.diploma.UfComissao != '' ? 
      this.getCEN() : '{CEN ou CE-UF}');
    this.textoDiploma = this.textoDiploma.replace('{nº}', this.diploma.numeroResolucao ? this.diploma.numeroResolucao : '{nº}');
    this.textoDiploma = this.textoDiploma.replace('{dia}', this.diploma.diaResolucao ? this.diploma.diaResolucao : '{dia}');
    this.textoDiploma = this.textoDiploma.replace('{mês}', this.diploma.mesResolucao ? this.getMes(this.diploma.mesResolucao) : '{mês}');
    this.textoDiploma = this.textoDiploma.replace('{aaaa}', this.diploma.anoResolucao ? this.diploma.anoResolucao : '{aaaa}');
    this.textoDiploma = this.textoDiploma.replace('{NOME}', this.diploma.nomeConselheiro);
    this.textoDiploma = this.textoDiploma.replace('{Cidade}', this.diploma.cidadeEmissao ? this.diploma.cidadeEmissao : '{Cidade}');
    this.textoDiploma = this.textoDiploma.replace('{UF}', this.diploma.UfEmissao != '' ? this.diploma.UfEmissao : '{UF}');
    this.textoDiploma = this.textoDiploma.replace('{tipoConselheiro}', this.diploma.tipoConselheiro != '' ? this.getTipoConselheiro() : '{tipoConselheiro}');
    this.textoDiploma = this.textoDiploma.replace('{diaRealizacao}', this.getDiaRealizacao());
    this.textoDiploma = this.textoDiploma.replace('{mesRealizacao}', this.diploma.mesRealizacao ? this.getMes(this.diploma.mesRealizacao) : '{mesRealizacao}');
    this.textoDiploma = this.textoDiploma.replace('{anoRealizacao}', this.diploma.anoRealizacao ? this.diploma.anoRealizacao : '{anoRealizacao}');
    this.textoDiploma = this.textoDiploma.replace('{diaEmissao}', this.diploma.diaEmissao ? this.diploma.diaEmissao : '{diaEmissao}');
    this.textoDiploma = this.textoDiploma.replace('{mesEmissao}', this.diploma.mesEmissao ? this.getMes(this.diploma.mesEmissao) : '{mesEmissao}');
    this.textoDiploma = this.textoDiploma.replace('{anoEmissao}', this.diploma.anoEmissao ? this.diploma.anoEmissao : '{anoEmissao}');
    this.textoDiploma = this.textoDiploma.replace('{localConselho}', this.diploma.UfComissao || this.membro.idRepresentacao == 1 || this.membro.idRepresentacao == 3 ?
      this.getLocalConselho() : '{localConselho}');
  }

   /**
   * Busca dia da Eleição parametrizado
   */
  public getDiaRealizacao(): string {
    let dia1 = this.diploma.diaRealizacao.toString();
    let dia2 = this.diploma.diaRealizacao2.toString();

    let dia = dia1 && dia2 ? dia1.concat(' e ').concat(dia2) : 
      (dia1 ? dia1 : '{diaRealizacao}');
    return dia;
  }

   /**
   * Busca dados do local do conselho
   * 
   */
   public getLocalConselho(): string {
    if (this.membro.idRepresentacao == 1 || this.membro.idRepresentacao == 3) {
      return 'do Brasil';
    }
    return this.getComissaoEleitoral(this.diploma);
  }

  /**
   * Busca dados da comissão eleitoral
   * 
   */
  public getComissaoEleitoral(diploma: any): string {
    let ufs = [
      { sigla: 'N', descricao: 'Nacional' },
      { sigla: 'BR', descricao:'Brasil' },
      { sigla: 'AC', descricao:'Acre' },
      { sigla: 'AL', descricao:'Alagoas' },
      { sigla: 'AP', descricao:'Amapá' },
      { sigla: 'AM', descricao:'Amazonas' },
      { sigla: 'BA', descricao:'Bahia' },
      { sigla: 'CE', descricao:'Ceará' },
      { sigla: 'DF', descricao:'Distrito Federal' },
      { sigla: 'ES', descricao:'Espírito Santo' },
      { sigla: 'GO', descricao:'Goiás' },
      { sigla: 'MA', descricao:'Maranhão' },
      { sigla: 'MT', descricao:'Mato Grosso' },
      { sigla: 'MS', descricao:'Mato Grosso do Sul' },
      { sigla: 'MG', descricao:'Minas Gerais' },
      { sigla: 'PA', descricao:'Pará' },
      { sigla: 'PB', descricao:'Paraíba' },
      { sigla: 'PR', descricao:'Paraná' },
      { sigla: 'PE', descricao:'Pernambuco' },
      { sigla: 'PI', descricao:'Piauí' },
      { sigla: 'RJ', descricao:'Rio de Janeiro' },
      { sigla: 'RN', descricao:'Rio Grande do Norte' },
      { sigla: 'RS', descricao:'Rio Grande do Sul' },
      { sigla: 'RO', descricao:'Rondônia' },
      { sigla: 'RR', descricao:'Roraima' },
      { sigla: 'SC', descricao:'Santa Catarina' },
      { sigla: 'SP', descricao:'São Paulo' },
      { sigla: 'SE', descricao:'Sergipe' },
      { sigla: 'TO', descricao:'Tocantins' },
      { sigla: 'CAU/BR', descricao:'Nacional' }
    ];
    let estado = ufs.find((uf) => uf.sigla === diploma.UfComissao);
    
    let preposicao = 'de ';
    let arrayEstados = ['BR','AC', 'AP', 'AM', 'CE', 'DF', 'ES', 'MA', 'PA', 'PR', 'PI', 'RJ', 'RN', 'RS', 'TO'];
    if (arrayEstados.find((element) => element == estado.sigla)) {
      preposicao = 'do';
    }
   
    arrayEstados = ['BA', 'PB'];
    if (arrayEstados.find((element) => element == estado.sigla)) {
      preposicao = 'da';
    }
    
    arrayEstados = ['CAU/BR'];
    if (arrayEstados.find((element) => element == estado.sigla)) {
      preposicao = '';
    }

    return preposicao + ' ' + estado.descricao;
  }

  /**
   * Busca o CEN ou CE
   * 
   */
  public getCEN(): string {
    return this.diploma.UfComissao == 'BR' || this.diploma.UfComissao == 'CAU/BR' ? 'CEN-CAU/BR' : 'CE-' + this.diploma.UfComissao;
  }

  /**
   * Busca o mês por numero
   * 
   */
  public getMes(mes): string {

		let meses = [
      { index: '1', mes: 'Janeiro' },
      { index: '2', mes: 'Fevereiro' },
      { index: '3', mes: 'Março' },
      { index: '4', mes: 'Abril' },
      { index: '5', mes: 'Maio' },
      { index: '6', mes: 'Junho' },
      { index: '7', mes: 'Julho' },
      { index: '8', mes: 'Agosto' },
      { index: '9', mes: 'Setembro' },
      { index: '10', mes: 'Outubro' },
      { index: '11', mes: 'Novembro' },
      { index: '12', mes: 'Dezembro' }
    ]
    let result = meses.find((element) => parseInt(element.index) === parseInt(mes));

		return result.mes;
  }

  /**
   * Busca o tipo de Conselheiro
   * 
   */
  public getTipoConselheiro(): string {
    return this.diploma.tipoConselheiro == 1 ? 'Titular' : 'Suplente';
  }
}
