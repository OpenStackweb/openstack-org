<div class="presentation-app-body">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <% include SpeakerSidebar ActiveLink='presentations' %>
            </div>
            <div class="col-lg-9 col-md-9">
                <div class="presentation-main-panel">
                    <div class="main-panel-section">
                        <% if $Presentation.exists %>
                            <h2>Edit Your Presentation</h2>
                        <% else %>
                            <h2>Add New Presentation</h2>
                        <% end_if %>
                    </div>
                    <% include PresentationPage_NavSteps Step=1 %>
                    $PresentationForm
                </div>
            </div>
        </div>
    </div>
</div>
