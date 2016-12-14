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

                    <% include PresentationPage_NavSteps Step=3 %>
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
                        <p>IMPORTANT! We have added new questions to the speaker bio form. Please review the bios for all speakers before submitting.</p>
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
