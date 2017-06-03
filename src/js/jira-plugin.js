/*globals scrum */

// Add a plugin to load tickets from local JIRA server
scrum.sources.push({
  // Fixed properties and methods
  name: "JIRA",
  position: 3,
  view: "templates/jira_source.html",
  feedback: false,
  jql: 'issuetype=story and status=backlog',
  // Feedback call for completed poll
  completed: function(result) {
  },
  
  // Custom properties and methods
  loaded: false,
  issues: [],
  issue: {},
  event: ['poll', 'start', 'JIRA'],

  load: function() {
    var self = this;

    var queryParameters = $.param({
      base_url: this.base_url,
      username: this.username,
      password: this.password,
      project: this.project,
      jql: this.jql
    });

    this.parent.$http({
      url: '/api/jira/getIssues',
      method: 'POST',
      data: queryParameters,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    })
      .then(function (response) {
        var data = response.data;

        if (!data || !data.issues) {
          self.error = 'Can\'t load Jira issues, check configuration';
        } else {
          self.issues = response.data.issues;
          self.issue = self.issues[0];
          self.loaded = true;
        }
      });
  },
  reload: function() {
    this.loaded = false;
  }
});
