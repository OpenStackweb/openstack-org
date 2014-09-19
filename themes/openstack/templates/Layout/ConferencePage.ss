<% require themedCSS(conference) %> 


$HeaderArea

<div class="span-5">
	<p><strong>The OpenStack Summit</strong><br />$MenuTitle.XML</p>
	<ul class="navigation">
		<li><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode"><span>Overview</span></a></li>
		<% loop Menu(3) %>
		  		<li><a href="$Link" title="Go to the $Title.XML page" class="$LinkingMode"><span>$MenuTitle.XML</span></a></li>
	   	<% end_loop %>
	</ul>
	
	<% include SummitVideos %>
	<% include HeadlineSponsors %>


</div> 

<!-- News Feed -->

<div class="prepend-1 span-11" id="news-feed">

	<div class="overview">
	$Content
	<p>
	</div>

	<hr />
	<div class="span-9">
		<h2>News &amp; Updates</h2>
	</div>
	<div class="span-1 last">
		<a href="{$Link}rss" class="rss">RSS</a>
	</div>
	<hr />

	<% loop NewsItems %>
		<div class="news-item">
		<h2>$Title</h2>
		<p class="post-date">Posted: $Created.Month $Created.DayOfMonth</p>
		$Content
		</div>
	<% end_loop %>

	<!-- Be Excellent -->
	<h3>Reminder: Be Excellent</h3>
	<p>Be excellent to everyone. If you think someone is not being excellent to you at the OpenStack Summit call 512-827-8633 or email <a href="mailto:events@openstack.org">events@openstack.org.</a></p>

</div>

<!-- Important Dates -->

<div class="span-6 prepend-1 last" id="important-dates">

	$Sidebar

</div>

$GATrackingCode
