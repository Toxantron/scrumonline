import { Component, OnInit } from '@angular/core';
import { Session } from '../session';
import { SessionService } from '../session.service';
import { Member } from '../member';

@Component({
  selector: 'join-session',
  templateUrl: './join.component.html',
  styleUrls: ['./join.component.css']
})
export class JoinComponent implements OnInit {

  session: Session = new Session();
  member: Member = new Member();

  buttonDisabled: boolean = true;
  idError: boolean = false;
  nameError: boolean = false;
  passwordError: boolean = false;

  constructor(private sessionService : SessionService) { }

  ngOnInit() {
    // Read name from cookie
  }

  idCheck() {
    this.sessionService.requiresPassword(this.session)
      .subscribe(result => {
        this.session.isPrivate = result;
        this.idError = false;
        this.evaluateButton();
      }, () => this.buttonDisabled = this.idError = true);
  }

  nameCheck() {
    if (this.member.name == null || this.member.name === '') {
      this.nameError = true;
    }
    else {
      this.nameError = false;      
    }
    this.evaluateButton();
  }

  evaluateButton() {
    if (this.idError || this.nameError || this.passwordError) {
      this.buttonDisabled = true;
    }
    else if (this.member.name == null || this.member.name === '') {
      this.buttonDisabled = true;
    }
    else {
      this.buttonDisabled = false;
    }
  }

  joinSession() {
    if (this.buttonDisabled)
      return;

    this.sessionService.addMember(this.session, this.member)
      .subscribe(memberId => {/* TODO: Navigate to member view */})
  }
}
