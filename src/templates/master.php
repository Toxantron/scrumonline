<?php
include __DIR__ . "/../config.php";

?>
<!-- Headline -->
<div class="row">
  <div class="col-sm-10 col-md-11">
    <h1>{{ master.id }} - {{ master.name }}</h1>
  </div>
  <div class="hidden-xs col-sm-2 col-md-1">
    <h1>{{ master.stopwatchElapsed }}</h1>
  </div>
</div>
      
<!-- Poll control -->
<div class="row topic">
  <div class="col-xs-12">
    <ul class="nav nav-tabs">
      <li ng-class="{active: master.current == source}" ng-repeat="source in master.sources| orderBy: 'position'">
        <a class="selectable" ng-click="master.selectSource(source)">{{ source.name }}</a>
      </li>
    </ul>
    <div class="ticketing" ng-include="master.current.view">
    </div>
  </div>
</div>
  
<!-- Live poll view and statistics -->
<div class="row" ng-if="master.teamComplete">
  <div class="card-overview">
    <div ng-repeat="vote in master.votes track by vote.id">        
      <div class="card-container">
        <div class="deletable-card">
          <div class="card-flip" ng-class="{flipped: master.flipped}">
            <div class="card front" ng-class="{active: vote.active}">
              <div ng-if="vote.placed" class="inner"><span class="card-label">?</span></div>
            </div>
            <div class="card back" ng-class="{active: vote.active, confirmed: master.consensus}">
              <div class="inner"><span class="card-label" ng-bind-html="vote.value"></span></div>
            </div>
          </div>
          <div ng-if="vote.canDelete" class="delete-member remove selectable" ng-click="master.remove(vote.id)">
            <span class="glyphicon glyphicon-remove"></span>
          </div>
        </div>
        <h2 ng-bind="vote.name"></h2>
      </div>            
    </div>
  </div> 
</div>           
            
<!-- Invite and statistics -->            
<div class="row">
  <div class="hidden-xs hidden-sm col-md-5">
    <h2>Invite members</h2>
    <p>Invite members to join your session. Session id: <strong ng-bind="master.id"></strong></p>
<?php
$joinUrl = $host . "/join/";
?>
    <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=<?= urlencode($joinUrl) . "{{ master.joinFragment }}" ?>&choe=UTF-8" title="Join {{ master.id }}" />
<?php
$joinUrl = $joinUrl . "{{ master.joinFragment }}";
?>
    <p>Or send them this link: <a href="<?= $joinUrl ?>"><?= $joinUrl ?></a>
  </div>

  <!-- Team list and complete button -->
  <div class="col-xs-12 col-md-5" ng-if="!master.teamComplete">
    <h2>Team</h2>
    <il class="list-group">
      <!-- Iterate over votes as they represent members as well -->
      <li class="list-group-item" ng-repeat="member in master.votes track by member.id">{{$index + 1}}. {{member.name}}</li>
    </ul>
    <button class="btn btn-success" ng-click="master.teamComplete = true">Team complete</button>
  </div>   
    
  <!-- Statistics column -->
  <div class="col-xs-12 col-md-7" ng-if="master.teamComplete">
    <div class="panel panel-default">
      <div class="panel-heading">Statistics</div>
      <div class="panel-body">
        <p ng-hide="master.statistics">Statistics will appear as soon as the first poll is concluded!</p>
        <table class="table table-striped" ng-show="master.statistics">
          <thead>
            <tr>
              <th>Enabled</th>
              <th>Name</th>
              <th>Value</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="statistic in master.statistics | orderBy:'!enabled'">
              <td><input type="checkbox" ng-model="statistic.enabled"></td>
              <td>
                <a target="_blank" href="<?php echo $src ?>/src/controllers/statistics/{{statistic.name}}.php">
                  {{ statistic.name }}
                </a>
              </td>
              <td><span ng-show="statistic.enabled" ng-bind="statistic.value"></span></td>
            </tr>
            <tr>
              <td></td>
              <td>
                <a target="_blank" href="<?php echo $src ?>/src/controllers/statistics">
                  Want more?
                </a>
              </td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Instructions -->
