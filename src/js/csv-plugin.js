/*globals scrum */

// Add a plugin for github integration
scrum.sources.push({
  // Fixed properties and methods
  name: "CSV",
  position: 5,
  view: "templates/csv_source.html",
  feedback: false,
  // Feedback call for completed poll
  completed: function(result) {
  },
  
  // Custom properties and methods
  loaded: false,
  format: '',

  // Issues after parsing the file
  issues: [],
  issue: {},
  event: ['poll', 'start', 'CSV'],

  // Load issues from github
  load: function() {
    var self = this;
    // Upload file http://stackoverflow.com/a/22538760
    var file = document.getElementById('csv_issues').files[0];
    var reader = new FileReader();
    reader.addEventListener("load", function (event) {
        var textFile = event.target;
        var content = textFile.result;
    });
    reader.readAsText(file);
  }
});
