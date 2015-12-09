<div class="presentation-app-body">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <% include SpeakerSidebar ActiveLink='presentations' %>
            </div>
            <div class="col-lg-9 col-md-9">
                <div class="presentation-main-panel">
                    <div class="main-panel-section">
                        <div class="row">
                            <div class="col-lg-8 col-md-8">
                                <h2>Presentations</h2>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <% if $Top.Summit.isCallForSpeakersOpen && $CurrentMember.SpeakerProfile.canAddMorePresentations($Top.Summit.ID) %>
                                    <a href="$Link('manage/new')" class="btn btn-success add-presentation-button">Add New Presentation</a>
                                    <p class="max-presentation-notice">** Speakers are limited to a total of five presentations submissions, whether submitted by them or on their behalf.</p>
                                <% end_if %>
                            </div>
                        </div>
                    </div>
                    <h3>Presentations <strong>You</strong> Submitted</h3>
                    <table class="table">
                        <tbody>
                            <% if $CurrentMember.SpeakerProfile.MyPresentations($Top.Summit.ID) %>
                                <% loop $CurrentMember.SpeakerProfile.MyPresentations($Top.Summit.ID) %>
                                <tr>
                                    <td class="item-name"><i class="fa fa-file-text-o"></i><a
                                            href="$EditLink">$Title</a></td>
                                    <% if $Status %>
                                        <td class="status"><i class="fa fa-tag"></i> $Status</td>
                                    <% else %>
                                        <td class="status"></td>
                                    <% end_if %>
                                    <td class="action">
                                        <% if $CanDelete %>
                                            <a data-confirm="Whoa, there..."
                                               data-confirm-text="Are you sure you want to delete this presentation?"
                                               data-confirm-ok="Yup. Get rid of it."
                                               data-confirm-cancel="Nope. My bad."
                                               data-confirm-color="#b80000"
                                               href="$DeleteLink">Delete</a>
                                        <% end_if %>
                                    </td>
                                </tr>
                                <% end_loop %>
                            <% else %>
                            <tr>
                                <td><i>You have not submitted any presentations.</i></td>
                            </tr>
                            <% end_if %>
                        </tbody>
                    </table>
                    <h3>Presentations <strong>Others</strong> Submitted With You As A Speaker</h3>
                    <table class="table">
                        <tbody>
                            <% if $CurrentMember.SpeakerProfile.OtherPresentations($Top.Summit.ID) %>
                                <% loop $CurrentMember.SpeakerProfile.OtherPresentations($Top.Summit.ID) %>
                                <tr>
                                    <td class="item-name"><i class="fa fa-file-text-o"></i><a
                                            href="$EditLink">$Title</a></td>
                                    <td class="status"><i class="fa fa-tag"></i> $Status</td>
                                    <td class="action"><% if $CanDelete %><a href="$DeleteLink">Delete</a><% end_if %>
                                    </td>
                                </tr>
                                <% end_loop %>
                            <% else %>
                            <tr>
                                <td><i>There are no presentations submitted by others with you as a speaker.</i></td>
                            </tr>
                            <% end_if %>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>