import ptBr from '@angular/common/locales/pt';
import { BsDatepickerModule, defineLocale, ptBrLocale, BsLocaleService, AccordionModule } from 'ngx-bootstrap';
import { NgModule, LOCALE_ID } from '@angular/core';
import { registerLocaleData } from '@angular/common';
import { BrowserModule } from '@angular/platform-browser';
import { LayoutsModule, GerarCookieModule } from '@cau/layout';
import { SecurityModule, SecurityInterceptor } from '@cau/security';
import { MessageModule, MessageResourceProvider } from '@cau/message';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { CalendarModule, ValidationResourceProvider, ValidationModule, LoaderModule, CALENDAR_PROVIDER } from '@cau/component';

import { AppMessage } from './app.message';
import { AppComponent } from './app.component';
import { PopoverModule } from 'ngx-bootstrap/popover';
import { AppRoutingModule } from './app-routing.module';
import { environment } from '../environments/environment';

/**
 * Init Locale Date.
 */
defineLocale('pt', ptBrLocale);
registerLocaleData(ptBr, 'pt-BR');

/**
 * Modulo principal da aplicação.
 * 
 * @author Squadra Tecnologia
 */
@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    LoaderModule,
    BrowserModule,
    CalendarModule,
    AppRoutingModule,
    HttpClientModule,
    ValidationModule,
    GerarCookieModule.forRoot({
      cookieName: environment.cookieName
    }),
    LayoutsModule.forRoot(),
    MessageModule.forRoot(),
    BrowserAnimationsModule,
    PopoverModule.forRoot(),
    AccordionModule.forRoot(),
    BsDatepickerModule.forRoot(),
    SecurityModule.forRoot({
      rootRoute: '/',
      storage: 'eleitoralStorage',
      urlLogout: environment.urlLogout,
      url: environment.urlApiSecurity,
      cookieName: environment.cookieName,
      cookieAuth: environment.cookieAuth,
    }),
],
  providers: [
    BsLocaleService,
    {
      provide: HTTP_INTERCEPTORS,
      useClass: SecurityInterceptor,
      multi: true
    },
    {
      provide: ValidationResourceProvider,
      useValue: AppMessage,
    },
    {
      provide: MessageResourceProvider,
      useValue: AppMessage,
    },
    {
      provide: LOCALE_ID,
      useValue: 'pt-BR',
    }
  ],
  bootstrap: [AppComponent]
})

export class AppModule {

 }
