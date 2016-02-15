/*eslint-env browser, jquery*/
/*globals angular */

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
    }
};

// Define angular app
scrum.app = angular.module('scrum-online', ['ngRoute']);

//------------------------------
// Configure routing
// -----------------------------
scrum.app.config(
  function($routeProvider) {
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
      	controller: 'MasterController'
      })
      .when('/join', { redirectTo: '/join/0' })
      .when('/join/:id', {
      	templateUrl : 'join.html',
      	controller: 'JoinController'
      })
      .when('/member/:sessionId/:memberId', {
      	templateUrl : 'member.html',
      	controller: 'CardController'
      })
      .otherwise({
      	templateUrl: '404.html'
      })
    ;
});

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
  pc.startPoll = function () {
    scrum.$http.post('/api.php?c=poll&m=start', { 
        sessionId: scrum.$scope.id, 
        topic: scrum.$scope.topic
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
      scrum.$scope.focus = false;
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
      
      var result = data.result;
      scrum.$scope.name = result.name;
      scrum.$scope.votes = result.votes;
      scrum.$scope.flipped = result.flipped;
      scrum.$scope.consensus = result.consensus;
      if (!scrum.$scope.focus)
        scrum.$scope.topic = result.topic;
      
      setTimeout(scrum.pc.pollVotes, 200);
    }, function(){
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
