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
<div class="containter jobCheck">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
		    <h2>Check the latest job postings</h2>
		</div>
	</div>
	<div class="filters">		
		<div class="col-md-2 col-md-offset-3">			
			<div class="form-group has-feedback search">
				<label class="control-label"></label>
				<input id="txt-keywords" type="text" class="form-control" placeholder="Keywords">
                <i class="glyphicon glyphicon-remove form-control-feedback clear-btn" style="display: none"></i>
				<i class="glyphicon glyphicon-search form-control-feedback search-btn"></i>
         	</div>
		</div>	
		<div class="col-md-2">	
			<div class="form-group dropdown">
				<button id="filter_by_type" class="btn btn-default dropdown-toggle" type="button" id="job_types" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    Filter Job Types
					<span class="glyphicon glyphicon-chevron-down"></span>
				</button>
				<ul id="ddl-menu-types" class="dropdown-menu">
                    <li><a data-type-id="0" data-target="#">Filter Job Types</a></li>
					<% loop JobTypes %>
                        <li><a data-type-id="{$ID}" data-target="#">$Type</a></li>
					<% end_loop %>
				</ul>
			</div>
		</div>			
		<div class="col-md-2">
			<div class="form-group dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="sort_by" data-toggle="dropdown">Sort Results By
					<span class="glyphicon glyphicon-chevron-down"></span>
				</button>
				<ul class="dropdown-menu" id="ddl-menu-sort" aria-labelledby="dropdownMenu1">
                    <li><a data-sort-by="" data-target="#">Sort Results By</a></li>
					<li><a data-sort-by="coa" data-target="#">Is COA Needed</a></li>
                    <li><a data-sort-by="foundation" data-target="#">Is Foundation Job</a></li>
					<li><a data-sort-by="company" data-target="#">Company</a></li>
					<li><a data-sort-by="location" data-target="#">Location</a></li>
                    <li><a data-sort-by="posted" data-target="#">Posted Date</a></li>
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
