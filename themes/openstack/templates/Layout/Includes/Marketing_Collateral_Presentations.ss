<h2>Collateral + Presentations</h2>
<hr>
<div class="item-list row">
		<% loop LatestPresentations %>
			<% if First %>
				<div class="col-sm-6">
			<% end_if %>
			
			<div class="item" title="$Name">
				<div class="">
					<a href="$FileURL" target="_blank">
						<img width="22" height="22" src="$Icon" alt="$Name">
					</a>
				</div>
				<div class="item-content">
					<a href="$FileURL" target="_blank">$Name</a>
				</div>
			</div>
			<% if Mid %>
				</div><div class="col-sm-6 last">
			<% end_if %>		
			<% if Last %>
				</div>
			<% end_if %>
		<% end_loop %>
</div>