<div class="row">
  <article class="col-xs-12 col-lg-10 col-lg-offset-1">
    <h3>Invite Members</h3>
    <p>
      Welcome to your planning poker session, it is time to invite members and start estimating stories and features. Your team members have three 
      options to join the session: 
      <ol>
        <li>
          <strong>Session Id:</strong> Members can join your session by entering the session id, displayed in the top left corner of the page. 
          If you chose a private session you must also tell joining members the password for this joining process.
        </li>
        <li>
          <strong>Join URL:</strong> In the bottom left of the session view, below the QR code is the URL to join the session. If you created the session 
          the URL also contains the session token, which is necessary for authorization and access control. You can send this link to your team members to
          join the session.
        </li>
        <li><strong>QR-Code:</strong> The QR-Code equals the previously mentioned join ULR. It is only a convenience feature for teams who sit in the 
          same room. Instead of typing the session id or copy&pasting the link your team members can use any QR-Code reader on their smartphones to 
          quickly join the session.
        </li>
      </ol>
    </p>
    <p>
      Independent from the method your team members pick, they all go through the <i>Join Session</i> form. After selecting a member name, and in some cases
      the session password, they are redirected to the member view of this session. They can pick anything the want as a member name, it must however be 
      unique within this session, otherwise it would not be possible to identify their votes later. In fact, it is not possible to have two members with 
      the same name. Instead both members would then simly share the same view and overwrite each others estimates in the process.
    </p>
    
    <h3>Load Stories (optional)</h3>
    <p>
      Scrumpoker Online offers integrations for GitHub and JIRA, with more plugins under development. If you would like to use either one of those, select 
      it from the tab control at the top and enter the necessary information to fetch issues from the server. Your credentials are not stored anywhere and 
      only transmitted through an encrypted connection. If you are worried you can check for yourself on GitHub or follow the instructions to deploy the 
      app on-premise.
    </p>

    <h3>First Estimation</h3>
    <p>
      To start the first estimation, enter topic and description of your feature or select an issue from the list, if you chose one of the plugins in the 
      previous step. As soon as you click <i>Start</i>, the poll begins and the stopwatch in the top right corner starts. Members of your team now see 
      title and description of the current story on their devices and can start voting.
    </p>
    <p>
      Members of your team place estimates by selecting one of the cards from their screen. After they placed a vote you will now see a card with a 
      question mark (<b>?</b>) above that members name this view. Until the poll is completed and everyone has voted, members can still change their mind. 
      They can retract their vote by pressing on the selected card again or simply select a different card. 
    </p>

    <h3>Poll Completed</h3>
    <p>
      Once every member placed his vote, the poll is closed and the cards are flipped. At this time the stopwatch also stops and shows the overall 
      estimation time for this feature. If the team directly reached a consensus, all cards are highlighted green to indicate a successful estimation. 
      Otherwise the highest and lowest estimations are highlighted in red. The team members with the highlighted cards should now explain their decision. 
      After all arguments were heared, you can simply restart the poll by clicking <i>Start</i>. This process is usually repeated until the team agres on 
      a value.
    </p>
    <p>
      After you completed the first poll, the statistics are enabled. Statistics are shown in the table on the bottom left of the session view and are
      updated with every completed poll. You can enable and disable individual values depending on your interests.
    </p>

    <h3>Wrapping up</h3>
    <p>
      Once you are finished with the tasks for your next sprint or when the meeting is over, there is no need to close the session or "sign out". Simply close
      the window and go along with the rest of your day. If you estimate reguarly in the same team constilation you can bookmark your session as well as each
      member login and return anytime.
    </p>
  </article>
</div>
