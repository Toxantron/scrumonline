# ScrumOnline

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/scrumonline/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

PHP web app for planning poker. It includes a master view for the ScrumMaster and a simple responsive card view for the team. A deployed demo is available at http://www.scrumpoker.online

## Idea
Everyone who ever managed a software project using scrum will have either heard of or played planning poker. It is a simple tool to determine a stories/features complexity. For the detailed process refer to https://en.wikipedia.org/wiki/Planning_poker. So far there are a couple of apps where you can select a card and display it on the screen - but none of them offer a network collaboration mode. Some of the existing web apps are nowhere near responsive or come with too overloaded UIs. I want to create a simple web app where the Scrum Master can start a named session and all team members can join the session.

## Deployment
You can find a detailed deployment how-to in the [documentation](/doc/Deployment.md) or use the docker image over at [chrisns/scrumonline](https://github.com/chrisns/scrumonline).

## Setup
The "deployment" is a general scrum meeting where the ScrumMaster has a laptop connected to a beamer while all team members have an internet connected device (phone, tablet, laptop, ... - smartwatch would be awesome). The meeting starts with the ScrumMaster creating a named session and all team members joining that session. The beamer should now show the list of joined members.

## Estimation workflow
For every story the Scrum Master will than start a poll and each member of the session must select a card. As they select a card the main screen will show a card over their name, but without showing the number. Once everyone selected a card the main page (beamer) flips all the cards. According to planning poker it will than highlight the minimum and maximum estimation for colleagues to bring their arguments. A demonstration using the Redmine plugin is available [on youtube](https://www.youtube.com/watch?v=faRYrNz8MYw).

## Road Map
* Include vote history of previous stories at the bottom of the master view
* Statistics tab in navigation bar
* Mobile apps with watch support. Imagine voting on Android Wear or Apple Watch. Wouldn't that be cool? :D

## Quick Installation (docker based)

The quick installation can be done with the included Docker configurations. In the Docker configuration, three systems are initiated and booted: the HTTP service with the software Scrumonline, the required database service based on MySQL and optionally a pypmyadmin.

In order to terminate, recreate and start any running containers of Scrumonline the following commands have to be called in the Scrumonline directory:

``` bash
docker-compose down && docker-compose build --no-cache && docker-compose up
```

Please wait until the console displays `Scrumonline is now accessible. Have a nice time!` appears. The Scrumonline provided by the container can now be reached by calling the address `http://localhost:8001` in your browser.

## Contribute
If you want to contribute you can just clone the repository and follow the deployment instructions. Any changes must be commited to a fork and then merged by issuing a pull request. For information on the REST API or ticketing plugins please have a look at the [wiki documentation](https://github.com/Toxantron/scrumonline/blob/master/doc/).

You can also use the [REST API](https://github.com/Toxantron/scrumonline/blob/master/doc/Developer-Documentation.md) to build a mobile for iOS or Android. In that case I am happy to link your app in the README and on the page.
