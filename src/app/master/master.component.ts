import { Component, OnInit, AfterViewInit, ViewChildren, QueryList } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Session } from '../session';
import { Card, MemberVote } from '../card';
import { IssueSource } from './issue-source';
import { DefaultSourceComponent } from './default-source/default-source.component';
import { Statistic } from '../statistic';
import { Topic } from '../topic';

@Component({
  selector: 'app-master',
  templateUrl: './master.component.html',
  styleUrls: ['./master.component.css']
})
export class MasterComponent implements OnInit, AfterViewInit {

  @ViewChildren(IssueSource) issueSources: QueryList<IssueSource>;

  session: Session = new Session();

  votes: MemberVote[] = [];

  statistics: Statistic[];

  stopwatchElapsed: string = '00:00';

  teamComplete: boolean = false;

  flipped: boolean;

  constructor(
    private route: ActivatedRoute) { 

    }

  ngOnInit() {
    // TODO: Read from service
    var session = new Session();
    session.id = +this.route.snapshot.paramMap.get('id');
    session.name = "Hi";
    this.session = session;
    
    this.flipped = true;
    this.votes = [
      {id: 1, value: '1', active: false,name: 'Thomas',confirmed: true,placed: true, canDelete: true},
      {id: 1, value: '3', active: false,name: 'Sandra',confirmed: true,placed: true, canDelete: true},
    ];
    this.statistics = [
      { name: "Test", value: "123", enabled: true}
    ]
  }

  ngAfterViewInit(): void {
    this.issueSources.forEach(s => s.newPoll = this.startPoll);
  }

  joinUrl(encode: boolean) : string {
    var location = window.location;
    // Build url from location
    var url = `${location.protocol}//${location.hostname}:${location.port}/join/${this.session.id}`;
    if (encode)
      url = encodeURIComponent(url);
    return url;
  }

  selectSource(source: IssueSource) {
    this.issueSources.forEach(s => s.active = s == source);
  }

  startPoll(topic: Topic) {
    console.log('Poll started');
  }

  removeMember(card: Card) {

  }

  wipe() {

  }
}
