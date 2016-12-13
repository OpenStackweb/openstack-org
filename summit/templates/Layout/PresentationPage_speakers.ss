<div class="presentation-app-body">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <% include SpeakerSidebar ActiveLink='presentations' %>
            </div>
            <div class="col-lg-9 col-md-9">
                <div class="presentation-main-panel">
                    <div class="main-panel-section">
                        <h2><% if $Presentation.Speakers %>Presentation Speakers<% else %>Add a
                            speaker<% end_if %></h2>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="presentation-steps">
                                <a href='$Presentation.EditLink' class="step">1.&nbsp;Presentation&nbsp;Summary&nbsp;&nbsp;<i class="fa fa-file-text-o"></i></a>
                                <a href='$Presentation.EditTagsLink' class="step">2.&nbsp;Presentation&nbsp;Tags&nbsp;&nbsp;<i class="fa fa-tags"></i></a>
                                <a href='$Presentation.EditSpeakersLink' class="step active">3.&nbsp;Speakers&nbsp;&nbsp;<i class="fa fa-user"></i></a>
                            </div>
                        </div>
                    </div>
                    <% if $Presentation.Moderator %>
                        <h3>Moderator for this presentation</h3>
                        <table class="table">
                            <tbody>
                                <% with $Presentation.Moderator %>
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
                                <% end_with %>
                            </tbody>
                        </table>
                    <% end_if %>

                    <% if $Presentation.Speakers %>
                        <h3>Speakers included in this presentation</h3>
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
                        <p>Please Note: We have added new questions to the speaker bio form. Please review the bios for all speakers before submitting.</p>
                    <% end_if %>
                    $AddSpeakerForm
                </div>
            </div>
        </div>
    </div>
</div>
