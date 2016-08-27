<% if ActiveSummit && ActiveSummit.isAttendeesRegistrationOpened && not CurrentMember.getUpcomingSummitAttendee %>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <p><%t Summit.RegistrationLine1 member_name=$CurrentMember.FullName summit_name=$Top.ActiveSummit.Title summit_registration_link=$Top.ActiveSummit.RegistrationLink %></p>
        <p><%t Summit.RegistrationLine2 confirm_order_link=$Top.Link(attendeeInfoRegistration) %></p>
    </div>
<% end_if %>