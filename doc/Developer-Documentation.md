# REST API
Scrumonline offers a JSON REST API for developers of apps or to extend the project. The API is available at /api.php and requires two default query parameters - the controller and the method. So the standard query URL might look like this:
_/api.php?c=poll&m=list_. All methods return a response object in the following form:
````js
{
  success: bool,
  result: object,
  error: string
}
````

## SessionController
The SessionController is responsible for the session life cycle - big suprise. It creates sessions, provides the list of active sessions and adds and removes members from it.

* **List sesssions**: _/api.php?c=session&m=list_ returns an array of session objects.
* **Create session**: _/api.php?c=session&m=create_ expects an object of `{name: 'Test', isPrivate: true, password: 'Test'}` and reponds with the session id.

# Ticketing plugin
On the client side the app can be extended with plugins for ticketing systems. A ticketing plugin consists of two components:
1. JS model
2. HTML template

## JS model
The plugins JS model must be an object with some standard attributes and functions. It must be added to the source array of the scrum global. The following properties and methods are mandatory, the rest is up to you. You have access to your object within the template by binding to __master.current.<property>__ like in any other angular application. The same applies for modules. Once you fetched the topic from the ticketing system, you must call __this.parent.startPoll(topic)__.
````js
scrum.sources.push({
  /* Standard members for each plugin */
  name: "<ticketing>",
  position: 2,     
  view: "templates/<ticketing>_source.html",
  feedback: false,  // Flag that the plugin wants feedback when the poll was completed
  // Feedback call for completed poll
  completed: function(result) {
    var value = result;
    this.feedback = false;
  }

  /* Plugin specific code */
  // Load issue from ticketing system
  loadIssue: function() {
    this.feedback = true;
    /* Some ticketing code */
    this.parent.$http.get(url).then(function(response) {
      var story = response.story;
      this.parent.startPoll(story.topic);
    });
  },  
});
````

## HTML Template
Each plugin can define its UI with a partial HTML snippet that is loaded with ngInclude when the tab is selected. The __current__ object points to your JS model. It might look like this:
````html
<div class="alert alert-warning" data-ng-if="master.current.warning">
  <p>This feature requires CORS - either on server or client side!
    <a class="selectable" data-ng-click="master.current.warning = false">
      <span class="glyphicon glyphicon-remove"></span>
    </a>
  </p>
</div>
<form role="form">
  <div class="form-group">
    <label for="topic">URL:</label>
    <input type="text" class="form-control" data-ng-model="master.current.url">
  </div>
  <div class="form-group">
    <label for="topic">Story id:</label>
    <input type="text" class="form-control" data-ng-model="master.current.story">
  </div>
  <button class="btn btn-default" data-ng-click="master.current.loadIssue()">Start</button>
</form>
````