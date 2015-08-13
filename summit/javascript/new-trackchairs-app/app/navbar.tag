<navbar>
	<nav class="navbar navbar-default">
	  <div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand" href="#">OpenStack Track Chairs App </a>
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <ul class="nav navbar-nav">

	        <li class="{ active: self.parent.DisplayMode === 'tutorial' }"><a href="#" onclick="{ setMode('tutorial') }">Tutorial</a></li>		      
	        <li class="{ active: self.parent.DisplayMode === 'browse' }"><a href="#" onclick="{ setMode('presentations') }">Browse Presentations <span class="sr-only">(current)</span></a></li>
	        <li class="{ active: self.parent.DisplayMode === 'selections' }"><a href="#" onclick="{ setMode('selections') }">Your Selections</a></li>
	        <li class="{ active: self.parent.DisplayMode === 'directory' }"><a href="#" onclick="{ setMode('directory') }">Chair Directory</a></li>
	        <li class="{ active: self.parent.DisplayMode === 'requests' }" show="{ opts.admin }"><a href="#" onclick="{ setMode('requests') }">Change Requests</a></li>        

	      </ul>

	      

	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>
	
	self = this;

	setMode(mode) {
		return function(e) {
			riot.route(mode)
		}
	}

</navbar>