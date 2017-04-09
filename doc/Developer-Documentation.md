# REST API
Scrumonline offers a JSON REST API for developers of apps or to extend the project. The API is available at /api/**{controller}**/**{method}**?**query** and 
requires two default query parameters - the controller and the method. So the standard query URL might look like this: _/api/session/list_. All methods return 
a response object in the following form:
````js
{
  success: bool,
  result: object,
  error: string
}
````

| **Controller** | **Method** | **Query** | **Description** |
|----------------|------------|--------------|-----------------|
| session | list | - | List of currently running sessions |
| session | create | | Create session with `{"name": "Foo", "isPrivate":true, "password":"Test"}` |
| session | join | - | Join a session with `{"id":1337, "name":"Thomas"}` |
| session | remove | - | Remove a member from a session with `{"memberId":42}` |
| session | protected | id=1337 | Check if a session is protected by password |
| session | check | - | Check of a sessions password is defined by `{"id":1337, "password":"Test"}` |
| poll | current | id=1337 | All votes and result of a sessions current poll |
| poll | start | - | Start a new poll in a session with `{"sessionId":1337, "topic":"Foo"}` |
| poll | place | - | Place a vote in a poll as a member `{"sessionId":1337, "memberId":42, "vote":3}` |
| poll | topic | sid=1337 | Current topic of a session |
| statistics | calcuate | id=1337&filter=PollCount\|AverageAttempts | Calculate statistics of the session. The filters are optional. |

# Ticketing plugin
On the client side the app can be extended with plugins for ticketing systems. A ticketing plugin consists of two components:
1. JS model
2. HTML template

## JS model
The plugins JS model must be an object with some standard attributes and functions. It must be added to the source array of the scrum global. Each plugin should be placed in a separate file _js/<ticketing>-plugin.js_. You need to include it in the _config.php_ to activate it.

The following properties and methods are mandatory, the rest is up to you. You have access to your object within the template by binding to __master.current.<property>__ like in any other angular application. The same applies for modules. Once you fetched the topic from the ticketing system, you must call __this.parent.startPoll(topic)__.

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