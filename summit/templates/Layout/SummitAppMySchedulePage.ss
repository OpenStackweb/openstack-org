<div class="container-fluid">
    <div class="container section1">
        <div class="row schedule-title-wrapper">
            <div class="col-sm-4 col-main-title">
                <h1 style="text-align:left;">My Schedule</h1>
                <% if $goback %>
                <div class="go-back">
                    <a href="#" onclick="window.history.back(); return false;"><< Go back </a>
                </div>
                <% end_if %>
            </div>

            <div class="col-sm-4">
                <form action="{$Top.Link('/mine/pdf')}">
                    <button type="submit" class="btn btn-primary export_schedule" >Export PDF</button>
                    <label class="btn btn-default" id="show_desc">
                        <input type="checkbox" autocomplete="off" name="show_desc"> Show Description
                    </label>
               </form>
            </div>
            <div class="col-sm-2 col-log-in">
                <a class="action btn btn-default" id="login-button" href="/Security/logout/?BackURL={$Top.Link}"><i class="fa fa-sign-out" aria-hidden="true"></i>Log Out</a>
             </div>
        </div>
        <hr/>
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
        </script>
        <schedule-my-schedule summit="{ summit }" dic_events="{ dic_events }" events="{ events }" base_url="{$Top.Link}" should_show_venues="{ should_show_venues }"></schedule-my-schedule>
    </div>
</div>

$ModuleJS('my-schedule-view')