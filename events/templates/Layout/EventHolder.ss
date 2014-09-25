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
	<div class="eventBlock upcoming future_events">
			$getEvents(100,future_events)
			<div class="clear"></div>
	</div>
</div>

<div class="prepend-2 span-11 last events-second-column">

    <% if FutureSummits(5) %>
        <h2>Upcoming Summits</h2>
        <div class="eventBlock past future_summits">
            $getEvents(5,future_summits)
            <div class="clear"></div>
        </div>
    <% end_if %>
    <% if PastSummits(5) %>
        <h2>Recent OpenStack Summits &amp; Conferences</h2>
        <div class="eventBlock past past_summits">
            $getEvents(5,past_summits)
            <div class="clear"></div>
        </div>
    <% end_if %>
</div>