import { Component, OnInit, EventEmitter } from '@angular/core';
import { IssueSource } from '../issue-source';

@Component({
  selector: 'default-source',
  templateUrl: './default-source.component.html',
  styleUrls: ['./default-source.component.css'],
  providers: [{ provide: IssueSource, useExisting: DefaultSourceComponent }]
})
export class DefaultSourceComponent extends IssueSource implements OnInit  {
 
  constructor() {
    super();

    this.active = true;
    this.name = "Default";
   }

  ngOnInit() {
  }

  completed() {
  }
}
