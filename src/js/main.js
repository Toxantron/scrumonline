/*eslint-env browser, jquery*/
/*globals angular*/

var scrum = {  
  // Ticketing sources
  sources: [ 
    { 
      name:'Default', 
      position: 1, 
      feedback: false,
      topic: '',
      description: '',
      view: 'default_source.html'
    },
    { 
      name: '+', 
      position: 99, 
      view: 'add_source.html', 
      feedback: false 
    },
  ],

  // At peak times the number of polling clients exceeds the servers capacity.
  // To avoid error 503 and rather keep the page running this strategy adapts
  // the polling interval to the response behavior -> Few clients & fast polling
  // or many clients and slow polling
  pollingScale: (function () {
    // Scale goes from 1.0 to 5.0 and starts in the middle
    var min = 1.0, current = 2.5, max = 5.0;

    return {
      // Call was successful -> decrease scale slowly
      success: function() {
        if (current > min)
          current -= 0.05;
      },
      // Call failed, so increase scale 4 times faster then decrease
      failed: function() {
        if (current < max)
          current += 0.2
      },
      // Scale interval using the current scaling value
      scale: function(interval, callback) {
        interval *= current;

        // Quadruple polling interval while tab is out of focus
        if (!document.hasFocus()) 
          interval *= 4;

        setTimeout(callback, interval);
      }
    };
  })()
};

// Define angular app
scrum.app = angular.module('scrum-online', ['ngRoute', 'ngSanitize', 'ngCookies']);

//------------------------------
// Configure routing
// -----------------------------
scrum.app.config(
  function($locationProvider, $routeProvider) {
    // Use HTML5 mode for prettier routes
    $locationProvider.html5Mode(true);

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
        controllerAs: 'master'
      })
      .when('/join', { redirectTo: '/join/0' })
      .when('/join/:id', {
      	templateUrl : 'join.html',
      	controller: 'JoinController',
        controllerAs: 'join'
      })
      .when('/member/:sessionId/:memberId', {
      	templateUrl : 'member.html',
      	controller: 'MemberController',
        controllerAs: 'member'
      })
      .when('/removal', {
        templateUrl: 'removal.html',        
      })
      .otherwise({
      	templateUrl: '404.html'
      })
    ;
});

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
      $location.url('/session/' + response.data.value);
    });
  };
});

