<div class="row">
	<div class="col-sm-12 center elections-alert">
		<% if NominationsAreOpen %>
			<div class="alert alert-info" role="alert">
				Nominations for Individual Board Members are now open.
			</div>
		<% else %>
			<div class="alert alert-info" role="alert">
				Nominations for Individual Board Members have closed.
			</div>
		<% end_if %>	
	</div>
</div>
<div class="row">
	<div class="col-sm-8 col-sm-push-4">
		<h1>{$Title}</h1>
		$Content
	</div>
	<% include ElectionSideMenu %>	
</div>