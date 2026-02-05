import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DocumentoComissaoMembroClientResolve } from './documento-comissao-membro-client.resolve';
import { DocumentoComissaoMembroClientService } from './documento-comissao-membro-client.service';

@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    DocumentoComissaoMembroClientService,
    DocumentoComissaoMembroClientResolve
  ]
})
export class DocumentoComissaoMembroClientModule { }
