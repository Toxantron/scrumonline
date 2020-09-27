import { Component, OnInit } from '@angular/core';
import { Observable, of } from 'rxjs';
import { Session, SessionListItem } from '../session';
import { SessionService } from '../session.service';

@Component({
  selector: 'app-sessions',
  templateUrl: './sessions.component.html',
  styleUrls: ['./sessions.component.css']
})
export class SessionsComponent implements OnInit {

  sessions : SessionListItem[];

  constructor(private sessionService : SessionService) { 

  }

  ngOnInit() {
    this.sessionService.getSessions()
      .subscribe(sessions => this.sessions = sessions);
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
