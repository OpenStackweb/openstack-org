<div class="presentation-app-body">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <% include SpeakerSidebar ActiveLink='presentations' %>
            </div>
            <div class="col-lg-9 col-md-9">
                <div class="presentation-main-panel">
                    <div class="main-panel-section">
                        <h2><% if $Presentation.Speakers %>Presentation Speakers<% else %>Add your first
                            speaker<% end_if %></h2>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="presentation-steps">
                                <a href='$Presentation.EditLink' class="step"><i class="fa fa-file-text-o"></i>&nbsp;Presentation&nbsp;Summary</a>
                                <a href='$Presentation.EditTagsLink' class="step"><i class="fa fa-tags"></i>&nbsp;Presentation&nbsp;Tags</a>
                                <a href='$Presentation.EditSpeakersLink'class="step active"><i class="fa fa-user"></i>&nbsp;Speakers</a>
                            </div>
                        </div>
                    </div>

                    <% if $Presentation.Speakers %>
                        <h3>Speakers included in this presenation</h3>
                        <table class="table">
                            <tbody>
                                <% loop $Presentation.Speakers %>
                                <tr>
                                    <td class="item-name"><i class="fa fa-user"></i><a
                                            href="$EditLink($Top.Presentation.ID)">$Name</a></td>
                                    <td class="action">
                                        <% if $Top.Presentation.CanRemoveSpeakers %>
                                            <a class='delete-speaker'
                                               href="$DeleteLink($Top.Presentation.ID)">Remove</a>
                                        <% end_if %>
                                    </td>
                                </tr>
                                <% end_loop %>
                            </tbody>
                        </table>
                    <% end_if %>
                    $AddSpeakerForm
                </div>
            </div>
        </div>
    </div>
</div>
