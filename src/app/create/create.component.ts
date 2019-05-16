import { Component, OnInit } from '@angular/core';
import { Session } from '../session';
import { CardSet } from '../cardset';

@Component({
  selector: 'create-session',
  templateUrl: './create.component.html',
  styleUrls: ['./create.component.css']
})
export class CreateComponent implements OnInit {

  session: Session = new Session();

  nameError: boolean = false;
  pwdError: boolean = false;

  cardSets : CardSet[] = [
    new CardSet(['1', '2', '3', '5', '7']),
    new CardSet(['2', '4', '8', '16', '32'])
  ];
  selectedSet : CardSet;

  constructor() {    
  }

  ngOnInit() {
    this.selectedSet = this.cardSets[0];
  }

  selectSet(set : CardSet) {
    this.selectedSet = set;
  }

  createSession() : void {
    if (Session.name == '')
      this.nameError = true;
  }
}
