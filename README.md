# ScrumOnline

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/scrumonline/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

PHP web app for planning poker. It includes a master view for the ScrumMaster and a simple responsive card view for the team. A deployed demo is available at http://www.scrumpoker.online

## Idea
Everyone who ever managed a software project using scrum will have either heard of or played planning poker. It is a simple tool to determine a stories/features complexity. For the detailed process refer to https://en.wikipedia.org/wiki/Planning_poker. So far there are a couple of apps where you can select a card and display it on the screen - but none of them offer a network collaboration mode. Some of the existing web apps are nowhere near responsive or come with too overloaded UIs. I want to create a simple web app where the Scrum Master can start a named session and all team members can join the session.

## Deployment
You can find a detailed deployment how-to in the [documentation](https://github.com/Toxantron/scrumonline/blob/master/doc/Deployment.md).

## Setup
The "deployment" is a general scrum meeting where the ScrumMaster has a laptop connected to a beamer while all team members have an internet connected device (phone, tablet, laptop, ... - smartwatch would be awesome). The meeting starts with the ScrumMaster creating a named session and all team members joining that session. The beamer should now show the list of joined members.

## Estimation workflow
For every story the Scrum Master will than start a poll and each member of the session must select a card. As they select a card the main screen will show a card over their name, but without showing the number. Once everyone selected a card the main page (beamer) flips all the cards. According to planning poker it will than highlight the minimum and maximum estimation for colleagues to bring their arguments. A demonstration using the Redmine plugin is available [on youtube](https://www.youtube.com/watch?v=faRYrNz8MYw).

## Road Map
* Include vote history of previous stories at the bottom of the master view
* Statistics tab in navigation bar
* Mobile apps with watch support. Imagine voting on Android Wear or Apple Watch. Wouldn't that be cool? :D

## Contribute
If you want to contribute you can just clone the repository and follow the deployment instructions. We also offer support for [Vagrant](doc/Vagrant.md) and [Docker](doc/Docker.md). Any changes must be commited to a fork and then merged by issuing a pull request. For information on the REST API or ticketing plugins please have a look at the [wiki documentation](https://github.com/Toxantron/scrumonline/blob/master/doc/).

You can also use the [REST API](https://github.com/Toxantron/scrumonline/blob/master/doc/Developer-Documentation.md) to build a mobile for iOS or Android. In that case I am happy to link your app in the README and on the page.
