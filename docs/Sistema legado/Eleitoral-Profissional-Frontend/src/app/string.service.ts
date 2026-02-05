import * as $ from 'jquery';
import { Injectable } from '@angular/core';

/**
 * Serviço para realizar ações com strings
 */
@Injectable({
    providedIn: 'root'  // <--provides this service in the root ModuleInjector
})
export abstract class StringService {
    /**
   * Remove caracters especial de uma string
   * @param str 
   */
    public static replaceSpecialChars(str: string): string {
        return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remove acentos
            .replace(/([^\w]+|\s+)/g, ' ') // Substitui espaço e outros caracteres por hífen
            .replace(/\-\-+/g, '-')	// Substitui multiplos hífens por um único hífen
            .replace(/(^-+|-+$)/, ''); // Remove hífens extras do final ou do inicio da string
    }

    /**
     * Recupera o texto puro de um texto com html
     * @param stringHtml 
     */
    public static getPlainText(stringHtml: any): string {
        return $('<div>'+stringHtml+'</div>').text();
    }

    /**
     * Verifica se a quantidade de caracters e valida baseada no limite
     * @param text 
     * @param limit 
     */
    public static isLimitValid(text: string, limit: number): boolean {
        return !(this.getPlainText(text).length >= limit);
    }

    /**
     * Não permite que o valor da string seja zero
     * @param str 
     */
    public static replaceZeroChars(str: string): string {
        if (str === "0") {
            str = "";
        }
        return str;
    }

    /**
     * Substituie caracters especiais e remove um caracter zero
     * @param str 
     */
    public static replaceZeroSpecialChars(str: string): string {
        return this.replaceSpecialChars(this.replaceZeroChars(str));
    }

     /**
     * Verifica se a tecla pressionada ira produzir texto.
     * 
     * @param code 
     */
    public static isTextualCaracter(code: number): boolean{        
        return code != 8;        
    }

    /**
     * Adiciona mascara no registro do profissional
     * @param str 
     */
    public static maskRegistroProfissional(str: string): string {

        let registroFormatado = '';
        
        if (str && str !== undefined) {
            let tamanho = str.length;
            registroFormatado = str.substr(0, tamanho - 1) + "-" + str.substr(tamanho - 1);
            registroFormatado = registroFormatado.replace(/^0+/, "")
        }

        return registroFormatado;
    }
}