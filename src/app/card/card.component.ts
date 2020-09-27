import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { Card } from '../card';

@Component({
  selector: 'app-card',
  templateUrl: './card.component.html',
  styleUrls: ['./card.component.css']
})
export class CardComponent implements OnInit {

  @Input() card: Card;

  @Input() flipped: boolean;

  @Input() backfaceVisible: boolean;

  @Input() canDelete: boolean;

  @Input() canSelect: boolean;

  @Output() selected: EventEmitter<Card> = new EventEmitter<Card>();

  @Output() delete: EventEmitter<Card> = new EventEmitter<Card>();

  constructor() { }

  ngOnInit() {
  }

  select() {
    if (this.canSelect)
      this.selected.emit(this.card);
  }

  callDelete() {
    this.delete.emit(this.card);
  }
}
