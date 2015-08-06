
<h1>Here are the details</h1>

<ul>
	<li>Hotel name: $Location.Name </li>
	<li>Booking Start Date: $Location.BookingStartDate.Month $Location.BookingStartDate.DayOfMonth</li>
	<li>Booking End Date: $Location.BookingEndDate.Month $Location.BookingEndDate.DayOfMonth</li>
	<li>Booking Link: <a href="{$Location.BookingLink}">$Location.BookingLink</a> </li>

	<% if $Location.InRangeBookingGraphic %>
		<h4>In range booking graphic:</h4>
		<img src="{$Location.InRangeBookingGraphic}.png" alt="">
	<% end_if %>

	<% if $Location.OutOfRangeBookingGraphic %>
		<h4>Out of range booking graphic:</h4>
		<img src="{$Location.OutOfRangeBookingGraphic}.png" alt="">
	<% end_if %>


</ul>