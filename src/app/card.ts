export class Card {
  value: string = '';

  active: boolean;
  confirmed: boolean;

  // Optional name of the card
  name: string;

  constructor() {   
  }
}

// Cards on the master view actually represent members votes
export class MemberVote extends Card {
  id: number;

  placed: boolean;

  canDelete: boolean;

  constructor() {
    super();    
  }
}