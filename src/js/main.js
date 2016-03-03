/*eslint-env browser, jquery*/
/*globals angular ga_id*/

var scrum = scrum || {
	// Set scope values on global var
	init: function($scope, $http, $location) {
		scrum.$scope = $scope;
		scrum.$http = $http;
		scrum.$location = $location;
	},
	
	// Shared join function
    join: function() {
      var join = scrum.$scope.join;
      if(!join.id)
      {
      	join.idError = true;
      	return;
      }
      if(!join.name)
      {
      	join.nameError = true;
      	return;
      }
    	
      scrum.$http.post('/api.php?c=session&m=join', scrum.$scope.join).then(function (response) {
      	var data = response.data;
      	if(data.success)
      	{
      	  var result = data.result;
      	  scrum.$location.url('/member/' + result.sessionId + '/' + result.memberId);
      	}
        else
        {
          scrum.$scope.join.idError = true;
        }
      });
    },
    
    // Ticketing sources
    sources: [
      {name: "Default", position: 1, view: "default_source.html", feedback: false}, 
      {name: "+", position: 99, view: "add_source.html", feedback: false},
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
      	templateUrl: 'home.html',
      	controller: 'HomeController'
      })
      .when('/sessions', {
        templateUrl: 'list.html',
        controller: 'ListController'
      })
      .when('/session/:id',{
      	templateUrl : 'master.html',
      	controller: 'MasterController',
      	pageTrack: '/session'
      })
      .when('/join', { redirectTo: '/join/0' })
      .when('/join/:id', {
      	templateUrl : 'join.html',
      	controller: 'JoinController',
      	pageTrack: '/join',
      })
      .when('/member/:sessionId/:memberId', {
      	templateUrl : 'member.html',
      	controller: 'CardController',
      	pageTrack: '/member',
      })
      .otherwise({
      	templateUrl: '404.html'
      })
    ;
    
  // Set analytics id and remove ids from routes
  AnalyticsProvider.setAccount(ga_id)
                   .readFromRoute();
});
// Run once to activate tracking
scrum.app.run(function(Analytics) {});

//------------------------------
// Home controller
//------------------------------
scrum.hc = function () {
  var hc = { name: 'HomeController' };
  
  hc.createSession = function () {
  	var create = scrum.$scope.create;
  	if(!create.name)
  	{
  	  create.nameError = true;
  	  return;
  	}
  	if(create.isPrivate && !create.password)
  	{
  	  create.pwdError = true;
  	  return;
  	}
  	
  	scrum.$http.post('/api.php?c=session&m=create', scrum.$scope.create).then(function (response) {
      if(response.data.success)
  	  {
  	    scrum.$location.url('/session/' + response.data.result);
      }
  	});
  };
  
  // Init the controller
  hc.init = function ($scope, $http, $location) {
  	// Set current controller
  	scrum.current = hc;
  	
  	// Set scope and http on controller
  	scrum.init($scope, $http, $location);
  	
  	// Prepare scope
  	$scope.create = { isPrivate: false };
  	$scope.join = { error: false };
  	$scope.createSession = hc.createSession;
    $scope.joinSession = scrum.join;
  };
  
  return hc;
}();

//------------------------------
// List controller
//------------------------------
scrum.lc = function () {
  var lc = { name: 'ListController' };
  
  lc.update = function() {
  	scrum.$http.get('/api.php?c=session&m=list').then(function(response) {
  	  scrum.$scope.sessions = response.data.result;	
  	});
  };
  
  lc.open = function (session, transmit) {
  	// Public session
  	if(!session.isPrivate) {
  	  scrum.$location.url('/session/' + session.id);	
  	}
  	// Private session
  	else {
      // Check password
  	  if(transmit) {
  	  	scrum.$http.post('api.php?c=session&m=check', session).then(function (response){
  	      var data = response.data;
  	  	  if(data.success && data.result === true)
  	  	    scrum.$location.url('/session/' + session.id);
  	  	  else
  	  	    session.pwdError = true;
  	  	});
  	  }	
  	  // Toggle the expander
  	  else {
  	    session.expanded = !session.expanded;
  	  }
  	}
  };
  
  // Init the controller
  lc.init = function ($scope, $http, $location) {
  	// Set current controller
  	scrum.current = lc;
  	
  	// Set scope and http
  	scrum.init($scope, $http, $location);
  	$scope.update = lc.update;
  	$scope.open = lc.open;
  	
  	// Fetch session list
  	lc.update();
  };
  
  return lc;
}();

//------------------------------
// Join controller
//------------------------------
scrum.jc = function () {
  var jc = { name: 'JoinController' };
  
  // Mandatore init function
  jc.init = function($scope, $http, $routeParams, $location) {
  	// Set current controller
  	scrum.current = jc;
  	
  	// Init scrum
  	scrum.init($scope, $http, $location);
  	
  	// Load id from route
  	$scope.join = { id: $routeParams.id };
  	$scope.joinSession = scrum.join;
  };
  
  return jc;
}();
  
