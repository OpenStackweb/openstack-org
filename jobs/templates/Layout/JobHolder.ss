<div class="row">
	<div class="center jumbotron">
		<div class="jobHeader">
			<h1>OpenStack Job Board</h1>
			<h5>Join the best OpenStack-related jobs board for free!</h5>
			<div class="postButton">
				<a href="$PostJobLink">Post job Now</a>
			</div>
		</div>
	</div>
</div>
<div class"containter jobCheck">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
		    <h2>Check the latest job postings</h2>
		</div>
	</div>
	<div class="filters">		
		<div class="col-md-2 col-md-offset-3">			
			<div class="form-group has-feedback search">
				<label class="control-label"></label>
				<input type="text" class="form-control" placeholder="Keywords">
				<i class="glyphicon glyphicon-search form-control-feedback"></i>
			</div>
		</div>	
		<div class="col-md-2">	
			<div class="form-group dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Filter Job Types
					<span class="glyphicon glyphicon-chevron-down"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="#">test</a></li>
					<li><a href="#">test2</a></li>
					<li><a href="#">test3</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">Separated link</a></li>
				</ul>
			</div>
		</div>			
		<div class="col-md-2">	
			<div class="form-group dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown">Sort Results By
					<span class="glyphicon glyphicon-chevron-down"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="#">test</a></li>
					<li><a href="#">test2</a></li>
					<li><a href="#">test3</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">Separated link</a></li>
				</ul>
			</div>			
		</div>		
	</div>
</div>
	<div>
		<div class="job_list">
		    $getDateSortedJobs
		</div>
	</div>
</div>
