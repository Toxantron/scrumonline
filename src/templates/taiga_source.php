<?php
include __DIR__ . "/../config.php";
?>
<!-- Initial screen till static information was put in -->
<div ng-if="!master.current.loaded">
  <form role="form">
    <div class="row" ng-if="master.current.error">
      <div class="alert alert-danger">{{master.current.error}}</div>
    </div>

    <div class="row" ng-if="!master.current.disable_jira_fields">
      <div class="col-xs-12 col-md-6">
        <div class="form-group">
          <label>Taiga base url:</label>
          <input type="text" class="form-control" placeholder="https://your.taiga.server" ng-init="master.current.base_url = '<?php print($taigaConfiguration['base_url']); ?>'" ng-model="master.current.base_url">
        </div>
      </div>
      <div class="col-xs-12 col-md-6">
        <div class="form-group">
          <label>Project</label>
          <input type="text" class="form-control" placeholder="myproject" ng-model="master.current.project">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-md-6">
        <div class="form-group">
          <label>Username:</label>
          <input type="text" class="form-control" placeholder="username" ng-model="master.current.username">
        </div>
      </div>
      <div class="col-xs-12 col-md-6">
        <div class="form-group">
          <label>Password</label>
          <input type="password" class="form-control" placeholder="password" ng-model="master.current.password">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-md-6">
        <div class="form-group">
          <label>Status</label>
          <input type="text" class="form-control" placeholder="New" ng-model="master.current.status">
        </div>
      </div>
      <div class="col-xs-12 col-md-6">
        <div class="form-group">
          <label>Backlog</label>
          <input type="radio" ng-model="master.current.from" value="backlog" ng-init="master.current.from = 'backlog'" />
          <label>Sprint</label>
          <input type="radio" ng-model="master.current.from" value="sprint" />
          <label>All</label>
          <input type="radio" ng-model="master.current.from" value="all" />
        </div>
      </div>
    </div>
    <button class="btn btn-default" ng-click="master.current.load()">Load stories</button>
  </form>
</div>

<!-- Screen after static process was completed -->
<div ng-if="master.current.loaded">
  <div class="row form-group">
    <div class="col-xs-6 col-xs-offset-3 col-md-6 col-md-offset-3">
      <button class="btn btn-default btn-block" ng-click="master.current.reload()" ga-track-event="master.current.event">Reload</button>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-4">
      <div class="list-group issue-list">
        <a  ng-repeat="story in master.current.stories track by story.id" class="selectable list-group-item" ng-class="{active: master.current.story == story}" ng-click="master.current.story = story">
          {{ story.ref }}: {{ story.subject }}
        </a>
      </div>
    </div>
    <div class="col-xs-8">
      <div class="row">
        <div class="col-xs-11">
          <h2><a href="{{ master.current.base_url }}/project/{{ master.current.project }}/us/{{ master.current.story.ref }}" target="_blank">{{ master.current.story.ref }}</a>: {{ master.current.story.subject }}</h2>
        </div>
        <div class="col-xs-1">
          <h3>{{ master.current.story.status_extra_info.name }}</h3>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">
          {{ master.current.story.owner_extra_info.full_name_display }} created story on {{ master.current.story.created_date | date : "medium" }}
        </div>
        <div class="panel-body" style="white-space: pre-line" ng-bind-html="master.current.story.description"></div>
      </div>

      <button class="btn btn-default" ng-click="master.startPoll(master.current.story.ref + ' ' + master.current.story.subject, master.current.story.description, master.current.base_url + '/project/' + master.current.project + '/us/' + master.current.story.ref)">Start</button>
      <button class="btn btn-default" ng-click="master.stopPoll()">Stop</button> 
    </div>
  </div>
</div>
