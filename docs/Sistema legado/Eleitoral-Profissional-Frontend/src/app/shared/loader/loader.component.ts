import { Component } from '@angular/core';
import { LoaderService } from './loader.service';

/**
 * Loader spinner component.
 *
 * @author Squadra Tecnologia
 */
@Component({
  selector: 'loader',
  template: `<div *ngIf="show" aria-labelledby="dialog-static-name" class="modal-loader modal fade in show" role="dialog" tabindex="-1" aria-hidden="false" aria-modal="true" style="display: flex;">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-body">
                    <img class="loader" src="assets/images/logo-sm.png">
                  </div>
                </div>
              </div>
            </div>`,
  styleUrls: ['./loader.component.scss']
})
export class LoaderComponent {

  public show: boolean;

  /**
   * Constructor classe.
   * 
   * @param loaderService 
   */
  constructor(private loaderService: LoaderService) {
    this.loaderService.onStart.subscribe(() => { this.show = true; });
    this.loaderService.onStop.subscribe(() => { this.show = false; });
  }
}