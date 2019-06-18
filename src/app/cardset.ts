export class CardSet {
  values: string[];

  visual: string;

  constructor(values : string[]) {
    this.values = values;

    this.visual = values.join(', ');
  }
}