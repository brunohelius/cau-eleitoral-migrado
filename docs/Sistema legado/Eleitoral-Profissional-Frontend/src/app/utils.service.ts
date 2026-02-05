import * as $ from 'jquery';
import { Injectable } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

/**
 * Serviço para realizar ações de utilidades diversas
 */
@Injectable({
    providedIn: 'root'  // <--provides this service in the root ModuleInjector
})
export abstract class UtilsService {

    /**
     * Converte string base64 para file objeto.
     *
     * @param dataURI
     */
    public static base64toFile(dataURI, name: string) {
        if (dataURI) {
            // convert base64/URLEncoded data component to raw binary data held in a string
            var byteString;
            if (dataURI.split(',')[0].indexOf('base64') >= 0) {
                byteString = atob(dataURI.split(',')[1]);
            } else {
                byteString = unescape(dataURI.split(',')[1]);
            }

            // separate out the mime component
            var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

            // write the bytes of the string to a typed array
            var ia = new Uint8Array(byteString.length);
            for (var i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }

            return new File([new Blob([ia], { type: mimeString })], name);
        }
    }

    /**
     * Rertorna a configuração do Ckeditor padrão
     */
    public static getConfiguracaoPadraoCKeditor(): any {
        return {
            toolbar: [
                { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
                { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', groups: ['find', 'selection', 'spellchecker'], items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
                { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] }
            ]
        }
    }


    /**
     * Retorna um valor de parâmetro passado na rota (data)
     * @param nameParam
     */
    public static getValorParamDoRoute(nameParam, route: ActivatedRoute) {
        let data = route.snapshot.data;
        let valor = undefined;

        for (let index of Object.keys(data)) {
            let param = data[index];

            if (param !== null && typeof param === 'object' && param[nameParam] !== undefined) {
                valor = param[nameParam];
                break;
            }
        }
        return valor;
    }

}

