/*globals scrum */

// Add a plugin to load tickets from local JIRA server
scrum.sources.push({
  // Fixed properties and methods
  name: "JIRA",
  position: 3,
  view: "templates/jira_source.html",
  feedback: false,
  // Feedback call for completed poll
  completed: function(result) {
  },
  
  // Custom properties and methods
  loaded: false,
  issues: [],
  issue: {},
  event: ['poll', 'start', 'JIRA'],

  // Private repo
  isPrivate: false,
  password: ''
});
