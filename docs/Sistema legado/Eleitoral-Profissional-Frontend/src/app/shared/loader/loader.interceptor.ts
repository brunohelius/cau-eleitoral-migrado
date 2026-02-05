import { Observable } from 'rxjs';
import { finalize } from 'rxjs/operators';
import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';

import { LoaderService } from './loader.service';

/**
 * Implementation responsible for intercepting HTTP requests.
 */
@Injectable()
export class LoaderInterceptor implements HttpInterceptor {

    private requestCount: number;

    /**
     * Constructor classe.
     * 
     * @param loaderService 
     */
    constructor(private loaderService: LoaderService) {
        this.requestCount = 0;
    }

    /**
     * Method responsible for intercepting the HTTP request.
     * 
     * @param request 
     * @param next 
     */
    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        if (++this.requestCount === 1) {
            this.loaderService.onStart.emit();
        }

        return next.handle(request).pipe(finalize(() => {
            if (--this.requestCount === 0) {
                this.loaderService.onStop.emit();
            }
        }));
    }
}