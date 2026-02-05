import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FormDeclaracaoRepresentatividadeComponent } from './form-declaracao-representatividade.component';

describe('FormDeclaracaoRepresentatividadeComponent', () => {
  let component: FormDeclaracaoRepresentatividadeComponent;
  let fixture: ComponentFixture<FormDeclaracaoRepresentatividadeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FormDeclaracaoRepresentatividadeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FormDeclaracaoRepresentatividadeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
