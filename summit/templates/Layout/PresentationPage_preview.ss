<div class="presentation-app-body">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <% include SpeakerSidebar ActiveLink='presentations' %>
            </div>
            <div class="col-lg-9 col-md-9">
                <div class="presentation-main-panel">
                    <div class="main-panel-section">
                        <h2>Preview Your Submission</h2>
                    </div>
                    <% include PresentationPreview Presentation=$Presentation %>
                    <div class="row confirm-actions">
                        <div class="col-md-12">
                            <p><a class="btn btn-primary" href="$Link()">Done</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>