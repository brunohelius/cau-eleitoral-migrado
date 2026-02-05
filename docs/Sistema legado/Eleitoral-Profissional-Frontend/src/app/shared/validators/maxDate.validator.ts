import { Directive, forwardRef, Attribute } from '@angular/core';
import { Validator, AbstractControl, NG_VALIDATORS, ValidationErrors } from '@angular/forms';
import * as moment from 'moment';

@Directive({
    selector: '[maxDateValidate][formControlName],[maxDateValidate][formControl],[maxDateValidate][ngModel]',
    providers: [
        { provide: NG_VALIDATORS, useExisting: forwardRef(() => MaxDateValidator), multi: true }
    ]
})
export class MaxDateValidator implements Validator {

    constructor( @Attribute('dateLimit') public dateLimit: string) {

    }

    validate(c: AbstractControl): ValidationErrors | null {
  
        let dateText: string = c.value;
        let date: Date = new Date(dateText);
        let dateLimit: Date = (this.dateLimit) ? new Date(this.dateLimit) : new Date();
        if(dateText === undefined) {
            return null;
        }

        if(dateLimit < date) {
            return {
                'maxDateValidate' : true
            };
        }
        return null;
    }
}