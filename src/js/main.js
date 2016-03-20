/*eslint-env browser, jquery*/
/*globals angular ga_id*/

var scrum = scrum || {  
  // Ticketing sources
  sources: [ 
    { 
      name: "Default", 
      position: 1, 
      feedback: false,
      topic: '',
      view: "default_source.html"  
    }, 
    { 
      name: "+", 
      position: 99, 
      view: "add_source.html", 
      feedback: false 
    },
  ]
};

// Define angular app
scrum.app = angular.module('scrum-online', ['ngRoute', 'angular-google-analytics']);

//------------------------------
// Configure routing
// -----------------------------
scrum.app.config(
  function($routeProvider, AnalyticsProvider) {
    // Configure routing
    $routeProvider
      .when('/', {
      	templateUrl: 'home.html'
      })
      .when('/sessions', {
        templateUrl: 'list.html',
        controller: 'ListController',
        controllerAs: 'list'
      })
      .when('/session/:id',{
      	templateUrl : 'master.html',
      	controller: 'MasterController',
        controllerAs: 'master',
      	pageTrack: '/session'
      })
      .when('/join', { redirectTo: '/join/0' })
      .when('/join/:id', {
      	templateUrl : 'join.html',
      	controller: 'JoinController',
        controllerAs: 'join',
      	pageTrack: '/join'
      })
      .when('/member/:sessionId/:memberId', {
      	templateUrl : 'member.html',
      	controller: 'MemberController',
        controllerAs: 'member',
      	pageTrack: '/member'
      })
      .otherwise({
      	templateUrl: '404.html',
      	dontTrack: true
      })
    ;
    
  // Set analytics id and remove ids from routes
  AnalyticsProvider.setAccount(ga_id)
  		   .readFromRoute(true)
  		   .ignoreFirstPageLoad(true);
});
// Run once to activate tracking
scrum.app.run(function(Analytics) {});

//------------------------------
// Create controller
//------------------------------
scrum.app.controller('CreateController', function CreateController($http, $location) {
  // Save reference and set current
  scrum.current = this;
  
  // Initialize properties
  this.name = '';
  this.isPrivate = false;
  this.password = '';
  
  // Create a new session
  var self = this;
  this.createSession = function() {
    // Validate input
    if(!self.name) {
      self.nameError = true;
      return;
    }
    if(self.isPrivate && !self.password) {
      self.pwdError = true;
      return;
    }
  	
    // Post session creation to server
    $http.post('/api.php?c=session&m=create', this).then(function (response) {
      if(response.data.success) {
        $location.url('/session/' + response.data.result);
      }
    });
  };
});

//------------------------------
// Join controller
//------------------------------
scrum.app.controller('JoinController', function JoinController($http, $location, $routeParams) {
  // Save reference to current
  scrum.current = this;
  
  // Init properties
  this.id = $routeParams.id;
  this.idError = false;
  this.name = '';
  this.nameError = false;
  
  // Join function
  var self = this;
  this.joinSession = function() {
    // Validate input
    if (!self.id) {
      self.idError = true;
      return;
    }
    if (!self.name) {
      self.nameError = true;
      return;
    }
    	
    $http.post('/api.php?c=session&m=join', self).then(function (response) {
      var data = response.data;
      if(data.success) {
        var result = data.result;
      	$location.url('/member/' + result.sessionId + '/' + result.memberId);
      } else {
        self.idError = true;
      }
    });
  };
});

//------------------------------
// List controller
//------------------------------
scrum.app.controller('ListController', function($http, $location) {
  // Set current controller
  scrum.current = this;
  
  // Update the list
  var self = this;
  this.update = function() {
    $http.get('/api.php?c=session&m=list').then(function(response) {
      self.sessions = response.data.result;
    });
  };
  
  // Open session
  this.open = function (session, transmit) {
    // Public session
    if (!session.isPrivate) {
      $location.url('/session/' + session.id);	
    } else {
      // Private session
      // Check password
      if(transmit) {
        $http.post('api.php?c=session&m=check', session).then(function (response){
          var data = response.data;
  	      if(data.success && data.result === true) {
            $location.url('/session/' + session.id);
          } else {
            session.pwdError = true;
  	  }
        });
      }	else {
        // Toggle the expander
        session.expanded = !session.expanded;
      }
    }
  };
  
  // Invoke update to fetch sessions
  this.update();
});
  
