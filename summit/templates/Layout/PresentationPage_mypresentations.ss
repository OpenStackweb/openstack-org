<div class="content-container unit size3of4 lastUnit">
	<article>
		<h2>My Presentations</h2>
		<ul>
			<% loop $Presentations %>
			<li><a href="$Link">$Title</a> (<a href="$DeleteLink">delete</a>)</li>
			<% end_loop %>
		</ul>
		
	</article>
</div>