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

                    <% if $Presentation.exists %>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="presentation-steps">
                                    <a href='$Presentation.EditLink' class="step active">1.&nbsp;Presentation&nbsp;Summary&nbsp;&nbsp;<i class="fa fa-file-text-o"></i></a>
                                    <a href='$Presentation.EditTagsLink' class="step">2.&nbsp;Presentation&nbsp;Tags&nbsp;&nbsp;<i class="fa fa-tags"></i></a>
                                    <a href='$Presentation.EditSpeakersLink' class="step">3.&nbsp;Speakers&nbsp;&nbsp;<i class="fa fa-user"></i></a>
                                </div>
                            </div>
                        </div>
                    <% end_if %>

                    $PresentationForm
                </div>
            </div>
        </div>
    </div>
</div>
