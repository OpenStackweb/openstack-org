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

                        <h2>Hello $ConfirmedSpeaker.FirstName $ConfirmedSpeaker.Surname! Thanks for confirming your availability to speak at the Paris Summit.</h2>

		<p><strong>ONE MORE STEP: To help ensure great communication and coordination, please provide a phone number that we can reach you at while onsite at the Summit.</strong></p>

		$OnsitePhoneForm
                

		</div>

	</div>
</div>

$GATrackingCode