//------------------------------
// Join controller
//------------------------------
scrum.app.controller('JoinController', function JoinController($http, $location, $routeParams, $cookies) {
  // Save reference to current
  scrum.current = this;
  
  // Init properties
  this.id = $routeParams.id;
  this.idError = false;
  this.name = '';
  this.nameError = false;
  this.password = '';
  this.requiresPassword = false;

  // If the route contains the token, append it to the API call
  var cookieQuery = '';
  var cookieValue = $location.search().token;
  if(cookieValue)
    cookieQuery = '?token=' + cookieValue;
  
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
    	
    $http.put('/api/session/member/' + self.id + cookieQuery, { name: self.name, password: self.password })
      .then(function (response) {
        var result = response.data;
        $location.url('/member/' + result.sessionId + '/' + result.memberId);
      }, function () {
        self.idError = true;
      });
  };

  this.passwordCheck = function() {
    $http.get("api/session/requiresPassword/" + self.id).then(function (response) {
      self.idError = false;
      self.requiresPassword = response.data.success;
    }, function () {
      self.idError = true;
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
    $http.get('/api/session/active').then(function(response) {
      self.sessions = response.data;
    });
  };

  // Check the password of a session
  function checkPassword(session, url) {
    $http.post('api/session/check/' + session.id, {password: session.password}).then(function (response){
      var data = response.data;
      if (data.success === true) {
        $location.url(url + '/' + session.id);
      } else {
        session.pwdError = true;
      }
    });
  }

  // Continue opening operation 
  function continueOpen(session) {
    checkPassword(session, 'session');
  }

  // Continue joining operation
  function continueJoin(session) {
    checkPassword(session, 'join');
  }
  
  // Open session
  this.open = function (session) {
    // Public session
    if (!session.requiresPassword) {
      $location.url('/session/' + session.id);	
    } else if (session.expanded) {
      this.continue = continueOpen;
    } else {
      // Toggle the expander and set continue method
      session.expanded = true;
      this.continue = continueOpen;
    }
  };

  // Join the session
  this.join = function(session) {
    // Public session
    if (!session.requiresPassword) {
      $location.url('/join/' + session.id);
    } else if (session.expanded) {
      this.continue = continueJoin;
    } else {
      // Toggle the expander and set continue method
      session.expanded = true;
      this.continue = continueJoin;
    }
  };
  
  // Invoke update to fetch sessions
  this.update();
});
  
//------------------------------
// Master controller
//------------------------------
scrum.app.controller('MasterController', function ($http, $routeParams, $location, $cookies, $timeout) {
  // Validate keyring
  $http.get("api/session/requiresPassword/" + $routeParams.id).then(function (response) {
    if(response.data.success) {
      // Redirect to 404 if the session requires password
      $location.url("/404.html"); 
    }
  });
  
  // Set current controller and self reference
  var self = scrum.current = this;
  
  // Save reference to $http for plugins
  this.$http = $http;

  // Flag for initialization
  this.teamComplete = false;
  
  // Init the properties
  this.id = $routeParams.id;
  this.name = '';
  this.votes = [];
  this.flipped = false;
  this.consensus = false;
  this.sources = scrum.sources;
  this.current = this.sources[0];
  
  // Fragment for the join url
  this.joinFragment = this.id;
  var token = $cookies.get('session-token-' + this.id);
  if (token)
    this.joinFragment += '?token=' + token;

  // Stopwatch
  var interval = 1000;
  var stopwatchMs = 0;
  this.stopwatchElapsed = '00:00';
  this.stopwatch = function() {
    // Break recursive timer after completion
    if(self.flipped)
      return;

    $timeout(function() {
      stopwatchMs += interval; // Increase timer
      // Format nicely -> thanks to https://stackoverflow.com/a/35890816/6082960
      self.stopwatchElapsed = new Date(stopwatchMs).toISOString().slice(14, 19);
      // Start next cycle
      self.stopwatch();    
    }, interval);
  };
  
  // Starting a new poll
  this.startPoll = function (topic, description, url) {
    $http.post('/api/poll/topic/' + self.id, { topic: topic, description:description || '', url:url || '' }).then(function(response) {
      // Reset our GUI
      for(var index=0; index < self.votes.length; index++)
      {
        var vote = self.votes[index];
        vote.placed = false;
        vote.active = false;
      }
      self.flipped = false;
      // Reset stopwatch
      stopwatchMs = 0;
      self.stopwatchElapsed = '00:00';
      // Start the stopwatch
      self.stopwatch();
    });
  };
  
  // Remove a member from the session
  this.remove = function (id) {
    $http.delete("/api/session/member/" + self.id + "/" + id);  
  };

  // Wipe the session and redirect
  this.wipe = function () {
    var confirmed = confirm("Do you want to delete the session and wipe all associated data?");
    if (!confirmed)
      return;
      
    $http.delete('/api/session/wipe/' + self.id).then(function (response){
      $location.url("/404.html"); // Redirect to 404 when we wiped the session
    });
  }
  
  // Select a ticketing system
  this.selectSource = function(source) {
    // Give source a reference to the this and set as current
    source.parent = this;
    this.current = source;
  };
  
  // Fetch statistics
  function fetchStatistics() {
    var query = "/api/statistics/calculate/" + self.id    
    $http.get(query).then(function(response){
      var result = response.data;
      
      if (self.statistics) {
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
  	
    $http.get("/api/poll/current/" + self.id + "?last=" + self.timestamp).then(function(response){
      var result = response.data;

      // Session was not modified
      if (result.unchanged) {
        scrum.pollingScale.scale(300, pollVotes);
        return;
      }
      
      // Query statistics
      if (!self.flipped && result.flipped) {
        fetchStatistics();
      }        
      
      // Copy poll values      
      self.name = result.name;
      self.timestamp = result.timestamp;
      self.votes = result.votes;
      self.flipped = result.flipped;
      self.consensus = result.consensus;

      // If the result has a topic, the team has started estimating
      if(result.topic !== '') {
        self.current.topic = result.topic;
        self.current.description = result.description;
        self.teamComplete = true;
      }        
      
      // Forward result to ticketing system
      if (self.current.feedback && self.flipped && self.consensus) {
        self.current.completed(self.votes[0].value);
      }
      
      scrum.pollingScale.success();
      scrum.pollingScale.scale(400, pollVotes);
    }, function(){
      scrum.pollingScale.failed();
      scrum.pollingScale.scale(400, pollVotes);
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
  this.description = '';
  this.topicUrl = '';
  this.cards = [];

  // Self reference for callbacks
  var self = this;
  
  // Reset the member UI
  this.reset = function () {
    for (var i=0; i < this.cards.length; i++) {
      this.cards[i].active = false;
      this.cards[i].confirmed = false;
    }
  };  
  
  // Leave the session
  this.leave = function () {
    this.leaving = true;
    $http.delete("/api/session/member/" + self.id + "/" + self.member).then(function (response) {
      $location.url("/");
    }, function() {
      self.leaving = false;
    });  
  };
  
  // Select a card and try to place a vote
  this.selectCard = function (card) {
    // If the user tapped the confirmed card again, remove the vote
    if (this.currentCard == card && this.currentCard.confirmed) {
      $http.delete('/api/poll/vote/' + this.id + '/' + this.member).then(function() {
        self.currentCard.confirmed = false;
        self.currentCard = null;
      });
      return;
    }

    // Otherwise figure out what to do
    this.currentCard = card;
    card.active = true;
    
    $http.post('/api/poll/vote/' + this.id + "/" + this.member, {
      vote: card.value
    }).then(function (response) {
      self.reset();
      card.confirmed = true;
    });
  }; 

  // Check if we are part of the session
  // callback: function (stillPresent : boolean)
  function selfCheck(callback) {
    $http.get("/api/session/membercheck/" + self.id + '/' + self.member).then(function(response){
      var data = response.data;
      if (self.leaving) {
        return;
      }

      callback(data.success);
    });
  }
  
  // Update current topic from server to activate voting
  function update() {
    if (scrum.current !== self) 
      return;
  	
    // Update topic
    $http.get("/api/poll/topic/" + self.id + "?last=" + self.timestamp).then(function(response){
      var result = response.data;

      // Keep current state
      if (result.unchanged) {
        scrum.pollingScale.scale(500, update);
        return
      }

      self.timestamp = result.timestamp;

      // Voting was closed, get our peers votes
      if(self.votable && !result.votable) {
      }

      // Topic changed or poll was opened for voting again
      if(self.topic !== result.topic || (!self.votable && result.votable)) {
        self.reset();
        self.topic = result.topic;
        self.description = result.description || '';
        self.topicUrl = result.url || '#';
      }
      
      self.votable = result.votable;
      
      scrum.pollingScale.success();
      scrum.pollingScale.scale(500, update);
    }, function() {
      scrum.pollingScale.failed();
      scrum.pollingScale.scale(500, update);	
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
    $http.get("/api/session/cardset/" + self.id).then(function(response){
      var cards = response.data;
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
