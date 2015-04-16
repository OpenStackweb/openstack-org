<div class="row">
	<div class="promoted-content">
		<% loop LatestSectionLinks %>
			<% if First %>
				<div class="col-sm-6">
			<% end_if %>
			<a href="$Link" target="_top">
				$Preview
			</a>
			<% if Mid %>
				</div><div class="col-sm-6">
			<% end_if %>
			<% if Last %>
				</div>
			<% end_if %>
		<% end_loop %>
	</div>
</div>