import { Component, OnInit } from '@angular/core';
import { Observable, of } from 'rxjs';
import { Session } from '../session';
import { CardSet } from '../cardset';
import { SessionService } from '../session.service';

@Component({
  selector: 'create-session',
  templateUrl: './create.component.html',
  styleUrls: ['./create.component.css']
})
export class CreateComponent implements OnInit {

  session: Session = new Session();

  nameError: boolean = false;
  pwdError: boolean = false;

  cardSets: CardSet[] = [];
  selectedSet: CardSet = new CardSet([]);

  constructor(private sessionService : SessionService) {
  }

  ngOnInit() {
    this.sessionService.cardSets().subscribe(sets => {
      this.cardSets = sets;
      this.selectedSet = sets[0];
      this.session.cardSet = 0;
    });    
  }

  selectSet(set : CardSet) {
    this.selectedSet = set;
    this.session.cardSet = this.cardSets.indexOf(set);
  }

  createSession() : void {
    if (Session.name == '')
      this.nameError = true;

    this.sessionService.createSession(this.session).subscribe(id => {
      // TODO: Navigate to master view
    });
  }
}
