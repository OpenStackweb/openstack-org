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
            <a href="https://www.openstack.org/summit/austin-2016/">
                <div class="event-ad-lrg"></div>
            </a>
        </div>
    </div>
    <div class="row">
        <div id='upcoming-events-container' class="col-sm-6" style="min-height:500px">
            <div style="float:left;"><h2>Upcoming Events</h2></div>
            <div style="clear: both; margin:5px 0 10px 0px">
                <div class="event-type-links">
                    $EventTypes
                </div>
                <span class="events-loading hidden">&nbsp;</span>
                <div style="clear:both"></div>
            </div>
            <div id='upcoming-events' class="upcoming hidden">
                <div>
                    $getEvents(100,future_events)
                </div>
            </div>
        </div>
        <div class="col-sm-6 events-second-column" style="min-height:500px">
            <% if FutureSummits(5) %>
            <h2>Upcoming Summits</h2>
            <div id='future-summits' class="eventBlock summit hidden">
                $getEvents(5,future_summits)
            </div>
            <% end_if %>
            <% if PastSummits(5) %>
            <h2>Recent OpenStack Summits &amp; Conferences</h2>
            <div id='past-summits' class="eventBlock past hidden">
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
