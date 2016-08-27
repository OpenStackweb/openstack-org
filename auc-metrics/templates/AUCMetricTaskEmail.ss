<html>
<body>
<p>There were some problems with AUC Metrics task:</p>
<% loop $Services %>
<h3>$Title</h3>
<ul>
	<% loop $Errors %>
	<li>$Title</li>
	<% end_loop %>
</ul>
<% end_loop %>

