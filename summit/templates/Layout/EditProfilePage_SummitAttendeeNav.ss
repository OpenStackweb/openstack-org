<% if UpcomingSummit && UpcomingSummit.isAttendeesRegistrationOpened %>
    <a href="{$Link}attendeeInfoRegistration"  <% if CurrentTab=8 %>class="active"<% end_if %> >Attendee Registration</a>
<% end_if %>