
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DocumentosPublicadosResolve } from './documentos-publicados.resolve';
import { DocumentoEleicaoClientService } from './documento-eleicao-client.service';

@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    DocumentosPublicadosResolve,
    DocumentoEleicaoClientService,
  ]
})
export class DocumentoEleicaoClientModule { }
