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
                            <div class="col-lg-6 col-md-6">
                                <h2>Presentations</h2>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <% if $Top.isCallForSpeakerOpen %>
                                    <% if $Top.isPresentationSubmissionAllowed  %>
                                        <a href="$Link('manage/new')" class="btn btn-success add-presentation-button">Add New Presentation</a>
                                    <% else %>
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>Warning!</strong> You reached presentations submissions limit.
                                        </div>
                                    <% end_if %>
                                <% else %>
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <strong>Warning!</strong> Call for Speakers is closed!.
                                    </div>
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
                                    <td class="item-name">
                                        <i class="fa fa-file-text-o"></i>
                                        <% if $Top.canEditPresentation($ID) %>
                                            <a href="$EditLink">
                                        <% end_if %>
                                        <% if $Title %>$Title<% else %>$ID<% end_if %>
                                        <% if $Top.canEditPresentation($ID) %>
                                            </a>
                                        <% end_if %>
                                    </td>
                                    <% if $Status %>
                                        <td class="status"><i class="fa fa-tag"></i> $Status</td>
                                    <% else %>
                                        <td class="status"></td>
                                    <% end_if %>
                                    <td class="action">
                                        <% if $CanDelete && $Top.canEditPresentation($ID) %>
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
                                    <td class="item-name">
                                        <i class="fa fa-file-text-o"></i>
                                        <% if $Top.canEditPresentation($ID) %>
                                            <a href="$EditLink">
                                        <% end_if %>
                                            <% if $Title %>$Title<% else %>$ID<% end_if %>
                                        <% if $Top.canEditPresentation($ID) %>
                                            </a>
                                        <% end_if %>
                                    </td>
                                    <td class="status"><i class="fa fa-tag"></i> $Status</td>
                                    <td class="action">
                                        <% if $CanDelete && $Top.canEditPresentation($ID) %><a href="$DeleteLink">Delete</a><% end_if %>
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