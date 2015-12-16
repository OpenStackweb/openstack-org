<% if ActiveSummit && ActiveSummit.isAttendeesRegistrationOpened && not CurrentMember.getUpcomingSummitAttendee %>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        Hey Attendees ! registration process for <strong>$Top.ActiveSummit.Title</strong> Summit is opened from <strong>$Top.ActiveSummit.RegistrationBeginDate</strong> to <strong>$Top.ActiveSummit.RegistrationEndDate</strong>.
        <a href="{$Link}attendeeInfoRegistration" class="alert-link">Check here!</a>
    </div>
<% end_if %>