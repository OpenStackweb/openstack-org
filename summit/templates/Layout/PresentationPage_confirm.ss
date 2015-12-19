<div class="presentation-app-body">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-3">
				<% include SpeakerSidebar ActiveLink='presentations' %>
			</div>
			<div class="col-lg-9 col-md-9">
				<div class="presentation-main-panel">
					<div class="main-panel-section">
						<h2>Confirm Your Submission Details</h2>
						<p class="panel-note">Look over everything below to make sure it's to your liking.</p>
						<p class="panel-note"><strong>IMPORTANT!</strong> You MUST push "CONFIRM MY SUBMISSION to complete the process.</p>
					</div>
										
					<div class="panel-buttons">
					    <a class="btn btn-primary" href="$SuccessLink" style="color:#009000;">Confirm my submission <i class="fa fa-arrow-right fa-end"></i></a>
					    <a class="btn btn-primary" href="$Presentation.EditLink" style="color:#d1d307;">Make changes</a>
				    </div>
									
                    <% include PresentationPreview Presentation=$Presentation %>

				</div>
			</div>
		</div>
	</div>
</div>