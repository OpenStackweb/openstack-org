require('./login.tag')

// Login API
var auth = riot.observable()

auth.login = function(params) {
  auth.trigger('login')
}

riot.mount('login', auth)