<div class="container-fluid">
    <div class="container section1">
        <script type="application/javascript">
            var events       = {};
            var events_by_id = {};
            <% loop $Schedule %>
                if (!events["{$getDayLabel()}"]) events["{$getDayLabel()}"] = [];
                var event_{$ID} = {
                    id: $ID,
                    start_time: "{$getStartTime}",
                    end_time: "{$getEndTime}",
                    title: {$TitleJson},
                    description: {$AbstractJson},
                    room: "{$getLocationNameNice.JS}",
                    total: $Attendees.Count(),
                    capacity: "{$LocationCapacity}",
                    rsvp: "{$RSVPURL}",
                    time_zone_id : "{$Summit.TimeZoneName}",
                    start_datetime : "{$StartDate}",
                    end_datetime   : "{$EndDate}",
                };
                events["{$getDayLabel()}"].push(event_{$ID});
                events_by_id[event_{$ID}.id] = event_{$ID};
            <% end_loop %>

            var should_show_venues = <% if $Summit.ShouldShowVenues %> true <% else %> false <% end_if %>;
            var is_logged_user     = <% if $CurrentMember %> true <% else %> false <% end_if %>;
            var summit_id          = {$Summit.ID};
            var base_url           = "{$Top.Link}";
            var pdfUrl             = "{$Top.Link(/full/pdf)}";
            var backUrl            = "{$Top.Link(/full)}";
            var goBack             =  <%if $goback %>true<% else %>false<% end_if %>;
        </script>
        <div id="full-schedule-view-container"></div>
    </div>
</div>
$ModuleJS('full-schedule-view')
$ModuleCSS('full-schedule-view')