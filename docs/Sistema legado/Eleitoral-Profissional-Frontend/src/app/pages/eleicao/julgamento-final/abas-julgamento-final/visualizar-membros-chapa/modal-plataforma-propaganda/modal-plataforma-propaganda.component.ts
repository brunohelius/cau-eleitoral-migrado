import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { ActivatedRoute, Router } from '@angular/router';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, Input, TemplateRef } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { UtilsService } from 'src/app/utils.service';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'app-modal-plataforma-propaganda',
    templateUrl: './modal-plataforma-propaganda.component.html',
    styleUrls: ['./modal-plataforma-propaganda.component.scss']
})
export class ModalPlataformaPropagandaComponent implements OnInit {

    @Input() chapaEleicao: any;

    public redesSociaisChapaFixas: any;
    public redesSociaisChapaOutras: any;
    public redesSociaisChapaOutrasAtiva: any;

    public configuracaoCkeditor: any = {};

    /**
     * Construtor da classe.
     */
    constructor(
        private router: Router,
        public modalRef: BsModalRef,
        private route: ActivatedRoute,
        private messageService: MessageService,
    ) {
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.inicializarRedesSociais();
        this.inicializaConfiguracaoCkeditor();
    }

    /**
   * Inicializa a configuração do ckeditor.
   */
    private inicializaConfiguracaoCkeditor(): void {
        this.configuracaoCkeditor = UtilsService.getConfiguracaoPadraoCKeditor();
    }

    /**
   * Inicializa as variáveis referentes a rede social.
   */
    private inicializarRedesSociais(): void {

        this.redesSociaisChapaFixas = [
            { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_FACEBOOK }, icone: 'fa fa-facebook-official fa-2x', descricao: '', isAtivo: false },
            { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_INSTAGRAM }, icone: 'fa fa-instagram fa-2x', descricao: '', isAtivo: false },
            { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_LINKEDIN }, icone: 'fa fa-linkedin fa-2x', descricao: '', isAtivo: false },
            { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_TWITTER }, icone: 'fa fa-twitter fa-2x', descricao: '', isAtivo: false }
        ];

        this.redesSociaisChapaOutras = [];
        this.redesSociaisChapaOutrasAtiva = false;

        this.chapaEleicao.redesSociaisChapa.forEach((redeSocialChapa) => {

            if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_FACEBOOK) {
                this.redesSociaisChapaFixas[0].isAtivo = redeSocialChapa.isAtivo;
                this.redesSociaisChapaFixas[0].descricao = redeSocialChapa.descricao;
            }

            if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_INSTAGRAM) {
                this.redesSociaisChapaFixas[1].isAtivo = redeSocialChapa.isAtivo;
                this.redesSociaisChapaFixas[1].descricao = redeSocialChapa.descricao;
            }

            if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_LINKEDIN) {
                this.redesSociaisChapaFixas[2].isAtivo = redeSocialChapa.isAtivo;
                this.redesSociaisChapaFixas[2].descricao = redeSocialChapa.descricao;
            }

            if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_TWITTER) {
                this.redesSociaisChapaFixas[3].isAtivo = redeSocialChapa.isAtivo;
                this.redesSociaisChapaFixas[3].descricao = redeSocialChapa.descricao;
            }

            if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_OUTROS) {
                this.redesSociaisChapaOutrasAtiva = redeSocialChapa.isAtivo;
                this.redesSociaisChapaOutras.push({
                    tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS },
                    descricao: redeSocialChapa.descricao,
                    isAtivo: redeSocialChapa.isAtivo
                });
            }
        });

        if (this.redesSociaisChapaOutras.length === 0) {
            this.redesSociaisChapaOutras = [
                { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS }, descricao: '' }
            ];
        } else {
            this.redesSociaisChapaOutrasAtiva = this.redesSociaisChapaOutras[0].isAtivo;
        }
    }

    /**
   * Verifica se o campo descrição do rede social está visível.
   * @param redeSocial
   */
    public isDescricaoRedeSocialVisivel(redeSocial: any): boolean {
        return redeSocial.descricao.trim() || redeSocial.isAtivo;
    }

    /**
   * Verifica se o campo rede social outro está visível.
   * @param redesSociaisChapaOutras
   */
    public isRedesSociaisChapaOutrasVisivel(redesSociaisChapaOutras: Array<any>): boolean {
        let isOutrosVazio = false;

        if (redesSociaisChapaOutras) {
            for (let i = 0; i < redesSociaisChapaOutras.length; i++) {
                if (redesSociaisChapaOutras[i].descricao != '' && redesSociaisChapaOutras[i].descricao != undefined) {
                    isOutrosVazio = true;
                }
            }
        }

        return (
            this.redesSociaisChapaOutrasAtiva ||
            (redesSociaisChapaOutras && redesSociaisChapaOutras.length > 1 && isOutrosVazio) ||
            (redesSociaisChapaOutras && redesSociaisChapaOutras.length > 1 && redesSociaisChapaOutras[0].descricao.trim())
        );
    }

    /**
     * Válida se a plataforma eleitoral é uma 'UF-BR'.
     */
    public isPlataformaEleitoralUfBr(): boolean {
        return this.chapaEleicao.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_UF_BR;
    }

    /**
     * Válida se a plataforma eleitoral é uma 'IES'.
     */
    public isPlataformaEleitoralIes(): boolean {
        return this.chapaEleicao.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_IES;
    }
}