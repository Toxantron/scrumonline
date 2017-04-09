/*globals scrum */

// Add a plugin for github integration
scrum.sources.push({
  // Fixed properties and methods
  name: "Github",
  position: 3,
  view: "templates/github_source.html",
  feedback: false,
  // Feedback call for completed poll
  completed: function(result) {
  },
  
  // Custom properties and methods
  loaded: false,
  user: '',
  repo: '',
  issues: [],
  issue: {},
  event: ['poll', 'start', 'Github'],

  // Private repo
  isPrivate: false,
  password: '',
  
  // Load issues from github
  load: function() {
    var self = this;

    var headers = {};
    if(self.isPrivate) {
      var auth = window.btoa(self.user + ':' + self.password);
      headers.Authorization = 'Basic ' + auth;
    }

    this.parent.$http
      .get('https://api.github.com/repos/' + this.repo + '/issues', { headers: headers })
      .then(function (response) {
        self.issues = response.data;
        self.issue = self.issues[0];
        self.loaded = true;
      });
  }
});
