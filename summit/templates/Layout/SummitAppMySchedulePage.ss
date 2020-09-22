<div class="container-fluid">
    <div class="container section1">

        <script type="application/javascript">
            var events     = {};
            var dic_events = [];
            <% loop $Schedule %>
                if (!events["{$getDayLabel()}"]) events["{$getDayLabel()}"] = [];
                var event_{$ID} = {
                    id: $ID,
                    start_time: "{$getStartTime}",
                    end_time: "{$getEndTime}",
                    start_datetime : "{$getStartDate}",
                    end_datetime : "{$getEndDate}",
                    time_zone_id: "{$Summit.TimeZoneName}",
                    title: "{$Title.JS}",
                    description: "{$Abstract.JS}",
                    room: "{$getLocationNameNice.JS}",
                    total: $Attendees.Count(),
                    capacity: "{$LocationCapacity}",
                    rsvp: "{$RSVPLink}",
                    summit_id: $Summit.ID,
                };

                events["{$getDayLabel()}"].push(event_{$ID});
                dic_events[$ID] = event_{$ID};
            <% end_loop %>
            var summit = {
                id : $Summit.ID,
            };

            var should_show_venues = <% if $Summit.ShouldShowVenues %> 1 <% else %> 0 <% end_if %>;
            var is_logged_user     = <% if $CurrentMember %> true <% else %> false <% end_if %>;
            var summit_id          = {$Summit.ID};
            var base_url           = "{$Top.Link}";
            var pdfUrl             = "{$Top.Link(/mine/pdf)}";
            var backUrl            = "{$Top.Link(/mine)}";
            var goBack             =  <%if $goback %>true<% else %>false<% end_if %>;
            var timeZone           = "{$Summit.getTimeZoneOffsetFriendly()}";

        </script>
        <div id="my-schedule-view-container"></div>
    </div>
</div>
$ModuleJS('my-schedule-view')
$ModuleCSS('my-schedule-view')