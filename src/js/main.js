/*eslint-env browser, jquery*/
/*globals angular ga_id*/

var scrum = {  
  // Ticketing sources
  sources: [ 
    { 
      name:'Default', 
      position: 1, 
      feedback: false,
      topic: '',
      event: ['poll', 'start', 'Default'],
      view: 'default_source.html'  
    }, 
    { 
      name: '+', 
      position: 99, 
      view: 'add_source.html', 
      feedback: false 
    },
  ],
  
  // Store of unlocked sessions
  keyring: [
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
      .when('/impressum', {
        templateUrl: 'impressum.html',        
      })
      .when('/removal', {
        templateUrl: 'removal.html',        
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
  this.cardSets = [];
  for(var i=0; i<cardSets.length; i++) {
    this.cardSets[i] = { key: i, value: cardSets[i].cards.join() };
  }
  this.selectedSet = this.cardSets[0];
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
    $http.post('/api/session/create', {
      name: self.name,
      cardSet: self.selectedSet.key,
      isPrivate: self.isPrivate,
      password: self.password
    }).then(function (response) {
      if(response.data.success) {
        // Add this id to keyring and switch view
        scrum.keyring.push(response.data.result);
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
    	
    $http.post('/api/session/join', self).then(function (response) {
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
    $http.get('/api/session/list').then(function(response) {
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
        $http.post('api/session/check', session).then(function (response){
          var data = response.data;
  	      if(data.success && data.result === true) {
            // Add to keyring if not set
            if (scrum.keyring.indexOf(session.id) === -1)
              scrum.keyring.push(session.id);
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
scrum.app.controller('MasterController', function ($http, $routeParams, $location) {
  // Validate keyring
  $http.get("api/session/protected?id=" + $routeParams.id).then(function (response) {
    if(response.data.success && response.data.result) {
     var id = parseInt($routeParams.id);
     if(scrum.keyring.indexOf(id) == -1) {
       $location.url("/404.html");
     } 
    }
  });
  
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
    $http.post('/api/poll/start', { 
      sessionId: self.id, 
      topic: topic
    }).then(function(response) {
      var data = response.data;
      // Exit if call failed
      if (!data.success) return;
      
      // Reset our GUI
      for(var index=0; index < self.votes.length; index++)
      {
        var vote = self.votes[index];
        vote.placed = false;
        vote.active = false;
      }
      self.flipped = false;
    });
  };
  
  // Remove a member from the session
  this.remove = function (id) {
    $http.post("/api/session/remove", { memberId: id });  
  };
  
  // Select a ticketing system
  this.selectSource = function(source) {
    // Give source a reference to the this and set as current
    source.parent = this;
    this.current = source;
  };
  
  // Build filter from current statistics
  function buildQuery() {
    var query = "/api/statistics/calculate?id=" + self.id;
    if (!self.statistics) 
      return query; 
    
    query += "&filter=";
    for(var i=0; i < self.statistics.length; i++) {
      // Filter the enabled ones
      var statistic = self.statistics[i];
      if (!statistic.enabled)
        continue;

      query += statistic.name + "|";
    }
    
    return query;
  }
  
  // Fetch statistics
  function fetchStatistics() {
    var query = buildQuery();    
    $http.get(query).then(function(response){
      var data = response.data;
      var result = data.result;
      
      if(self.statistics) {
        // Update values
        for (var i=0; i < result.length; i++) {
          var item = result[i];
          // Find match
          for(var j=0; j < self.statistics.length; j++) {
            var statistic = self.statistics[j];
            if(statistic.name == item.name) {
              statistic.value = item.value;
              break;
            }
          }
        }
      } else {
        // Initial set
        self.statistics = result;
      }      
    });
  } 
  
  // Poll all votes from the server 
  function pollVotes() {
    if (scrum.current !== self)
      return;
  	
    $http.get("/api/poll/current?id=" + self.id).then(function(response){
      var data = response.data;
      var result = data.result;
      if(!data.success) {
      	// Error handling
      	return;
      }
      
      // Query statistics
      if (!self.flipped && result.flipped) {
        fetchStatistics();
      }        
      
      // Copy poll values      
      self.name = result.name;
      self.votes = result.votes;
      self.flipped = result.flipped;
      self.consensus = result.consensus;
      
      // Forward result to ticketing system
      if (self.current.feedback && self.flipped && self.consensus) {
        self.current.completed(self.votes[0].value);
      }
      
      setTimeout(pollVotes, 200);
    }, function(){
      setTimeout(pollVotes, 200); 
    });
  }
  
  // Start the polling timer
  pollVotes();
});
  
// -------------------------------
// Card controller
// -------------------------------
scrum.app.controller('MemberController', function MemberController ($http, $location, $routeParams) {
  // Set current
  scrum.current = this;
  
  // Init model
  this.id = $routeParams.sessionId;
  this.member = $routeParams.memberId;    
  this.votable = false;
  this.leaving = false;
  this.topic = '';
  this.cards = [];

  // Self reference for callbacks
  var self = this;
  
  // Reset the member UI
  this.reset = function () {
    var card = this.currentCard;
    if(!card) return;
  	  
    card.active = false;
    card.confirmed = false;
  };  
  
  // Leave the session
  this.leave = function () {
    this.leaving = true;
    $http.post("/api/session/remove", { 
      memberId: this.member 
    }).then(function (response) {
      $location.url("/");
    }, function() {
      self.leaving = false;
    });  
  };
  
  // Select a card and try to place a vote
  this.selectCard = function (card) {
    this.reset();
    this.currentCard = card;
    card.active = true;
    
    $http.post('/api/poll/place', { 
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

  // Check if we are part of the session
  // callback: function (stillPresent : boolean)
  function selfCheck(callback) {
    $http.get("/api/session/membercheck?sid=" + self.id + '&mid=' + self.member).then(function(response){
      var data = response.data;
      if (self.leaving) {
        return;
      }

      if (data.success) {
        callback(data.result);
      }
    });
  }
  
  // Update current topic from server to activate voting
  function update() {
    if (scrum.current !== self) return; 
  	
    // Update topic
    $http.get("/api/poll/topic?sid=" + self.id).then(function(response){
      var data = response.data;
      if(!data.success)
      {
      	self.reset();
      	return;
      }
    	
      var result = data.result;

      // Voting was closed, get our peers votes
      if(self.votable && !result.votable) {

      }

      // Topic changed or poll was opened for voting again
      if(self.topic !== result.topic || (!self.votable && result.votable)) {
        self.reset();
        self.topic = result.topic;
      }
      
      self.votable = result.votable;
      
      setTimeout(update, 400);
    }, function() {
      setTimeout(update, 400);	
    });

    // Check if we are still here
    selfCheck(function (stillPresent){
      if(!stillPresent) {
        $location.url("/removal");
      }
    });
  };

  // Get card set of our session
  function getCardSet() {
    $http.get("/api/session/cardset?id=" + self.id).then(function(response){
      var data = response.data;
      var cards = cardSets[data.result].cards;
      for(var i=0; i<cards.length; i++) {
        self.cards[i] = { value: cards[i], active: false };
      }

      // Start timer to fetch current session state
      update();
    });
  }

  // Check if our member-id is still present
  // This may happen if users navigate-back from removal page
  selfCheck(function (present) {
    if (present) {
      // Fetch cards
      getCardSet();
    } else {
      $location.url("/join/" + self.id);
    }
  })
});
