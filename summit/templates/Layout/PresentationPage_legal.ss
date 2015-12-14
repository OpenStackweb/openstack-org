<div class="presentation-app-body">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <% include SpeakerSidebar ActiveLink='presentations' %>
            </div>
            <div class="col-lg-9 col-md-9">
                <div class="presentation-main-panel">
                    <% if VideoLegalConsent %>
                        <div class="main-panel-section">
                            <h2>Speaker Video Consent</h2>
                        </div>
                        <p class="panel-note"><strong>Note:</strong>{$Top.VideoLegalConsent}</strong></p>
                        <p><strong>Please read the terms below and indicate that you agree to be recorded.</strong></p>
                    <% end_if %>
                    $LegalForm
                </div>
            </div>
        </div>
    </div>
</div>