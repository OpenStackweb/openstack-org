<% if Menu(2) %>
	<div id="subnav" class="span-5">
		<% include SubMenu %>
	</div>
	<div class="span-19 last">
<% else %>
	<div class="span-24 last">
<% end_if %>

$Content
$Form

</div>
