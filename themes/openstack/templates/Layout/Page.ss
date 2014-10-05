<div class="container">
	<% if Menu(2) %>
		<div class="row">
			<div class="col-lg-3">
				<% include SubMenu %>
			</div>
			<div class="col-lg-9">
	<% end_if %>

	$Content
	$Form

	<% if Menu(2) %>
		</div> <!-- Close content div -->
		</div> <!-- Close row div -->
	<% end_if %>
</div>