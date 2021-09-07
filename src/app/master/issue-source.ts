import { Output, EventEmitter } from '@angular/core';
import { Topic } from '../topic';

export abstract class IssueSource {
  name: string;

  active: boolean;

  current: Topic = new Topic();

  feedback: boolean;

  newPoll: (topic: Topic) => void;

  abstract completed(): void;
}