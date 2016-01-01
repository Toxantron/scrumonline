/*eslint-env browser */
/*globals angular */

var scrum = scrum || {
	init: function($scope, $http, $location) {
		scrum.$scope = $scope;
		scrum.$http = $http;
		scrum.$location = $location;
	},
	
	join: function() {
      scrum.$http.post('/api.php?c=session&m=join', {
        id: scrum.$scope.id,
        name : scrum.$scope.name
      }).success(function (reponse) {
        scrum.$location.url('/member/' + reponse.sessionId + '/' + reponse.memberId);
      });
    }
};

// Define angular app
scrum.app = angular.module('scrum-online', ['ngRoute']);

//------------------------------
// Configure routing
// -----------------------------
scrum.app.config(['$routeProvider',
  function($routeProvider) {
  	// Configure routing
    $routeProvider
      .when('/', {
      	templateUrl: '/templates/home.html',
      	controller: 'HomeController'
      })
      .when('/session-list', {
        templateUrl: '/templates/list.html',
        controller: 'ListController'
      })
      .when('/session/:id/:name',{
      	templateUrl : '/templates/master.php',
      	controller: 'MasterController'
      })
      .when('/join', { redirectTo: '/join/0' })
      .when('/join/:id', {
      	templateUrl : '/templates/join.html',
      	controller: 'JoinController'
      })
      .when('/member/:sessionId/:memberId', {
      	templateUrl : '/templates/member.php',
      	controller: 'CardController'
      })
    ;
}]);

//------------------------------
// Home controller
//------------------------------
scrum.hc = function () {
  var hc = { name: 'HomeController' };
  
  hc.createSession = function () {
  	scrum.$http.post('/api.php?c=session&m=create', {
  	  name: scrum.$scope.name,
  	  isPrivate: scrum.$scope.isPrivate
  	}).success(function (response) {
  		scrum.$location.url('/session/' + response + '/' + scrum.$scope.name);
  	});
  };
  
  // Init the controller
  hc.init = function ($scope, $http, $location) {
  	// Set current controller
  	scrum.current = hc;
  	
  	// Set scope and http on controller
  	scrum.init($scope, $http, $location);
  	
  	// Prepare scope
  	$scope.isPrivate = false;
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
  	scrum.$http.get('/api.php?c=session&m=list').success(function(response) {
  	  scrum.$scope.sessions = response;	
  	});
  };
  
  lc.open = function (session) {
  	if(session.isPrivate) {
  	  session.expanded = true;
  	}
  	else {
  	  scrum.$location.url('/session/' + session.id + '/' + session.name);
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
  	$scope.id = $routeParams.id;
  	$scope.join = scrum.join;
  };
  
  return jc;
}();
  
//------------------------------
// Master controller
//------------------------------
scrum.pc = function () {
  var pc = { name: 'MasterController' };
  // Start a new poll
  pc.startPoll = function () {
    scrum.$http.post('/api.php?c=poll&m=start', { 
        sessionId: scrum.$scope.id, 
        topic: scrum.$scope.topic
    }).success(function() {
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
  	
    scrum.$http.get("/api.php?c=poll&m=current&id=" + scrum.$scope.id).success(function(response){
      scrum.$scope.votes = response.votes;
      scrum.$scope.flipped = response.flipped;
      scrum.$scope.consensus = response.consensus;
      setTimeout(scrum.pc.pollVotes, 200);
    });
  };
  // Remove a member from the session
  pc.deleteMember = function (id) {
    scrum.$http.post("/api.php?c=session&m=remove", { memberId: id });  
  };
  // init the controller
  pc.init = function($scope, $http, $routeParams) {
  	// Set current controller
  	scrum.current = pc;
  	
    // Set scope and http on controller
    scrum.init($scope, $http);
    
    // Int model
    $scope.id = $routeParams.id;
    $scope.name = $routeParams.name;
    
    $scope.startPoll = scrum.pc.startPoll;
    $scope.remove = scrum.pc.deleteMember;
    $scope.votes = [];
    
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
  // Select a card from all available cards
  cc.selectCard = function (card) {
  	if(scrum.currentCard != null) {
      scrum.currentCard.active = scrum.currentCard.confirmed = false;
  	}
  	scrum.currentCard = card;
    card.active = true;
    scrum.$http.post('/api.php?c=poll&m=place', { 
           sessionId: scrum.$scope.id, 
           memberId: scrum.$scope.member, 
           vote: card.value
         }).success(function (response) {
         	card.active = false;
         	card.confirmed = response;
         });
  };
  // Fetch the current topic from the server
  cc.fetchTopic = function () {
  	if (scrum.current !== cc)
  	  return; 
  	
    scrum.$http.get("/api.php?c=poll&m=topic&sid=" + scrum.$scope.id).success(function(response){
      scrum.$scope.topic = response.topic;
      scrum.$scope.votable = response.votable;
    
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