//------------------------------
// Master controller
//------------------------------
scrum.app.controller('MasterController', function ($http, $routeParams) {
  // Set current controller
  scrum.current = this;
  
  // Save reference to $http for plugins
  this.$http = $http;
  
  // Init the properties
  this.id = $routeParams.id;
  this.name = '';
  this.votes = [];
  this.flipped = false;
  this.consensus = false;
  this.sources = scrum.sources;
  this.current = this.sources[0];
  
  // Starting a new poll
  var self = this;
  this.startPoll = function (topic) {
    $http.post('/api.php?c=poll&m=start', { 
      sessionId: self.id, 
      topic: topic
    }).then(function(response) {
      var data = response.data;
      // Exit if call failed
      if (!data.success) return;
      
      // Reset our GUI
      for(var index=0; index < self.votes.length; index++)
      {
        var vote = scrum.$scope.votes[index];
        vote.placed = false;
        vote.active = false;
      }
      self.flipped = false;
    });
  };
  
  // Remove a member from the session
  this.remove = function (id) {
    $http.post("/api.php?c=session&m=remove", { memberId: id });  
  };
  
  // Select a ticketing system
  this.selectSource = function(source) {
    // Give source a reference to the this and set as current
    source.parent = this;
    this.current = source;
  }; 
  
  // Poll all votes from the server 
  this.pollVotes = function () {
    if (scrum.current !== self)
      return;
  	
    $http.get("/api.php?c=poll&m=current&id=" + self.id).then(function(response){
      var data = response.data;
      if(!data.success)
      {
      	// Error handling
      	return;
      }
      
      var result = data.result;
      self.name = result.name;
      self.votes = result.votes;
      self.flipped = result.flipped;
      self.consensus = result.consensus;
      
      // Forward result to ticketing system
      if (self.current.feedback && self.flipped && self.consensus) {
        self.current.completed(self.votes[0].value);
      }
      
      setTimeout(self.pollVotes, 200);
    }, function(){
      setTimeout(self.pollVotes, 200); 
    });
  };
  
  // Start the polling timer
  this.pollVotes();
});
  
// -------------------------------
// Card controller
// -------------------------------
scrum.app.controller('MemberController', function MemberController ($http, $routeParams) {
  // Set current
  scrum.current = this;
  
  // Init model
  this.id = $routeParams.sessionId;
  this.member = $routeParams.memberId;    
  this.votable = false;
  this.topic = '';
  
  // Reset the member UI
  this.reset = function () {
    var card = this.currentCard;
    if(!card) return;
  	  
    card.active = false;
    card.confirmed = false;
  };  
  
  // Select a card and try to place a vote
  this.selectCard = function (card) {
    this.reset();
    this.currentCard = card;
    card.active = true;
    
    $http.post('/api.php?c=poll&m=place', { 
      sessionId: this.id, 
      memberId: this.member, 
      vote: card.value
    }).then(function (response) {
      if(!response.data.success)
        return;
      card.active = false;
      card.confirmed = true;
    });
  }; 
  
  // Update current topic from server to activate voting
  var self = this;
  this.fetchTopic = function () {
    if (scrum.current !== self) return; 
  	
    $http.get("/api.php?c=poll&m=topic&sid=" + self.id).then(function(response){
      var data = response.data;
      if(!data.success)
      {
      	self.reset();
      	return;
      }
    	
      var result = data.result;
      if(self.topic !== result.topic || (!self.votable && result.votable))
      {
        self.reset();
        self.topic = result.topic;
      }
      
      self.votable = result.votable;
      
      setTimeout(self.fetchTopic, 400);
    }, function() {
      setTimeout(self.fetchTopic, 400);	
    });
  };
  
      // Start timer
  this.fetchTopic();
});
