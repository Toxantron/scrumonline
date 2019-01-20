/*globals scrum */

// Add a plugin for github integration
scrum.sources.push({
  // Fixed properties and methods
  name: "Gitlab",
  position: 4,
  view: "templates/gitlab_source.html",
  feedback: false,
  // Feedback call for completed poll
  completed: function(result) {
  },
  
  // Custom properties and methods
  loaded: false,
  server: 'https://gitlab.com/',
  repo: '',
  issues: [],
  issue: {},

  // Private repo
  isPrivate: false,
  token: '',
  
  // Load issues from github
  load: function() {
    var self = this;

    var headers = {};
    if(self.isPrivate) {
      headers['Private-Token'] = this.token;
    }

    // Build access URL. Gitlab is very picky about that!
    var encodedRepo = encodeURIComponent(this.repo);
    var uri = this.server;
    if(uri.substr(-1) !== '/')
      uri += '/';
    uri += 'api/v4/projects/' + encodedRepo + '/issues';
    this.parent.$http
      .get(uri, { headers: headers })
      .then(function (response) {
        // Convert markdown to HTML
        var converter = new showdown.Converter();
        response.data.forEach(function(issue) {
          issue.description = converter.makeHtml(issue.description);
        });
        self.issues = response.data;
        self.issue = self.issues[0];
        self.loaded = true;
      });
  }
});
