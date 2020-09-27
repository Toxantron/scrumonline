import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpEvent } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { map } from 'rxjs/operators';
import { SessionBase, Session, SessionListItem } from './session';
import { Member } from './member';
import { CardSet } from './cardset';

/* Type for the server side response */
class Response<TValue> {
  public value: TValue;
}

@Injectable({
  providedIn: 'root'
})
export class SessionService {
  private cardSetBuffer : CardSet[];

  constructor(private http: HttpClient) { }

  // Fetch all conigured card sets from the server once
  cardSets() : Observable<CardSet[]> {
    if(this.cardSetBuffer == null) {
      var response = this.http.get<string[][]>('api/session/cardsets')
        .pipe(map(set => set.map(cards => new CardSet(cards))));
      response.subscribe(sets => this.cardSetBuffer = sets);
      return response;
    }
    return of(this.cardSetBuffer);
  }

  // Create a new session
  createSession(session : Session) : Observable<number> {
    return this.http.put<Response<number>>('api/session/create', session)
      .pipe(map(response => response.value));
  }

  // Get session by id
  getSession(id : number) : Observable<Session> {
    return of(new Session());
  }

  // Get all active sessions
  getSessions() : Observable<SessionListItem[]> {
    return this.http.get<SessionListItem[]>('api/session/active');
  }

  // Check if the session requires a password
  requiresPassword(session : SessionBase) : Observable<boolean> {
    return of(false);
  }

  // Check if the password on the session is correct
  checkPassword(session : Session) : Observable<boolean> {
    return of(true);
  }

  // Add a member to the session
  addMember(session : Session, member : Member) : Observable<number> {
    return this.http.put<Response<number>>(`api/session/member/${session.id}`, member)
      .pipe(map(response => response.value));
  }

  // Remove a member from the session
  removeMember(session : Session, member : Member) {
    this.http.delete(`api/session/member/${session.id}/${member.id}`);
  }

  // Check if a member is still part of a session
  memberCheck(session : Session, member : Member) : Observable<boolean> {
    return of(true);
  }

  // Wipe the entire session and all its data
  wipeSession(session : Session) {

  }
}
