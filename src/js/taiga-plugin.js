/*globals scrum */

// Add a plugin to load stories from local Taiga server
scrum.sources.push({
  // Fixed properties and methods
  name: "Taiga",
  position: 4,
  view: "templates/taiga_source.php",
  feedback: false,
  // Feedback call for completed poll
  completed: function(result) {
  },
  
  // Custom properties and methods
  loaded: false,
  stories: [],
  story: {},
  statuses: [],
  statuses_by_name: [],

  load: function() {
    var self = this;

    var authParameters = $.param({
      username: self.username,
      password: self.password,
    });
    if (self.base_url) {
        authParameters['base_url'] = self.base_url;
    }
    if (self.type) {
        authParameters['type'] = self.type;
    }

    self.parent.$http({
      url: '/api/taiga/auth',
      method: 'POST',
      data: authParameters,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    })
      .then(function (response) {
        var data = response.data;
        if (!data || !data.auth_token) {
          self.error = 'Taiga login failed';
          return;
        }
        self.auth_token = data.auth_token;
        var resolveParameters = $.param({
            auth_token: self.auth_token,
            project: self.project
        });
        if (self.base_url) {
            authParameters['base_url'] = self.base_url;
        }
        self.parent.$http({
          url: '/api/taiga/resolve',
          method: 'POST',
          data: resolveParameters,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
          .then(function (response) {
            var data = response.data;
            if (!data || !data.project) {
                self.error = 'Unknown project'
                return;
            }
            self.project_id = data.project;
            var queryParameters = $.param({
                auth_token: self.auth_token,
                project: self.project_id,
            });
            self.parent.$http({
              url: '/api/taiga/getStoryStatuses',
              method: 'POST',
              data: queryParameters,
              headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
              .then(function (response) {
                var data = response.data;
                if (!data) {
                    self.error = 'Can\'t fetch story statuses';
                    return;
                }
                data.forEach(function(status) {
                    self.statuses[status.id] = status;
                    self.statuses_by_name[status.name] = status;
                });
                var queryParameters = {
                    auth_token: self.auth_token,
                    project: self.project_id,
                    status_is_closed: false,
                    status_is_archived: false
                };
                if (self.from == 'backlog') {
                    queryParameters['milestone_isnull'] = true;
                }
                if (self.from == 'sprint') {
                    queryParameters['milestone_isnull'] = false;
                }
                if (self.status) {
                    if (self.statuses_by_name[self.status]) {
                        queryParameters['status'] = self.statuses_by_name[self.status].id;
                    }
                }
                queryParameters = $.param(queryParameters);
                self.parent.$http({
                  url: '/api/taiga/getStories',
                  method: 'POST',
                  data: queryParameters,
                  headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                  .then(function (response) {
                    var data = response.data;
                    if (!data) {
                        self.error = 'Can\'t load stories';
                        return;
                    }
                    self.stories = data;
                    self.story = self.stories[0];
                    self.stories.forEach(function(story) {
                        self.retrieve_description(story.id);
                    });
                  });
              });
          });
      });
  },

  retrieve_description: function(id = self.story.id) {
    var self = this;
    var queryParameters = $.param({
	auth_token: self.auth_token,
        id: id
    });
    self.parent.$http({
      url: '/api/taiga/getStory',
      method: 'POST',
      data: queryParameters,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    })
      .then(function (response) {
        var data = response.data;
        if (!data) {
            self.error = 'Can\'t load story';
            return;
        }
        var converter = new showdown.Converter();
        data.description = converter.makeHtml(data.description);
        all_loaded = true;
        self.stories.forEach(function(value, index) {
          if ( value.id == data.id ) {
            self.stories[index] = data;
            self.stories[index].loaded = true;
          }
          if ( ! self.stories[index].loaded ) {
            all_loaded = false;
          }
          });
        if (all_loaded) {
	  self.loaded = true;
          self.story = self.stories[0]
        }
      });
  },

  reload: function() {
    this.loaded = false;
  }
});
