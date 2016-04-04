    $SetCurrentTab(8)
    <% require themedCSS(profile-section) %>
    <h1>$Title</h1>
    <% if CurrentMember %>
        <% include CurrentUserInfoBox LogOutLink=$Top.LogoutUrl, ResignLink=$Top.ResignUrl %>
        <% include ProfileNav %>
        <% if $Top.ActiveSummit %>
            <h2>$Top.ActiveSummit.Title Summit Attendee Registration Info</h2>
            <a href="#" data-toggle="modal" data-target="#myModal">Where is the Evenbrite Order # ?</a>
            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Where is the Evenbrite Order # ?</h4>
                        </div>
                        <div class="modal-body">
                            <img src="summit/images/ticket_sample.png" class="ticket-sample"/>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>

                        </div>
                    </div>
                </div>
            </div>
            $SummitAttendeeInfoForm
        <% else %>
            <p>There is not current summit yet!</p>
        <% end_if %>

    <% else %>
        <p>In order to edit your community profile, you will first need to
            <a href="/Security/login/?BackURL={$Top.Link(attendeeInfoRegistration)}">login as a member</a>. Don't have an account?
            <a href="/join/">Join The Foundation</a>
        </p>
        <p>
            <a class="roundedButton" href="/Security/login/?BackURL={$Top.Link(attendeeInfoRegistration)}/">Login</a>
            <a href="/join/" class="roundedButton">Join The Foundation</a>
        </p>
    <% end_if %>