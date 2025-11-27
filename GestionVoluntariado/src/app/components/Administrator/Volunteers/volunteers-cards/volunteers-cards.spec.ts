import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VolunteersCards } from './volunteers-cards';

describe('VolunteersCards', () => {
  let component: VolunteersCards;
  let fixture: ComponentFixture<VolunteersCards>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [VolunteersCards]
    })
    .compileComponents();

    fixture = TestBed.createComponent(VolunteersCards);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
