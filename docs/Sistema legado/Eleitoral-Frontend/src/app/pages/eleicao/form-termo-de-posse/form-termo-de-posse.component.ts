import { Component, OnInit } from '@angular/core';
import * as _ from "lodash";
import { MessageService } from '@cau/message';
import { NgForm } from '@angular/forms';
import { SecurityService } from '@cau/security';
import { Router} from "@angular/router";
import { LayoutsService } from '@cau/layout';

import { CalendarioClientService } from '../../../client/calendario-client/calendario-client.service';
import { UFClientService } from '../../../client/uf-client/uf-client.service';
import { TermoDePosseService } from '../../../client/termo-de-posse-client/termo-de-posse-client.service';
import { ProfissionalClientService } from '../../../client/profissional-client/profissional-client.service';
import { MembroChapaService } from '../../../client/membro-chapa-client/membro-chapa.service';
import { Constants } from 'src/app/constants.service';
import { PortalClientService } from '../../../client/portal-client/portal-client.service';

@Component({
  selector: 'app-termo-de-posse-eleitoral',
  templateUrl: './form-termo-de-posse.component.html',
  styleUrls: ['./form-termo-de-posse.component.scss']
})
export class FormTermoDePosseComponent implements OnInit {
  public termo: any;

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

  public textoTermo: any = '';
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
    private TermoDePosseService: TermoDePosseService,
    private profissionalClientService: ProfissionalClientService,
    private securityService: SecurityService,
    private layoutsService: LayoutsService,
    private membroChapaService: MembroChapaService,
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
          'LABEL_EMITIR_TERMO'
      ),
    });

    this.user = this.securityService.credential["_user"];
    this.termo = {
      diaEmissao: 15,
      mesEmissao: 12,
      anoEmissao: 2023,
      preposicao: '',
      cidadeEmissao: this.user.cauUf.endereco.cidade,
      UfEmissao: this.user.cauUf.endereco.uf,
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
      idConselheiro: this.membro.idConselheiro,
      cpfPresidente: null,
      nomePresidente: null,
      ufPresidente: '',
      assinatura: {
        image: null,
        nome: null
      }
    }

    if (this.securityService.hasRoles(Constants.ROLE_ACESSOR_CE) && !this.membro.termo) {
      this.initPresidente();
    } else  if (this.membro.termo) {
      this.initTermo();
    }
    
    this.initDias();
    this.initMeses();
    this.initAnos();
    this.initAnoResolucao();
  }

  /**
   * Busca o Presidente da chapa
   */
  public initPresidente(): void {
    let filtro = {
      ano: this.membro.ano,
      filial:this.membro.uf
    };

    this.membroChapaService.getPresidenteUf(filtro).subscribe(
      data => {
        this.termo.cpfPresidente = data.cpf;
        this.termo.nomePresidente = data.nome;
        this.termo.ufPresidente = data.uf;
        if (this.membro.termo) {
          this.initTermo();
        }
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
  public initTermo(): void {
    this.TermoDePosseService.getById(this.membro.termo).subscribe(
      data => {
        let dataRealizacao = data.termo.dt_eleicao.split(' ');
        dataRealizacao = dataRealizacao[0].split('-');

        let dataResolucao = data.termo.dt_resolucao.split(' ');
        dataResolucao = dataResolucao[0].split('-');

        let dataEmissao = data.termo.dt_emissao.split(' ');
        dataEmissao = dataEmissao[0].split('-');

        if (data.termo.dt_eleicao_2) {
          let dataRealizacao2 = data.termo.dt_eleicao_2.split(' ');
          dataRealizacao2 = dataRealizacao2[0].split('-');
          this.termo.diaRealizacao2 = parseInt(dataRealizacao2[2]);
        }

        this.termo.diaRealizacao = parseInt(dataRealizacao[2]);
        this.termo.mesRealizacao = parseInt(dataRealizacao[1]);
        this.termo.anoRealizacao = parseInt(dataRealizacao[0]);
        this.termo.numeroResolucao = data.termo.numero_resolucao;
        this.termo.diaResolucao = parseInt(dataResolucao[2]);
        this.termo.mesResolucao = parseInt(dataResolucao[1]);
        this.termo.anoResolucao = parseInt(dataResolucao[0]);
        this.termo.idConselheiro = data.termo.conselheiro_id
        this.termo.diaEmissao = parseInt(dataEmissao[2]);
        this.termo.mesEmissao = parseInt(dataEmissao[1]);
        this.termo.anoEmissao = parseInt(dataEmissao[0]);
        this.termo.cidadeEmissao = data.termo.cidade_emissao;
        this.termo.UfEmissao = data.termo.uf_emissao;
        this.termo.cpfPresidente = data.termo.cpf_presidente,
        this.termo.nomePresidente = data.termo.nome_presidente,
        this.termo.ufPresidente = data.termo.uf_presidente,
        this.termo.preposicao = data.termo.preposicao_cidade
        this.termo.assinatura.arquivo = data.assinatura.base64;
        this.termo.assinatura.nome = data.assinatura.nome;   
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
    while (ano <= this.anoHoje + 3) {
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
   * Salva o Termo de posse
   */
  public salvar(form: NgForm): void {
    this.submitted = true;
    if (!form.valid) {
      this.messageService.addMsgDanger('MSG_CAMPOS_OBRIGATORIOS');
      return;
    }

    let dados = {
      membro: this.membro,
      termo: this.termo,
      cen: this.securityService.hasRoles(Constants.ROLE_ACESSOR_CEN) ? true : false,
      ufLogado: !this.securityService.hasRoles(Constants.ROLE_ACESSOR_CEN) ? this.user.cauUf.endereco.uf : null
    }

    if (!this.membro.termo) {
      this.create(dados);
      return;
    }

    this.update(dados);
  }

   /**
   * Cria um novo Termo
   */
  public create(dados): void {
    this.TermoDePosseService.create(dados).subscribe(
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
   * Cria um novo Termo
   */
  public update(dados): void {
    this.TermoDePosseService.update(dados, this.membro.termo).subscribe(
      data => {
        this.messageService.addMsgSuccess('LABEL_PROGRESSBAR_NIVEL_CADASTRO');
        this.imprimir(this.membro.termo);
      },
      error => {
        const message = error.message || error.description;
        this.messageService.addMsgDanger(message);
      }
    );  
  }

  /**
   * Imprimir Termo de posse
   */
  public imprimir(idTermo): void {
    this.TermoDePosseService.imprimir(idTermo).subscribe(
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
   * Retorna dados do presidente
   */
  public buscarPresidente(): void {
    if (this.termo.cpfPresidente) {
      this.profissionalClientService.getProfissional(this.termo.cpfPresidente).subscribe(
        data => {
          this.termo.nomePresidente = data.nomeCompleto;
          this.termo.ufPresidente = data.pessoa.endereco.uf;
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
        this.termo.assinatura.image = img.src;
        this.termo.assinatura.nome = file.name;
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
      this.termo.assinatura.arquivo = reader.result;
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
    this.portalClientService.getDeclaracoesById(42).subscribe(
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
    this.textoTermo = this.textoPadrao;
    this.textoTermo = this.textoTermo.replaceAll('{dia}', this.termo.diaEmissao ? this.termo.diaEmissao : '{dia}');
    this.textoTermo = this.textoTermo.replaceAll('{mes}', this.termo.mesEmissao ? this.getMes(this.termo.mesEmissao) : '{mes}');
    this.textoTermo = this.textoTermo.replaceAll('{ano}', this.termo.anoEmissao ? this.termo.anoEmissao : '{ano}');
    this.textoTermo = this.textoTermo.replace('{de}', this.termo.preposicao ? this.termo.preposicao : '{de}');
    this.textoTermo = this.textoTermo.replaceAll('{Cidade}', this.termo.cidadeEmissao ? this.termo.cidadeEmissao : '{Cidade}');
    this.textoTermo = this.textoTermo.replaceAll('{UF}', this.termo.UfEmissao != '' ? this.termo.UfEmissao : '{UF}');
    this.textoTermo = this.textoTermo.replace('{diaEleicao}', this.termo.diaEleicao ? this.termo.diaEleicao : '{diaEleicao}');
    this.textoTermo = this.textoTermo.replace('{nº}', this.termo.numeroResolucao ? this.termo.numeroResolucao : '{nº}');
    this.textoTermo = this.textoTermo.replace('{diaResolucao}', this.termo.diaResolucao ? this.termo.diaResolucao : '{diaResolucao}');
    this.textoTermo = this.textoTermo.replace('{mêsResolucao}', this.termo.mesResolucao ? this.getMes(this.termo.mesResolucao) : '{mêsResolucao}');
    this.textoTermo = this.textoTermo.replace('{aaaaResolucao}', this.termo.anoResolucao ? this.termo.anoResolucao : '{aaaaResolucao}');
    this.textoTermo = this.textoTermo.replaceAll('{NOME}', this.termo.nomeConselheiro);
    this.textoTermo = this.textoTermo.replace('{tipoConselheiro}', this.termo.tipoConselheiro != '' ? this.getTipoConselheiro() : '{tipoConselheiro}');
    this.textoTermo = this.textoTermo.replace('{diaRealizacao}', this.getDiaRealizacao());
    this.textoTermo = this.textoTermo.replace('{mesRealizacao}', this.termo.mesRealizacao ? this.getMes(this.termo.mesRealizacao) : '{mesRealizacao}');
    this.textoTermo = this.textoTermo.replace('{anoRealizacao}', this.termo.anoRealizacao ? this.termo.anoRealizacao : '{anoRealizacao}');
    this.textoTermo = this.textoTermo.replaceAll('{localConselheiro}', this.getLocalConselheiro());
    this.textoTermo = this.textoTermo.replaceAll('{ufConselheiro}', this.getComissaoEleitoral());
    this.textoTermo = this.textoTermo.replace('{localCandidato}', this.getLocalCandidato());
  }

  /**
   * Busca dia da Eleição parametrizado
   */
  public getDiaRealizacao(): string {
    let dia1 = this.termo.diaRealizacao.toString();
    let dia2 = this.termo.diaRealizacao2.toString();

    let dia = dia1 && dia2 ? dia1.concat(' e ').concat(dia2) : 
      (dia1 ? dia1 : '{diaRealizacao}');
    return dia;
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
    return this.termo.tipoConselheiro == 1 ? 'Titular' : 'Suplente';
  }

  /**
   * Busca dados da comissão eleitoral
   * 
   */
  public getLocalConselheiro(): string {
    if (this.membro.idRepresentacao == 1 || this.membro.idRepresentacao == 3) {
      return 'do Brasil';
    }
    return this.getComissaoEleitoral();
  }

  /**
     * Busca dados da comissão eleitoral
     * 
     */
  public getLocalCandidato(): string {
    if (this.membro.idRepresentacao == 3) {
      return 'Nacional';
    }
    return this.getComissaoEleitoral();
  }

  /**
   * Busca dados da comissão eleitoral
   * 
   */
  public getComissaoEleitoral(): string {
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

    let estado = ufs.find((uf) => uf.sigla === this.membro.uf);
    
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
}
