function pollVotes($scope, $http, timeout) {
  setTimeout(function(){
    $http.get("/polls/current.php?id=" + $scope.id).success(function(response){
      $scope.votes = response;
      pollVotes($scope, $http, 500);
    });
  }, timeout);
}

function startPoll($scope, $http) {
  $http.post('/polls/start.php', { sessionId: $scope.id, topic: $scope.topic}).success(function() {
    $scope.votes[0].placed = true;
  });
}