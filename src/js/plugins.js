/*globals scrum */

// Add a plugin for redmine integration
scrum.sources.push({
  name: "Redmine",
  position: 2,
  view: "templates/redmine_source.html",
  warning: true,
  // Load issue from redmine server
  loadIssue: function() {
  	var self = this;
  	self.feedback = true;
  	
  	var url = self.url + '/issues/' + self.story + '.json';
  	scrum.$http.get(url).then(function(response) {
      var issue = response.data.issue;
  	  self.topic = issue.subject;
  	  scrum.pc.startPoll();
  	});
  },
  
  // Feedback call for completed poll
  completed: function(result) {
  	var value = result;
  }
});
