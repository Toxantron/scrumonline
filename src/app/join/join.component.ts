import { Component, OnInit } from '@angular/core';
import { Session } from '../session';

@Component({
  selector: 'join-session',
  templateUrl: './join.component.html',
  styleUrls: ['./join.component.css']
})
export class JoinComponent implements OnInit {

  session: Session = new Session();

  idError : boolean = false;
  nameError : boolean = false;
  passwordError : boolean = false;

  constructor() { }

  ngOnInit() {
    // Read name from cookie
  }

  passwordCheck() {
    
  }

  joinSession() {

  }
}
