<% if UpcomingSummit && UpcomingSummit.isAttendeesRegistrationOpened && not CurrentMember.getUpcomingSummitAttendee %>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Hey!</strong> Attendees registration process is opened for $Top.UpcomingSummit.Title Summit.
        <a href="{$Link}attendeeInfoRegistration" class="alert-link">Check here!</a>
    </div>
<% end_if %>