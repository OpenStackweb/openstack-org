<% require themedCSS(conference) %> 



<div class="container summit">

	<% with Parent %>
	$HeaderArea
	<% end_with %>
	
  <div class="row">
		<div class="col-lg-3 col-md-3 col-sm-3">
			<p><strong>The OpenStack Summit</strong><br />$MenuTitle.XML</p>

				<div class="newSubNav">
				    <ul class="overviewNav">

						<% loop Parent %>
							<li id="$URLSegment"><a href="$Link" title="Go to the $Title.XML page"><span>Overview</span> <i class="fa fa-chevron-right"></i></a></li>
						<% end_loop %>

				        <% loop Menu(3) %>
				            <li id="$URLSegment"><a href="$Link" title="Go to the &quot;{$Title}&quot; page"  class="$LinkingMode">$MenuTitle <i class="fa fa-chevron-right"></i></a></li>
				        <% end_loop %>
				    </ul>
				</div>
			<% with Parent %>
				<% include SummitVideos %>
				<% include HeadlineSponsors %>
			<% end_with %>


		</div> 

		<!-- News Feed -->

		<div class="col-lg-9 col-md-9 col-sm-9" id="news-feed">

            <h1>Would you like to be in the Speaker's Bureau?</h1>

            <p><strong>The OpenStack Speaker’s Bureau</strong> is a list of worldwide community members who are subject matter experts. From time to time, we call on members of the Speaker’s Bureau to help present at the dozens of OpenStack meetups and industry conferences held annually. Would you be willing to join the Speaker’s Bureau?</p>

            <p>There’s no obligation, but you my occasionally be emailed regarding speaking opportunities in your area.</p>

            $SpeakerBureauForm

		</div>

	</div>
</div>

$GATrackingCode