//------------------------------
// Master controller
//------------------------------
scrum.pc = function () {
  var pc = { name: 'MasterController' };
  // Start a new poll
  pc.startPoll = function (topic) {
    scrum.$http.post('/api.php?c=poll&m=start', { 
        sessionId: scrum.$scope.id, 
        topic: topic
    }).then(function(response) {
      var data = response.data;
      // Exit if call failed
      if(!data.success)
        return;
      
      // Reset our GUI
      for(var index=0; index < scrum.$scope.votes.length; index++)
      {
        var vote = scrum.$scope.votes[index];
        vote.placed = false;
        vote.active = false;
      }
      scrum.$scope.flipped = false;
    });
  };
  // Poll current votes of time members
  pc.pollVotes = function () {
  	if (scrum.current !== pc)
  	  return;
  	
    scrum.$http.get("/api.php?c=poll&m=current&id=" + scrum.$scope.id).then(function(response){
      var data = response.data;
      if(!data.success)
      {
      	// Error handling
      	return;
      }
      
      var scope = scrum.$scope;
      var result = data.result;
      scope.name = result.name;
      scope.votes = result.votes;
      scope.flipped = result.flipped;
      scope.consensus = result.consensus;
      
      // Forward result to ticketing system
      if(scope.current.feedback && scope.flipped && scope.consensus)
        scope.current.completed(scope.votes[0].value);
      
      setTimeout(scrum.pc.pollVotes, 200);
    }, function(){
      setTimeout(scrum.pc.pollVotes, 200); 
    });
  };
  // Remove a member from the session
  pc.deleteMember = function (id) {
    scrum.$http.post("/api.php?c=session&m=remove", { memberId: id });  
  };
  // Select a ticketing system
  pc.selectTicketing = function(source) {
  	scrum.$scope.current = source;
  };  
  // init the controller
  pc.init = function($scope, $http, $routeParams) {
  	// Set current controller
  	scrum.current = pc;
  	
    // Set scope and http on controller
    scrum.init($scope, $http);
    
    // Int model
    $scope.id = $routeParams.id;
    $scope.votes = [];
    $scope.current = scrum.sources[0];
    $scope.sources = scrum.sources;
    
    $scope.selectSource = scrum.pc.selectTicketing;
    $scope.startPoll = scrum.pc.startPoll;
    $scope.remove = scrum.pc.deleteMember;
    
    // Start polling
    scrum.pc.pollVotes();
  };
  
  return pc;
}();
  
// -------------------------------
// Card controller
// -------------------------------
scrum.cc = function() {
  var cc = { name: 'CardController' };
  // Reset UI
  cc.reset = function () {
  	var card = scrum.currentCard;
  	if(!card)
  	  return;
  	  
  	card.active = false;
  	card.confirmed = false;
  };  
  // Select a card from all available cards
  cc.selectCard = function (card) {
  	cc.reset();
  	scrum.currentCard = card;
    card.active = true;
    
    scrum.$http.post('/api.php?c=poll&m=place', { 
      sessionId: scrum.$scope.id, 
      memberId: scrum.$scope.member, 
      vote: card.value
    }).then(function (response) {
      if(!response.data.success)
        return;
      card.active = false;
      card.confirmed = true;
    });
  }; 
  // Fetch the current topic from the server
  cc.fetchTopic = function () {
  	if (scrum.current !== cc)
  	  return; 
  	
    scrum.$http.get("/api.php?c=poll&m=topic&sid=" + scrum.$scope.id).then(function(response){
      var data = response.data;
      if(!data.success)
      {
      	cc.reset();
      	return;
      }
    	
      var result = data.result;
      var scope = scrum.$scope;
      if(scope.topic !== result.topic || (!scope.votable && result.votable))
      {
        cc.reset();
        scope.topic = result.topic;
      }
      
      scope.votable = result.votable;
      
      setTimeout(scrum.cc.fetchTopic, 400);
    }, function() {
      setTimeout(scrum.cc.fetchTopic, 400);	
    });
  };
  // Initialize the controller
  cc.init = function($scope, $http, $routeParams) {
  	// Set current controller
  	scrum.current = cc;
  	
    // Set scope and http on controller
    scrum.init($scope, $http);
    
    // Init model
    $scope.id = $routeParams.sessionId;
    $scope.member = $routeParams.memberId;    
    $scope.votable = false;
    $scope.selectCard = scrum.cc.selectCard;   
    
    // Start timer
    scrum.cc.fetchTopic();
  };
  
  return cc;
}();

// Group all controllers in array and register them in app
scrum.controllers = [ scrum.hc, scrum.lc, scrum.jc, scrum.pc, scrum.cc ];
scrum.controllers.forEach(function(controller) {
  scrum.app.controller(controller.name, controller.init);
});
