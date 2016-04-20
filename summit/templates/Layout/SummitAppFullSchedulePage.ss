<div class="container-fluid">
    <div class="container section1">
        <div class="row schedule-title-wrapper">
            <div class="col-sm-6 col-main-title">
                <h1 style="text-align:left;">Full Schedule</h1>
                <% if $goback %>
                <div class="go-back">
                    <a href="#" onclick="window.history.back(); return false;"><< Go back </a>
                </div>
                <% end_if %>
            </div>
            <div class="col-sm-6">
                <form action="{$Top.Link('/full/pdf')}">
                    <button type="submit" class="btn btn-primary export_schedule" >Export PDF</button>
                    <select id="full-schedule-filter" name="sort">
                        <option value="day">Sort By Day</option>
                        <option value="track">Sort By Track</option>
                        <option value="event_type">Sort By Event Type</option>
                    </select>
               </form>
            </div>
        </div>
        <hr/>
        <script type="application/javascript">
            var events = {};
            <% loop $Schedule %>
                if (!events["{$getDayLabel()}"]) events["{$getDayLabel()}"] = [];

                events["{$getDayLabel()}"].push({
                    id: $ID,
                    start_time: "{$getStartTime}",
                    end_time: "{$getEndTime}",
                    title: "{$Title.JS}",
                    room: "{$getLocationNameNice.JS}",
                    total: $Attendees.Count(),
                    capacity: "{$LocationCapacity}",
                    rsvp: "{$RSVPLink}"
                });
            <% end_loop %>

            var should_show_venues = <% if $Summit.ShouldShowVenues %> 1 <% else %> 0 <% end_if %>;
            var summit_id = {$Summit.ID};
        </script>
        <schedule-full-schedule events="{ events }" base_url="{$Top.Link}" summit_id="{ summit_id }" should_show_venues="{ should_show_venues }"></schedule-full-schedule>
    </div>
</div>


<script src="summit/javascript/schedule/full-schedule-view.bundle.js" type="application/javascript"></script>
