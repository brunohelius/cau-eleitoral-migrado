
import { Directive, forwardRef, Attribute } from '@angular/core';
import { Validator, AbstractControl, NG_VALIDATORS, ValidationErrors } from '@angular/forms';

@Directive({
    selector: '[timeValidate][formControlName],[timeValidate][formControl],[timeValidate][ngModel]',
    providers: [
        { provide: NG_VALIDATORS, useExisting: forwardRef(() => TimeValidator), multi: true }
    ]
})
export class TimeValidator implements Validator {

    validate(c: AbstractControl): ValidationErrors | null {

        let time: string = c.value;
        if(time == undefined) {
            return null;
        }


        let h: number = 25;
        let m: number = 61;

        if(!Number.isNaN(Number(time)) && time.length == 4 ) {
            h = parseInt(time.substring(0,2));
            m = parseInt(time.substring(2,4));
        } 
        

        if(( (h < 0 ||  h >  24) || (m < 0 ||  m >  60) || (h == 24 &&  m >  0) ) ) {
            return {
                'timeValidator' : true
            };
        }
        return null;
    }
}