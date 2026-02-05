import { SecurityService, IConfig, config, MenuService, NotificationService } from '@cau/security';
import { Component, OnInit, Inject } from '@angular/core';
import { LayoutsService, Menu, User } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Router } from '@angular/router';
import { Subscriber } from 'rxjs';
import { environment } from 'src/environments/environment';

/**
 * App Component
 * 
 * @author Squadra Tecnologia
 */
@Component({
  selector: 'app-root',
  template: `<alert></alert>
             <loader></loader>
             <confirm></confirm>
             <router-outlet></router-outlet>`
})
export class AppComponent implements OnInit {

  /**
   * Construtor da classe.
   * 
   * @param router 
   * @param menuService 
   * @param notificationService 
   * @param layoutsService 
   * @param config 
   * @param securityService 
   * @param messageService 
   */
  constructor(
    private router: Router,
    private menuService: MenuService,
    private notificationService: NotificationService,
    private layoutsService: LayoutsService,
    @Inject(config) private config: IConfig,
    private securityService: SecurityService,
    private messageService: MessageService
  ) { }

  /**
   * Inicializa as dependências do Component.
   * 
   */
  ngOnInit(): void {
    this.layoutsService.onLoadUser.subscribe((observable: Subscriber<User>) => {
      if (this.securityService.credential.user) {
        const user: User = {
          avatar: this.securityService.credential.user.avatar,
          name: this.securityService.credential.user.name,
          title: this.securityService.credential.user.title,
          nuNotification: this.securityService.credential.user.nuNotification,
          profile: this.securityService.credential.user.profile,
        };
        observable.next(user);
        observable.complete();
      }
    });

    this.layoutsService.onLogout.subscribe(() => {
      this.messageService.addConfirmYesNo('MSG_DESEJA_SAIR_SISTEMA', () => {
        window.location.href = this.config.urlLogout;
        this.securityService.invalidate();
      });
    });

    this.layoutsService.onLoadMenu.subscribe((observable: Subscriber<Array<Menu>>) => {
      if (this.securityService.credential.user) {
        this.menuService.getMenus().subscribe(data => {
          this.atualizarUrls(data);
          observable.next(data);
          observable.complete();
        }, error => {
          this.hideNav();
          observable.next();
          observable.complete();
        });
      } else {
        this.hideNav();
      }
    });

    this.layoutsService.onLoadNotification.subscribe((observable: Subscriber<Array<Notification>>) => {
      this.notificationService.getNotifications().subscribe(data => {
        observable.next(data);
        observable.complete();
      }, error => {
        observable.next();
        observable.complete();
      });
    });

    this.securityService.onForbidden.subscribe(() => {
      this.router.navigate([this.config.rootRoute]);
    });

    this.securityService.onUnauthorized.subscribe(() => {
      window.location.href = this.config.urlLogout;
      this.securityService.invalidate();
    });
  }

  /**
  * Atualiza as URLs conforme o endereço da aplicação. 
  * 
  * @param menus 
  */
  public atualizarUrls(menus): void {
    if (!menus || menus.length == 0) {
      return;
    }

    menus.forEach(menu => {
      if (menu.url) {
        let url = menu.url.split('://');

        if (url[0] && url[0] == 'eleitoral' && environment.urlLocal == 'http://localhost:4200') {
          menu.url = environment.urlLocal + '/' + url[1];
        }
      }

      this.atualizarUrls(menu.itens);
    });
  }

  /**
   * Retorna a URL base da aplicação conforme o módulo informado.
   * 
   * @param idModulo 
   */
  public getUrlBase(idModulo: any): string {
    let urlBase = '';

    if (idModulo) {
      let urlsModulo = [];

      let modulosMenu: any = urlsModulo.filter((urlModulo: any) => {
        return urlModulo.id == idModulo;
      });

      if (modulosMenu && modulosMenu.length > 0) {
        urlBase = modulosMenu[0].url;
      }

      return urlBase;
    }
  }

  /**
   * Oculta as áreas de navegação
   * 
   * @param modulo 
   */
  private hideNav() {
    this.layoutsService.showNavLeft = false;
    this.layoutsService.showNavTop = false;
    this.layoutsService.isContentFullPage = true;
  }
}
