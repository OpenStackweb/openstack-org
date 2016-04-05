<% if ActiveSummit && ActiveSummit.isAttendeesRegistrationOpened && not CurrentMember.getUpcomingSummitAttendee %>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <p>Hello {$CurrentMember.FullName}! registration process for <strong>$Top.ActiveSummit.Title</strong> Summit is opened from <strong>$Top.ActiveSummit.getBeginDateDMY</strong> to <strong>$Top.ActiveSummit.getEndDateDMY</strong>.</p>
        <p>Are you a Summit Attendee? Add your Order # to unlock features only available for Registered Summit Attendees <a href="{$Top.Link(attendeeInfoRegistration)}" class="alert-link">here</a></p>
    </div>
<% end_if %>