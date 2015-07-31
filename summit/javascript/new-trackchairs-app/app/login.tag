<login>
  <form onclick="{ login }">
    <input name="username" type="text" placeholder="username">
    <input name="password" type="password" placeholder="password">
  </form>

  login() {
    console.log("loggin in...")
    opts.trigger('login')
  }

  // any tag on the system can listen to login event
  opts.on('login', function() {
    console.log("logged in")
  })
</login>