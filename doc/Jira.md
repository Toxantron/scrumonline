# Jira integration

It is possible to import stuff from Jira. Below you can find simple tutorial, how to do it.

# 0. Enable Jira Integration

Valid only if you have your standalone copy of the tool!

* open `src/config.php`
* change `$plugins` value to contain `Jira`, for example:
```php
$plugins = [
    'GitHub',
    'Jira',
];
```

# 1. Start planning

Start session as a Scrum Master. Then in the menu you should see `Jira` option. Click on it.

# 2. Fill in your JIRA information

To import issues from Jira, you have to pass some configuration information:

* `JIRA base url` - your JIRA installation address
* `Project` - JIRA project, from which you want to import issues. Use shortname.
* `Username` - your JIRA username
* `Password` - your JIRA password
* `JQL` - (optional) if you want to select with extra criterias, feel free to change this value. By default it contains `issuetype=story and status=backlog`, which means that only stories from backlog will be imported.

Imported issues are sorted by priority.

# 3. Load issues

Just click on `Load issues` button. That's it!

# 4. (additional) Reload issues

During planning you might want to add some new issues to JIRA. Feel free to do it! Later you can click on `Reload` button on the top of issues list. Config form will appear again, then click `Load issues` and all new stuff should be visible now!

# 5. (additional) Hardcode your JIRA configuration

Valid only if you have your standalone copy of the tool!

It might be boring to type all the credentials over and over again. You can hardcode it in your application config.

* open `src/config.php`
* find `$jiraConfiguration` and fill in with values
* if you can not find it, just copy the code below, paste to `src/config.php` and fill in with your values:
```php
$jiraConfiguration = [
    'base_url' => '',
    'username' => '',
    'password' => '',
    'project' => '',
    'jql' => '',
];
```

Even if you hardcode your JIRA configuration, you can still override i.e. project during planning. Just fill in project name in `Project` field - it will override value from `$jiraConfiguration` variable.
