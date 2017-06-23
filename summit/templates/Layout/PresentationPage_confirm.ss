<% include PresentationPage_HeaderNav CurrentStep=4 %>

<div class="presentation-app-body">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <% include SpeakerSidebar ActiveLink='presentations' %>
            </div>
            <div class="col-lg-9 col-md-9">
                <div class="presentation-main-panel">
                    <div class="main-panel-section">
                        <h2>One Last Step - Confirm Your Submission</h2>

                        <p class="panel-note">Look over everything below to make sure it's to your liking. <strong>IMPORTANT!</strong>
                            You MUST push "CONFIRM MY SUBMISSION" to complete the process.</p>
                    </div>
                    <% include PresentationPreview Presentation=$Presentation %>

                    <br>
                    <div class="form-actions row">
                        <div class="col-md-4 col-xs-12 col-sm-4 ">
                            <a href="$GoBackLink" class="btn action btn go-back-action-btn confirm-back-btn">
                    	        <i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp;Go Back
                            </a>
                        </div>
                        <div class="col-md-4 col-xs-12 col-sm-4 middle">
                            &nbsp;
                        </div>
                        <div class="col-md-4 col-xs-12 col-sm-4 last">
                            <a class="action btn default-action-btn confirm-btn" href="$SuccessLink">
                                Confirm my submission&nbsp;<i class="fa fa-chevron-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>