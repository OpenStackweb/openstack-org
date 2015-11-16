<div class="container-fluid">
    <h1 class="schedule_title">Schedule</h1>
    <hr>
    <select class="select summit_event_type_filter">
        <option value="-1">All Events</option>
        <% loop $Summit.getEventTypes() %>
            <option value="$ID">$Type</option>
        <% end_loop %>
    </select>
    <hr>
    <input id="summit_id" type="hidden" value="$Summit.ID" />
    <script type="application/javascript">
        var summit =
        {
            id:   $Summit.ID,
            dates : [],
            events: {},
            summit_types: {},
            speakers : {},
            sponsors : {},
            event_types:{},
            locations : {},
        };

        <% loop $Summit.Speakers %>
            summit.speakers[{$ID}] =
            {
                id: {$ID},
                name : "{$Name.JS}",
                profile_pic : "{$ProfilePhoto.JS}",
                position : "{$CurrentPosition.JS}",
            };
        <% end_loop %>

        <% loop $Summit.EventTypes %>
        summit.event_types[{$ID}] =
        {
            type : "{$Type.JS}",
            color : "{$Color}",
        };
        <% end_loop %>

        <% loop $Summit.Types %>
        summit.summit_types[{$ID}] =
        {
           type : "{$Title}.JS",
           description : "{$Description.JS}",
        };
        <% end_loop %>

        <% loop $Summit.Locations %>
            <% if ClassName == SummitVenue || ClassName == SummitExternalLocation %>
            summit.locations[{$ID}] =
            {
                class_name : "{$ClassName}",
                name       : "{$Name.JS}",
                description : "{$Description.JS}",
                address_1 : "{$Address1.JS}",
                address_2 : "{$Address2.JS}",
                city : "{$City}",
                state : "{$State}",
                country : "{$Country}",
                lng : '{$Lng}',
                lat : '{$Lat}',
            };
            <% if ClassName == SummitVenue %>
            <% loop Rooms %>
                summit.locations[{$ID}] =
                {
                    class_name : "{$ClassName}",
                    name       : "{$Name.JS}",
                    capacity   : {$Capacity},
                    venue_id   : {$VenueID},
                };
            <% end_loop %>
            <% end_if %>
            <% end_if %>
        <% end_loop %>

       <% loop $Summit.getDates %>
       summit.dates.push({ label: '{$Label}', date:'{$Date}'});
       summit.events['{$Date}'] = [];
       <% end_loop %>
       <% loop $Top.CurrentSummitEventsBy1stDate() %>
            summit.events[summit.dates[0].date].push(
                    {
                        id              : {$ID},
                        title           : "{$Title.JS}",
                        description     : "{$Description.JS}",
                        start_datetime  : "{$StartDate}",
                        end_datetime    : "{$EndDate}",
                        start_time      : "{$StartTime}",
                        end_time        : "{$EndTime}",
                        allow_feedback  : {$AllowFeedBack},
                        location_id     : {$LocationID},
                        type_id         : {$TypeID},
                        sponsors_id     : [<% loop Sponsors %>{$ID},<% end_loop %>],
                        summit_types_id : [<% loop AllowedSummitTypes %>{$ID},<% end_loop %>],
                        <% if ClassName == Presentation %>
                        speakers_id : [<% loop Speakers %>{$ID},<% end_loop %>],
                        <% end_if %>
                        own      : false,
                        favorite : false,
                    }
            );
        <% end_loop %>
    </script>
    <schedule-grid summit="{ summit }" month="october"></schedule-grid>
</div>
<div id="fb-root"></div>
<script src="summit/javascript/schedule/schedule.bundle.js" type="application/javascript"></script>