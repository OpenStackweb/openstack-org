<% require themedCSS(conference) %> 

<% loop Parent %>
$HeaderArea
<% end_loop %>

<div class="span-5">
		<p><strong>The OpenStack Summit</strong><br />San Diego 2012</p>
	<ul class="navigation">
		<% loop Parent %>
			<li><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode"><span>Overview</span></a></li>
		<% end_loop %>
		<% loop Menu(3) %>
		  		<li><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode"><span>$MenuTitle.XML</span></a></li>
	   	<% end_loop %>
	</ul>

	
	<% loop Parent %>
		<% include HeadlineSponsors %>
	<% end_loop %>


</div> 

<!-- News Feed -->

<div class="prepend-1 span-11" id="news-feed">

	<div class="span-10 last">
		<h2>$Title</h2>
		$Content
	</div>
	
</div>

<!-- Important Dates -->

<div class="span-6 prepend-1 last" id="important-dates">

	<% loop Parent %>
		$Sidebar
	<% end_loop %>

</div>