import { ControlValueAccessor, NgControl } from "@angular/forms";

export class AbstractCustomInputComponent implements ControlValueAccessor {
  
 
  public required: boolean = true;

  public disabled: boolean = false;
  
  public valor: string;

  public onChangeFn = (_: any) => {};

  public onTouchedFn = () => {};

  constructor( public control: NgControl) {
    this.control && (this.control.valueAccessor = this);
  }

  public get invalid(): boolean {
    return this.control ? this.control.invalid : false;
  }

  public get showError(): boolean {
    if (!this.control) {
      return false;
    }

    const { dirty, touched } = this.control;

    return this.invalid ? dirty || touched : false;
  }

  public get errors(): Array<string> {
    if (!this.control) {
      return [];
    }
    return Object.keys(this.control.errors);
  }

  public registerOnChange(fn: any): void {
    this.onChangeFn = fn;
  }

  public registerOnTouched(fn: any): void {
    this.onTouchedFn = fn;
  }

  public setDisabledState(isDisabled: boolean): void {
    this.disabled = isDisabled;
  }

  public writeValue(obj: any): void {
    this.valor = obj;
  }

  public onChange() {
    this.onChangeFn(this.valor);
  }

}