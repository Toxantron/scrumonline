/*eslint-env browser */
/*globals angular */

var scrum = scrum || { };

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
      .when('/session/:id',{
      	templateUrl : '/templates/master.php',
      	controller: 'MasterController'
      })
    ;
}]);

//------------------------------
// Home controller
//------------------------------
scrum.hc = function () {
  var hc = { name: 'HomeController' };
  
  var createSession = function () {
  	scrum.$http.post('/controllers/session-controller.php?m=create', {
  	  name: scrum.$scope.name,
  	  isPrivate: scrum.$scope.isPrivate
  	}).success(function (response) {
  		scrum.sessionId = response;
  		scrum.$location.url('/session/' + response);
  	});
  };
  
  // Init the controller
  hc.init = function ($scope, $http, $location) {
  	// Set current controller
  	scrum.current = hc;
  	
  	// Set scope and http on controller
  	scrum.$scope = $scope;
  	scrum.$http = $http;
  	scrum.$location = $location;
  	
  	// Prepare scope
  	$scope.isPrivate = false;
  	$scope.createSession = createSession;
  };
  
  return hc;
}();

//------------------------------
// List controller
//------------------------------
scrum.lc = function () {
  var lc = { name: 'ListController' };
  
  // Init the controller
  lc.init = function ($scope, $http) {
  	// Set current controller
  	scrum.current = lc;
  	
  	// Fetch session list
  	$http.get('/controllers/session-controller.php?m=list').success(function(response) {
  	  $scope.sessions = response;	
  	});
  };
  
  return lc;
}();
  
//------------------------------
// Master controller
//------------------------------
scrum.pc = function () {
  var pc = { name: 'MasterController' };
  // Start a new poll
  pc.startPoll = function () {
    scrum.$http.post('/polls/start.php', { 
        sessionId: scrum.sessionId, 
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
  	
    scrum.$http.get("/polls/current.php?id=" + scrum.sessionId).success(function(response){
      scrum.$scope.votes = response.votes;
      scrum.$scope.flipped = response.flipped;
      scrum.$scope.consensus = response.consensus;
      setTimeout(scrum.pc.pollVotes, 200);
    });
  };
  // Remove a member from the session
  pc.deleteMember = function (id) {
    scrum.$http.post("/sessions/delete-member.php", { memberId: id });  
  };
  // init the controller
  pc.init = function($scope, $http) {
  	// Set current controller
  	scrum.current = pc;
  	
    // Set scope and http on controller
    scrum.$scope = $scope;
    scrum.$http = $http;
    
    // Int model from config
    $scope.startPoll = scrum.pc.startPoll;
    $scope.remove = scrum.pc.deleteMember;
    $scope.votes = [];
    
    $scope.$watch('id', scrum.pc.pollVotes);
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
    scrum.$scope.currentCard = card;
    scrum.$http.post('/polls/place-vote.php', { 
           sessionId: scrum.sessionId, 
           memberId: scrum.$scope.member, 
           vote: card.value
         }).success(scrum.cc.fetchTopic);
  };
  // Fetch the current topic from the server
  cc.fetchTopic = function () {
  	if (scrum.current !== cc)
  	  return; 
  	
    scrum.$http.get("/polls/topic.php?sid=" + scrum.$scope.id).success(function(response){
      scrum.$scope.topic = response.topic;
      scrum.$scope.votable = response.votable;
    
      setTimeout(scrum.cc.fetchTopic, 400);
    });
  };
  // Initialize the controller
  cc.init = function($scope, $http) {
  	// Set current controller
  	scrum.current = cc;
  	
    // Set scope and http on controller
    scrum.$scope = $scope;
    scrum.$http = $http;
    
    // Init model
    $scope.votable = false;
    $scope.selectCard = scrum.cc.selectCard;    
    $scope.$watch('id', scrum.cc.fetchTopic);
  };
  
  return cc;
}();

// Group all controllers in array and register them in app
scrum.controllers = [ scrum.hc, scrum.lc, scrum.pc, scrum.cc ];
scrum.controllers.forEach(function(controller) {
  scrum.app.controller(controller.name, ['$scope', '$http', '$location', controller.init]);
});
