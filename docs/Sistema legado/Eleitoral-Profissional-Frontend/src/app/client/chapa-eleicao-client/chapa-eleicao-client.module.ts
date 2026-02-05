import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ChapaEleicaoClientService } from './chapa-eleicao-client.service';
import { ChapaEleicaoVigenteClientResolve } from './chapa-eleicao-vigente-client.resolve';
import { ChapaEleicaoUfResponsavelClientResolve } from './chapa-eleicao-uf-responsavel-client.resolve';
import { ChapaEleicaoAcompanharClientResolve } from './chapa-eleicao-acompanhar-client.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a chapa eleção.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    ChapaEleicaoClientService,
    ChapaEleicaoVigenteClientResolve,
    ChapaEleicaoUfResponsavelClientResolve,
    ChapaEleicaoAcompanharClientResolve
  ]
})
export class ChapaEleicaoClientModule { }
