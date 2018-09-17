# Cookies
The scrumpoker application uses two types of cookies. One is the *scrum_member_name* and the other are session tokens. Both types and their usage are explained below.

## Scrum Member Name
This cookie is set and updated everytime a member enters a name to join a session. It is used to prefill the _Name_ box the next time the user open the application. It is only a convenience feature and not necesarry for the overall functionality.

## Session Tokens
Unlike the member name cookie these cookies are necessary for the app to work. Session tokens are assigned per session and have the key _session-token-{id}_. For private sessions the token is generated from the sessions name and its password, thereby it remains valid if a new session with the same name and password is created. The token for public sessions is generated from random bytes upon creation and returned to the creator.

For private sessions every member needs the token to join and perform operations. They receive the token either through qualified invite or by supplying the password during the join-process. A qualified invite is a Join-URL that includes the token as query parameter like `"?token=XXX"`.

In public sessions the token is returned to the creator and can be passed onto members through qualified invite. In that case the member gains full privilidge, for example to remove other members. If someones joins a public session **without** the token, a member-specific token is generated. That token limits the user to view the current topic, place a vote or remove itself. It does not grant privilidge to start a poll or remove other members. 