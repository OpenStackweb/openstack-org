<% include PresentationPage_HeaderNav CurrentStep=3 %>

<div class="presentation-app-body">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <% include SpeakerSidebar ActiveLink='presentations' %>
            </div>
            <div class="col-lg-9 col-md-9">
                <div class="presentation-main-panel">
                    <div class="main-panel-section">
                        <h2>
                            <% if $Presentation.Speakers %>
                                Speakers
                            <% else %>
                                Add a Speaker
                            <% end_if %>
                        </h2>
                    </div>



                    <% if $Presentation.Speakers %>
                        <h3>Speakers/Moderators included in this presentation</h3>
                        <table class="table">
                            <tbody>
                                <% loop $Presentation.Speakers %>
                                <tr>
                                    <td class="item-name">
                                        <i class="fa fa-user"></i><a
                                            href="$EditLink($Top.Presentation.ID)">$Name</a>
                                    </td>
                                    <td>
                                        $Role
                                    </td>
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
