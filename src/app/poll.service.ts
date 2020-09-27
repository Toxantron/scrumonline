import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Session } from './session';
import { MemberVote } from './card';
import { Observable, of } from 'rxjs';
import { Topic } from './topic';
import { Member } from './member';

export class PollResponse {
  name: string;
  timestamp: number;

  topic: string;
  description: string;
  url: string;

  flipped: boolean;
  consensus: boolean;

  votes: MemberVote[];
}

@Injectable({
  providedIn: 'root'
})
export class PollService {

  constructor(private http: HttpClient) { }

  currentPoll(session: Session) : Observable<PollResponse> {
    return this.http.get<PollResponse>('/api/poll/current/' + session.id);
  }

  getTopic(session: Session) : Observable<Topic> {
    return this.http.get<Topic>('/api/poll/topic/' + session.id);
  }

  setTopic(session: Session, topic: Topic) {
    this.http.post('/api/poll/topic/' + session.id, topic)
  }

  placeVote(session: Session, member:  Member, vote: string) {
    var wrapper = {
      vote: vote
    };
    var url = '/api/poll/vote/' + session.id + '/' + member.id;
    this.http.post(url, wrapper);
  }

  retractVote(session: Session, member:  Member, vote: string) {
    var url = '/api/poll/vote/' + session.id + '/' + member.id;
    this.http.delete(url);
  }
}
