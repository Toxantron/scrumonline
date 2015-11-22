function pollVotes($scope, $http) {
  $http.get("/polls/current.php?id=" + $scope.id).success(function(response){
      $scope.votes = response;
      setTimeout(function(){
        pollVotes($scope, $http);
      }, 250);
  });
}

function startPoll($scope, $http) {
  $http.post('/polls/start.php', { sessionId: $scope.id, topic: $scope.topic}).success(function() {
    // Reset our GUI
    for(var index=0; index < $scope.votes.length; index++)
    {
      var vote = $scope.votes[index];
      vote.placed = false;
      vote.flipped = false;
    }
  });
}

function selectCard($scope, $http, cardValue) {
  for(var index=0; index<$scope.cards.length; index++) {
    var card = $scope.cards[index];
    if(cardValue === card.value)
      $scope.currentCard = card;
   }
}

function placeVote($scope, $http) {
  $http.post('/polls/place-vote.php', { sessionId: $scope.id, memberId: $scope.member, vote: $scope.currentCard.value })
       .success(function() { fetchTopic($scope, $http) });
}

function fetchTopic($scope, $http) {
  $http.get("/polls/topic.php?sid=" + $scope.id).success(function(response){
    $scope.topic = response.topic;
    $scope.votable = response.votable;
    
    setTimeout(function(){
      fetchTopic($scope, $http);
    }, 500);
  });
}

var scrum = function() {
  var app = angular.module('scrum-online', []);
  
  // Controller for current poll
  app.controller('pollController', ['$scope', '$http', function($scope, $http) {
    // Int model from config
    $scope.startPoll = function() { startPoll($scope, $http); };    
    $scope.votes = [];
    
    $scope.$watch('id', function() { pollVotes($scope, $http); });
  }]);
  
  // Controller for card view
  app.controller('cardController', ['$scope', '$http', function($scope, $http) {
    // Init model
    $scope.votable = false;
    
    $scope.selectCard = function(cardValue){ selectCard($scope, $http, cardValue); };    
    $scope.placeVote = function() { placeVote($scope, $http); };
    
    $scope.$watch('id', function() { fetchTopic($scope, $http); });
  }]); 
  
  return app;
}();