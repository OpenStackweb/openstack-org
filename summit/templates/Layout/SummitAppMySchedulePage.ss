<div class="container-fluid">
    <div class="container section1">
        <div class="row schedule-title-wrapper">
            <div class="col-sm-6 col-main-title">
                <h1 style="text-align:left;">My Schedule</h1>
                <% if $goback %>
                <div class="go-back">
                    <a href="#" onclick="window.history.back(); return false;"><< Go back </a>
                </div>
                <% end_if %>
            </div>
            <div class="col-sm-6">
                <form action="{$Top.Link('/mine/pdf')}">
                    <button type="submit" class="btn btn-primary export_schedule" >Export PDF</button>
                    <label class="btn btn-default" id="show_desc">
                        <input type="checkbox" autocomplete="off" name="show_desc"> Show Description
                    </label>
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
                    description: "{$ShortDescription.JS}",
                    room: "{$getLocationNameNice.JS}",
                    total: $Attendees.Count(),
                    capacity: "{$LocationCapacity}",
                    rsvp: "{$RSVPLink}"
                });
            <% end_loop %>

            var should_show_venues = <% if $Summit.ShouldShowVenues %> 1 <% else %> 0 <% end_if %>;
        </script>
        <schedule-my-schedule events="{ events }" base_url="{$Top.Link}" should_show_venues="{ should_show_venues }"></schedule-my-schedule>
    </div>
</div>


<script src="summit/javascript/schedule/my-schedule-view.bundle.js" type="application/javascript"></script>
