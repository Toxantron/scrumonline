var scrum = scrum || { };

// Define angular app
scrum.app = angular.module('scrum-online', []);
  
//------------------------------
//Functions for poll controller
//------------------------------
scrum.pollController = function () {
  var pc = { name: 'pollController' };
  // Start a new poll
  pc.startPoll = function () {
    this.$http.post('/polls/start.php', { 
        sessionId: this.$scope.id, 
        topic: this.$scope.topic
    }).success(function() {
      // Reset our GUI
      for(var index=0; index < this.$scope.votes.length; index++)
      {
        var vote = this.$scope.votes[index];
        vote.placed = false;
        vote.active = false;
      }
      this.$scope.flipped = false;
    });
  };
  // Poll current votes of time members
  pc.pollVotes = function () {
    this.$http.get("/polls/current.php?id=" + this.$scope.id).success(function(response){
      this.$scope.votes = response.votes;
      this.$scope.flipped = response.flipped;
      this.$scope.consensus = response.consensus;
      setTimeout(this.pollVotes, 200);
    });
  };
  // Remove a member from the session
  pc.deleteMember = function (id) {
    this.$http.post("/sessions/delete-member.php", { memberId: id });  
  };
  // init the controller
  pc.init = function($scope, $http) {
    // Set scope and http on controller
    this.$scope = $scope;
    this.$http = $http;
    
    // Int model from config
    $scope.startPoll = this.startPoll;
    $scope.remove = this.deleteMember;
    $scope.votes = [];
    
    $scope.$watch('id', this.pollVotes);
  };
  
  return pc;
};
  
// -------------------------------
// Functions for card controller
// -------------------------------
scrum.cardController = function() {
  var cc = { name: 'cardController' };
  // Select a card from all available cards
  cc.selectCard = function (cardValue) {
    for(var index=0; index < this.$scope.cards.length; index++) {
      var card = this.$scope.cards[index];
      if(cardValue === card.value)
        this.currentCard = card;
    }
  };
  // Place your vote by transmitting current card to the server
  cc.placeVote = function () {
    this.$http.post('/polls/place-vote.php', { 
           sessionId: this.$scope.id, 
           memberId: this.$scope.member, 
           vote: this.currentCard.value
         }).success(function() { this.fetchTopic() });
  };
  // Fetch the current topic from the server
  cc.fetchTopic = function () {
    this.$http.get("/polls/topic.php?sid=" + this.$scope.id).success(function(response){
      this.$scope.topic = response.topic;
      this.$scope.votable = response.votable;
    
      setTimeout(this.fetchTopic, 400);
    });
  };
  // Initialize the controller
  cc.init = function($scope, $http) {
    // Set scope and http on controller
    this.$scope = $scope;
    this.$http = $http;
    
    // Init model
    $scope.votable = false;
    $scope.selectCard = this.selectCard;    
    $scope.placeVote = this.placeVote;
    $scope.$watch('id', this.fetchTopic);
  };
  
  return cc;
};

// Group all controllers in array and register them in app
scrum.controllers = [ scrum.pollController(), scrum.cardController() ];
scrum.controllers.forEach(function(controller, index, array) {
  app.controller(value.name, ['$scope', '$http', controller.init]);
});
