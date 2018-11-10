# Instructions
This page has detailed instructions how to use the app. It is written for the perspective of the session owner, which will be the Scrum Master or Product Owner in most cases, but also includes instructions for the team members.

## Setup a Session
The first step is to setup a new session using the Create Session from on the front page. Pick a name for your session. It does not have to be unique or special, it is mostly for the overview of running sessions and as a title for your session view. Next select a card set for your session from the drop-down box. Those are the card-sets that are most popular and some were contributed by other users. If you need a different card set just click the question mark next to the drop-down to navigate to the file and open a pull request on GitHub.

After setting the name and card set for your session, you can choose to declare your session as private. Unlike public sessions they can only be observed if spectators provide the password. Private sessions also require joining members to either join through invite or session id and password. Once you completed the form press Create and you will be redirected to your new session.

## Invite Members
After you created the session, it is time to invite members and start estimating stories. Your team members have three options to join the session:

1. **Session Id:** Members can join the session by entering the session id, displayed in the top right corner, on the main page. If you choose a private session you must also tell joining members the password for this joining process.
2. **Join URL:** In the bottom left of the session view, below the QR code is the URL to join the session. If you created the session the URL also contains the session token, which is necessary for authorization and access control. You can send this link to your team members to join the session.
3. **QR-Code:** The QR-Code equals the previously mentioned join ULR. It is only a convenience feature for teams who sit in the same room. Instead of typing the session id or copy&pasting the link your team members can use any QR-Code reader on their smartphones to quickly join the session.

Independent from the method your team members pick, they all go through the Join Session form. After entering a member name, and in some cases the session password, they are redirected to the member view of your session. They can pick anything the want as a member name, it must however be unique within your session, otherwise it would not be possible to identify their votes later. In fact, it is not possible to have two members with the same name. Instead both members would then simly share the same view and overwrite each others vote in the process.

The member view is optimized for mobile devices. It displays the title and description of the current poll at the top and all cards from the selected card set below. At the bottom of the page there is also a short explaination of the voting process.

## Load Stories (optional)
Scrumpoker Online offers integrations for GitHub and JIRA, with more plugins under development. If you would like to use either one of those, select it from the tab control at the top and enter the necessary information to fetch issues from the server. Your credentials are not stored anywhere and only transmitted through an encrypted connection. If you are worried you can check for yourself on GitHub or follow the instructions to deploy the app on-premise.

## First Estimation
To start the first estimation, enter topic and description of your feature or select an issue from the list, if you chose one the plugins in the previous step. As soon as you click Start, the poll begins and the stopwatch in the top right corner starts. Members of your team now see title and description of the current story on their devices and can start voting.

Members of your team place votes by selecting on of the cards from their screen. The card is highlighted red to indicate the server is processing the vote. Once the vote was placed successfully the card is highlighted green. You will now see a card with a question mark (?) above that members name in your session view. Until the poll is completed and everyone has voted, members can still change their mind. They can retract their vote by pressing on the selected card again or simply select a different card.

## Poll Completed
Once every member placed his vote, the poll is closed and the cards are flipped. At this time the stopwatch also stops and shows the overall estimation time. If the team directly reached a consensus, all cards are highlighted green to indicate a successful estimation. Otherwise the highest and lowest estimations are highlighted in red. The team members with the highlighted cards should now explain their decision. After all arguments were heared, you can simply restart the poll by clicking Start. This process is usually repeated until the team agres on a value.

After you completed the first poll, the statistics are enabled. Statistics are shown in the table on the bottom left of the session view and are updated with every completed poll. You can enable and disable individual values depending on your interests.

## Wrapping up
Once you are finished with the tasks for your next sprint or when the meeting is over, there is no need to close the session or "sign out". Simply close the window and go along with the rest of your day. If you estimate reguarly in the same team constilation you can bookmark your session as well as each member login and return anytime.