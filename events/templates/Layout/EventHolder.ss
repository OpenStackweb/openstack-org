<% loop RandomEventImage %>
<div class="eventsBanner" style="background-image: url($URL);">
<% end_loop %>

<div class="span-16">
		</div>
		<div class="span-8 last">
		<p class="eventsPhotoCaption">
		This Photo: The OpenStack Design Summit &amp; Conference 2011.
		</p>
	</div>
</div>


<div class="span-24 last">
	<div class="eventTitleArea">
		<h1>OpenStack Event Listing</h1>
	</div>
</div>

<div class="span-6 last postEvent">
    <a href="$PostEventLink">Post A Event For Free</a>
</div>


<div class="span-11">
	<h2>Upcoming Events</h2>
	<div class="eventBlock upcoming">
			<% if FutureEvents(100) %>
				<% loop FutureEvents(100) %>
						<div class="event<% if First %> top<% end_if %>">
							<h3><a rel="nofollow" href="$EventLink" target="_blank">$Title</a></h3>
							<p class="details">$formatDateRange - $EventLocation</p>
							<p class="eventButton"><a rel="nofollow" href="$EventLink" target="_blank">$EventLinkLabel</a></p>
						</div>
				<% end_loop %>
			<% else %>
				<div class="event top">
					<h3>Sorry, there are no upcoming events listed at the moment.</h3>
					<p class="details">Wow! It really rare that we don't have any upcoming events on display. Somewhere in the world there's sure to be an OpenStack event in the near future&mdash;We probably just need to update this list. Please check back soon for more details.</p>
				</div>
			<% end_if %>
		<div class="clear"></div>
	</div>
</div>

<div class="prepend-2 span-11 last events-second-column">
    <% if FutureSubmits(5) %>
    <h2>Upcoming Summits</h2>
    <div class="eventBlock past">
        <% loop FutureSubmits(5) %>
            <div class="event<% if First %> top<% end_if %>">
                <h3><a rel="nofollow" href="$EventLink" target="_blank">$Title</a></h3>
                <p class="details">$formatDateRange - $EventLocation</p>
                <% if EventLink %><p class="eventButton"><a rel="nofollow" href="$EventLink" target="_blank">$EventLinkLabel</a></p><% end_if %>
            </div>
        <% end_loop %>
        <div class="clear"></div>
    </div>
    <% end_if %>
    <% if FutureSubmits(5) %>
	<h2>Recent OpenStack Summits &amp; Conferences</h2>
	<div class="eventBlock past">
		<% loop PastSummits(5) %>
				<div class="event<% if First %> top<% end_if %>">
					<h3><a rel="nofollow" href="$EventLink" target="_blank">$Title</a></h3>
					<p class="details">$formatDateRange - $EventLocation</p>
					<% if EventLink %><p class="eventButton"><a rel="nofollow" href="$EventLink" target="_blank">$EventLinkLabel</a></p><% end_if %>
				</div>
		<% end_loop %>
		<div class="clear"></div>
	</div>
    <% end_if %>
</div>