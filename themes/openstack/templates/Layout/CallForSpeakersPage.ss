
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

			<% if DisplayCompletedMessage %>

			<div class="siteMessage" id="SuccessMessage">
	        	<p><strong>You have successfully submitted your presentation for voting and review by our track chairs.</strong></p>
	   		</div>

	   		<p>You will receive an email when a decision is reached about including your presenation in the OpenStack Summit. Thank you so much for your submission. Good luck!</p>

	   		<hr/>

		<% end_if %>

		<% if CurrentMember %>
			<h1>Your Presentation Submissions for $Parent.MenuTitle.XML</h1>
			<% if PastSubmissionDeadline %>
				<p>(Unfortunately, the deadline to submit new speaking sessions for this summit <strong>has passed</strong>. You may still make minor edits to the presentations you've already submitted below.)</p>
			<% end_if %>

			<% if TalkToDeleteConfirmed %>
				<p classs="siteMessage" id="InfoMessage">Are you sure you want to delete the presentation? <a href="{$Top.Link}DeleteTalk/{$TalkToDeleteConfirmed}" class="roundedButton">Ok, Delete</a> &nbsp; <a href="{$Link}CancelDelete" class="roundedButton">Cancel</a></p>

			<% end_if %>

			<% if MemberTalks %>
				<ul class="talks">
				<% loop MemberTalks %>
					<li><a href="{$Top.Link}TalkDetails/{$ID}">$PresentationTitle</a> <a class="delete-button" href="{$Top.Link}DeleteTalk/{$ID}">Delete</a></li>
				<% end_loop %>
				</ul>
				<p>
					<% if PastSubmissionDeadline %>
					<% else %>
						<a href="{$Top.Link}TalkDetails/" class="roundedButton">Add Another Submmission</a>
					<% end_if %>
					&nbsp; <a href="{$Top.Link}Logout/" class="roundedButton">Log Out</a>
				</p>
			<% else %>
				<% if PastSubmissionDeadline %>
					<p class="message" id="InfoMessage">Unfortunately, the deadline to submit speaking sessions for this summit <strong>has passed</strong>.</p>
				<% else %>
					<p>You haven't created any speaker submissions yet.</p>
					<p><a href="{$Top.Link}TalkDetails/" class="roundedButton">Create Submission</a>
				<% end_if %>
			<% end_if %>
		<% else %>
			<h1>Would you like to speak at the OpenStack Summit?</h1>	
			<h2>Create &amp; Edit Speaker Submissions</h2>
			<p>Please login or create an OpenStack.org site login below. You do not have to be a member of the OpenStack Foundation to create or edit speaking submissions. See a list of <a href="{$Top.Link}#ProposedTracks">Proposed Summit Tracks</a>.</p>

			<% if PastSubmissionDeadline %>
					<p class="message" id="InfoMessage"><strong>Welcome! You can edit your existing presentations by logging in below.</strong> <br/> (Unfortunately, the deadline to submit new speaking sessions for this summit has passed.)</p>
			<% else %>
				<p class="message" id="InfoMessage">The deadline to submit speaking sessions for the $Parent.MenuTitle.XML is <strong>$SubmissionDeadline.Day $SubmissionDeadline.Month {$SubmissionDeadline.DayOfMonth}, {$SubmissionDeadline.Year}</strong>.</p>
			<% end_if %>

            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <h3>Have a login for the OpenStack website?</h3>
                    <% include LoginForm %>
                    <p><a href="/Security/lostpassword">I don't think I ever had a password.</a></p>
                    <hr/>
                    <p>If you are having any issues resetting your password, logging in, or updating your presentations, <a href="mailto:events@openstack.org">ask us</a> and we'll be happy to help!</p>
                </div>
                <div class="col-lg-6 col-md-6">
                    <% if PastSubmissionDeadline %>
                    <% else %>
                    <h3>Don't have a login? Start here.</h3>
                    $RegisterForm
                    <% end_if %>
                </div>
                
			</div>
			

				<p></p>
				<hr/>
				<a name="ProposedTracks"></a>
				<h2>Proposed Summit Tracks</h2>
				<ul>

					<% loop CurrentSummit.SummitCategories %>
						<li>$Description</li>
					<% end_loop %>
					
				</ul>


		<% end_if %>

		</div>

	</div>
</div>

$GATrackingCode
