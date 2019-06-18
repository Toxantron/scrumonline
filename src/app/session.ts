export class SessionBase {
  id : number = 0;
  name : string = '';
  isPrivate : boolean = false;
  password : string = '';

  constructor() {
        
  }
}

export class SessionListItem extends SessionBase {
  memberCount : number = 0;
  requiresPassword : boolean = false;
    
  expanded : boolean = false;
  pwdError : boolean = false;

  constructor() {
    super();
  }
}

export class Session extends SessionBase {
  cardSet: number = 0;
  
  constructor() {
    super();
  }
}