var scrum = scrum || { };

// Define angular app
scrum.app = angular.module('scrum-online', []);
  
//------------------------------
//Functions for poll controller
//------------------------------
scrum.pollController = 
{
  // Start a new poll
  startPoll = function ($scope, $http) {
    $http.post('/polls/start.php', { sessionId: $scope.id, topic: $scope.topic}).success(function() {
      // Reset our GUI
      for(var index=0; index < $scope.votes.length; index++)
      {
        var vote = $scope.votes[index];
        vote.placed = false;
        vote.active = false;
      }
      $scope.flipped = false;
    });
  },
  // Poll current votes of time members
  pollVotes = function ($scope, $http) {
    $http.get("/polls/current.php?id=" + $scope.id).success(function(response){
      $scope.votes = response.votes;
      $scope.flipped = response.flipped;
      $scope.consensus = response.consensus;
      setTimeout(function(){
        pollVotes($scope, $http);
      }, 200);
    });
  },
  // Remove a member from the session
  deleteMember = function ($http, id) {
    $http.post("/sessions/delete-member.php", { memberId: id });  
  },
  // init the controller
  init = function($scope, $http) {
    // Int model from config
    $scope.startPoll = function() { startPoll($scope, $http); };
    $scope.remove = function(id) { deleteMember($http, id); }
    $scope.votes = [];
    
    $scope.$watch('id', function() { pollVotes($scope, $http); });
  },
  // Angular controller
  controller = scrum.app.controller('pollController', ['$scope', '$http', scrum.pollController.init])
}
  
// -------------------------------
// Functions for card controller
// -------------------------------
scrum.cardController = 
{
  // Select a card from all available cards
  selectCard = function ($scope, $http, cardValue) {
    for(var index=0; index<$scope.cards.length; index++) {
      var card = $scope.cards[index];
      if(cardValue === card.value)
        $scope.currentCard = card;
    }
  },
  // Place your vote by transmitting current card to the server
  placeVote = function ($scope, $http) {
    $http.post('/polls/place-vote.php', { sessionId: $scope.id, memberId: $scope.member, vote: $scope.currentCard.value })
         .success(function() { fetchTopic($scope, $http) });
  },
  // Fetch the current topic from the server
  fetchTopic = function ($scope, $http) {
    $http.get("/polls/topic.php?sid=" + $scope.id).success(function(response){
      $scope.topic = response.topic;
      $scope.votable = response.votable;
    
      setTimeout(function(){
        fetchTopic($scope, $http);
      }, 400);
    });
  },
  // Initialize the controller
  init = function($scope, $http) {
    // Init model
    $scope.votable = false;
    
    $scope.selectCard = function(cardValue){ selectCard($scope, $http, cardValue); };    
    $scope.placeVote = function() { placeVote($scope, $http); };
    
    $scope.$watch('id', function() { fetchTopic($scope, $http); });
  },
  // Controller for card view
  controller = app.controller('cardController', ['$scope', '$http', init]); 
};
