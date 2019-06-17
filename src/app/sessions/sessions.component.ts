import { Component, OnInit } from '@angular/core';
import { Session } from '../session';

class SessionListItem extends Session {
  expanded : boolean = false;
  pwdError : boolean = false;

  constructor() {
    super();
  }
}

@Component({
  selector: 'app-sessions',
  templateUrl: './sessions.component.html',
  styleUrls: ['./sessions.component.css']
})
export class SessionsComponent implements OnInit {

  sessions : SessionListItem[] = [
    { id: 1, name: 'Test', password: '', isPrivate: false, expanded: false, cardSet: [], pwdError: false, },
    { id: 1, name: 'Test2', password: '', isPrivate: true, expanded: false, cardSet: [],pwdError: false },
  ]

  constructor() { }

  ngOnInit() {
  }

  openSession(session : SessionListItem) {
    if (session.isPrivate)
      session.expanded = true;
  }

  joinSession(session : SessionListItem) {
    if (session.isPrivate)    
      session.expanded = true;
  }

  continueOperation(session : SessionListItem) {

  }

  cancelOperation(session : SessionListItem) {
    session.expanded = false;
  }
}
