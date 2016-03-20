/*globals scrum */

// Add a plugin for redmine integration
scrum.sources.push({
  name: "Redmine",
  position: 2,
  view: "templates/redmine_source.html",
  feedback: false,
  // Feedback call for completed poll
  completed: function(result) {
    this.pollResult = result;
    this.pollComplete = true;
  },
  
  // Custom properties and methods
  warning: true,
  loggedIn: false,
  pollComplete: false,
  pollResult: 0,
  
  
  // Properties of first view
  url: 'http://',    // Url of the server
  token: '',  // Token used for REST authentication
  login: function() {
    var self = this;
    var url = this.url + '/issues.json';
    this.parent.$http.get(url, { header: {
      'X-Redmine-API-Key': this.token,
    }}).then(function (response) {
      self.stories = response.data.issues;
      self.story = self.stories[0];
      self.loggedIn = true;
    });  
  },
  
  // Properties after log in was completed
  stories: [],
  story: '',
  
  // Load issue from redmine server
  loadIssue: function() {
    this.feedback = true;
    this.pollComplete = false;
    this.parent.startPoll(this.story.subject);
  }
});
