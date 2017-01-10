<% if $CurrentMember %>
    <% if $CurrentMember.isAttendee($Event.SummitID) %>
            <% if $Event.hasRSVPTemplate %>
                <div class="event_item">
                <% if $Event.CurrentRSVPSubmissionSeatType == "Regular" %>
                        <button id="btn_rsvp_{$Event.ID}" type="button" class="btn btn-primary btn-md active btn-warning btn-rsvp-event"
                                <% if $CurrentMember.getSummitAttendee($Event.SummitID).hasRSVPSubmission($Event.ID) %>disabled="disabled"<% end_if %>
                                data-toggle="modal" data-target="#rsvpModal_{$Event.ID}">RSVP to this Event</button>
                <% else_if $Event.CurrentRSVPSubmissionSeatType == "WaitList" %>
                        <button id="btn_rsvp_{$Event.ID}" type="button" class="btn btn-primary btn-md active btn-warning btn-rsvp-event"
                                <% if $CurrentMember.getSummitAttendee($Event.SummitID).hasRSVPSubmission($Event.ID) %>disabled="disabled"<% end_if %>
                                data-toggle="modal" data-target="#rsvpModal_{$Event.ID}">RSVP Waitlist to this Event</button>
                <% else %>
                    <span class="label label-warning label-rsvp-full">Event Full</span>
                <% end_if %>
                </div>
                <div id="rsvpModal_{$Event.ID}" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">RSVP</h4>
                            </div>
                            <div class="modal-body">
                                $RSVPForm($Event.ID)
                            </div>
                        </div>
                    </div>
                </div>
            <% else_if $Event.RSVPLink %>
                <div class="event_item">
                    <a id="btn_rsvp_{$Event.ID}" href="{$Event.RSVPLink}" class="btn btn-primary btn-md active btn-warning btn-rsvp-event" target="_blank" role="button">RSVP to this Event</a>
                </div>
            <% end_if %>
    <% else_if $Event.hasRSVPTemplate || $Event.RSVPLink %>
        <div class="event_item">
            <button type="button" class="btn btn-primary btn-md active btn-warning btn-rsvp-event" data-toggle="modal" data-target="#rsvpModal_{$Event.ID}">RSVP to this Event</button>
        </div>
        <div id="rsvpModal_{$Event.ID}" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">RSVP</h4>
                    </div>
                    <div class="modal-body">
                        <p><%t Summit.RegistrationLine1 member_name=$CurrentMember.FullName summit_name=$Top.Summit.Title summit_registration_link=$Top.Summit.RegistrationLink %></p>
                        <p><%t Summit.RegistrationLine2 confirm_order_link=$Top.ProfileAttendeeRegistrationLink %></p>
                    </div>
                </div>
            </div>
        </div>
    <% end_if %>
<% else_if $Event.hasRSVPTemplate %>
    <div class="event_item">
    <% if $Event.CurrentRSVPSubmissionSeatType == "Regular" %>
            <a id="btn_rsvp_{$Event.ID}" href="/Security/login/?BackURL={$Top.Link(events)}/{$Event.ID}/{$Event.TitleForUrl}" class="btn btn-primary btn-md active btn-warning btn-rsvp-event" target="_blank" role="button">RSVP to this Event</a>
    <% else_if $Event.CurrentRSVPSubmissionSeatType == "WaitList" %>
            <a id="btn_rsvp_{$Event.ID}" href="/Security/login/?BackURL={$Top.Link(events)}/{$Event.ID}/{$Event.TitleForUrl}" class="btn btn-primary btn-md active btn-warning btn-rsvp-event" target="_blank" role="button">RSVP Waitlist to this Event</a>
    <% else %>
        <span class="label label-warning label-rsvp-full">Event Full</span>
    <% end_if %>
    </div>
<% else_if $Event.RSVPLink %>
    <div class="event_item">
        <a id="btn_rsvp_{$Event.ID}" href="/Security/login/?BackURL={$Top.Link(events)}/{$Event.ID}/{$Event.TitleForUrl}" class="btn btn-primary btn-md active btn-warning btn-rsvp-event" target="_blank" role="button">RSVP to this Event</a>
    </div>
<% end_if %>