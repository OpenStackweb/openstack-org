<div class="presentation-app-body">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-3">
				<% include SpeakerSidebar ActiveLink='presentations' %>
			</div>
			<div class="col-lg-9 col-md-9">
				<div class="presentation-main-panel">
					<div class="main-panel-section">
					    <p class="pull-right"><a href="#" class="close"><span aria-hidden="true">&times;</span>
					    	<span class="sr-only">Close</span></a>
					    </p>
					    <h2>Thank you!</h2>
					</div>
					<div class="main-panel-section">
					    <p class="panel-note">Your submission has been saved and is ready for review.</p>
						<p class="panel-note">Once you've completed adding in the details, a confirmation email will be sent to you and all associated speakers/moderators. Check inbox and junk folders for an email sent from speakersupport@openstack.org.</p>
					</div>

					{$Me.PresentationSuccessText}

					<a class="btn btn-primary" href="$Link">Got It</a>

				</div>
			</div>
		</div>
	</div>
</div>
