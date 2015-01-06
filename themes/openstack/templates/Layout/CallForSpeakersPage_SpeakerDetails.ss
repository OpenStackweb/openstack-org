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

					<% if CurrentMember %>
			<h1>Edit The Speaker Details</h1>
			$SpeakerDetailsForm
		<% else %>
			<p>You must be logged in as a member to create or edit speaker submissions.</p>

			<div class="span-9">
			<h3>Already have a login for the OpenStack website?</h3>
			<% include LoginForm %>
			</div>
			
			<div class="span-9 last">
			<h3>Don't have a login? Start here.</h3>
			$RegisterForm
			</div>

		<% end_if %>


		</div>

	</div>
</div>

$GATrackingCode