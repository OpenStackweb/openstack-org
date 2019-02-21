<% include PresentationPage_HeaderNav CurrentStep=1 %>

<div class="presentation-app-body">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <% include SpeakerSidebar ActiveLink='presentations' %>
            </div>
            <div class="col-lg-9 col-md-9">
                <div class="presentation-main-panel">
                    <div class="main-panel-section">
                        <h2>{$HeaderTitle}</h2>
                    </div>
                    $PresentationForm
                </div>
            </div>
        </div>
    </div>
</div>
