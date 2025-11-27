import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OrganizationsCards } from './organizations-cards';

describe('OrganizationsCards', () => {
  let component: OrganizationsCards;
  let fixture: ComponentFixture<OrganizationsCards>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OrganizationsCards]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OrganizationsCards);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
