<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="eventTitleArea">
                <h1>OpenStack Event Listing</h1>
            </div>
        </div>
    </div> 
    <div class="row">
        <div class="col-lg-12">
            <a href="https://www.openstack.org/summit/tokyo-2015/">
                <div class="event-ad-lrg"></div>
            </a>
        </div>
    </div>
    <div class="row">
        <div id='upcoming-events-container' class="col-sm-6" style="min-height:500px">
            <div style="float:left;"><h2>Upcoming Events</h2></div>
            <div style="float:left; margin:5px 0 10px 12px">
                <a href="#" class="event-type-link event-type-selected" data-type="All">All (<span class="event-type-count">$AllEventCount</span>)</a>
                <a href="#" class="event-type-link" data-type="Industry">Industry (<span class="event-type-count">$IndustryEventCount</span>)</a>
                <a href="#" class="event-type-link" data-type="Meetups">Meetups (<span class="event-type-count">$MeetupEventCount</span>)</a>
                <a href="#" class="event-type-link" data-type="OpenStack Days">OpenStack Days (<span class="event-type-count">$OpenStackDaysEventCount</span>)</a>
                <a href="#" class="event-type-link" data-type="Other">Other (<span class="event-type-count">$OtherEventCount</span>)</a>
                <span class="events-loading hidden">&nbsp;</span>
            </div>
            <div style="clear:both"></div>
            <div id='upcoming-events' class="eventBlock upcoming">
                <div>
                    $getEvents(100,future_events)
                </div>
            </div>
        </div>
        <div class="col-sm-6 events-second-column" style="min-height:500px">
            <% if FutureSummits(5) %>
            <h2>Upcoming Summits</h2>
            <div id='future-summits' class="eventBlock summit">
                $getEvents(5,future_summits)
            </div>
            <% end_if %>
            <% if PastSummits(5) %>
            <h2>Recent OpenStack Summits &amp; Conferences</h2>
            <div id='past-summits' class="eventBlock past">
                $getEvents(5,past_summits)
            </div>
            <% end_if %>
        </div>
            <div class="postEvent">
                <p>
                    Submit your upcoming OpenStack event here.
                </p>
                <a href="/community/events/post-an-event/">Post An Event For Free</a>
            </div>
    </div>
</div>
