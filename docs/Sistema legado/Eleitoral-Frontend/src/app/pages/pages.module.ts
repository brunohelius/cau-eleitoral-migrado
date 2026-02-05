import { NgModule, Inject } from '@angular/core';
import { MessageModule } from '@cau/message';
import { CommonModule } from '@angular/common';
import { BsDatepickerModule } from 'ngx-bootstrap';
import { FlexLayoutModule } from '@angular/flex-layout';

import { PagesRoutingModule } from './pages-routing.module';
import { SecurityService, IConfig, config, AuthService, MenuService } from '@cau/security';
import { Router } from '@angular/router';

/**
 * @author Squadra Tecnologia
 */
@NgModule({
  imports: [
    CommonModule,
    MessageModule,
    FlexLayoutModule,
    PagesRoutingModule,
    BsDatepickerModule
  ]
})
export class PagesModule {
  constructor(
    private securityService: SecurityService,
    private router: Router,
    @Inject(config) private config: IConfig,
  ) {
    this.securityService.onForbidden.subscribe(() => {
      this.router.navigate([this.config.rootRoute]);
    });

    this.securityService.onUnauthorized.subscribe(() => {
      window.location.href = this.config.urlLogout;
      this.securityService.invalidate();
    });
  }
}
