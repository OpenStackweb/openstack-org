<div class="container-fluid">
    <div class="container section1">
        <div class="row schedule-title-wrapper">
            <div class="col-sm-5 col-main-title">
                <h1 style="text-align:left;">Full Schedule</h1>
                <% if $goback %>
                <div class="go-back">
                    <a href="#" onclick="window.history.back(); return false;"><< Go back </a>
                </div>
                <% end_if %>
            </div>
            <div class="col-sm-2 col-log-in">
                <% if CurrentMember %>
                    <% if not CurrentMember.isAttendee($Summit.ID)  %>
                        <script>
                            $(function(){
                                swal({
                                    type: 'info',
                                    html:
                                    '<p><%t Summit.RegistrationLine1 member_name=$CurrentMember.FullName summit_name=$Top.Summit.Title summit_registration_link=$Top.Summit.RegistrationLink %></p>'+
                                    '<p><%t Summit.RegistrationLine2 confirm_order_link=$Top.ProfileAttendeeRegistrationLink %></p>',
                                });
                            });
                        </script>
                    <% end_if %>
                    <a title="logout" class="action btn btn-default" id="login-button" href="/Security/logout/?BackURL={$Top.Link(full)}"><i class="fa fa-sign-out" aria-hidden="true"></i>Log Out</a>
                <% else %>
                    <a title="Log in to unlock features only available for registered summit attendees" class="action btn btn-default" id="login-button" href="Security/login?BackURL={$Top.Link(full)}"><i class="fa fa-user"></i>Log in</a>
                <% end_if %>
            </div>
            <div class="col-sm-5">
                <form action="{$Top.Link('/full/pdf')}">
                    <button type="submit" class="btn btn-primary export_schedule" >Export PDF</button>
                    <select id="full-schedule-filter" name="sort">
                        <option value="day">Sort By Day</option>
                        <option value="track">Sort By Track</option>
                        <option value="event_type">Sort By Event Type</option>
                    </select>
                    <label class="btn btn-default" id="show_desc">
                        <input type="checkbox" autocomplete="off" name="show_desc"> Show Description
                    </label>
               </form>
            </div>
        </div>
        <hr/>
        <script type="application/javascript">
            var events       = {};
            var events_by_id = {};
            <% loop $Schedule %>
                if (!events["{$getDayLabel()}"]) events["{$getDayLabel()}"] = [];
                var event_{$ID} = {
                    id: $ID,
                    start_time: "{$getStartTime}",
                    end_time: "{$getEndTime}",
                    title: "{$Title.JS}",
                    description: "{$ShortDescription.JS}",
                    room: "{$getLocationNameNice.JS}",
                    total: $Attendees.Count(),
                    capacity: "{$LocationCapacity}",
                    rsvp: "{$RSVPURL}",
                    time_zone_id : "{$Summit.TimeZoneName}",
                    start_datetime : "{$StartDate}",
                    end_datetime   : "{$EndDate}",
                    <% if $Top.CurrentMember && $Top.CurrentMember.isAttendee($Summit.ID) %>gcal_id : "{$Top.CurrentMember.getGoogleCalEventId($ID)}", <% end_if %>
                };
                events["{$getDayLabel()}"].push(event_{$ID});
                events_by_id[event_{$ID}.id] = event_{$ID};
            <% end_loop %>

            var should_show_venues = <% if $Summit.ShouldShowVenues %> 1 <% else %> 0 <% end_if %>;
            var is_logged_user     = <% if $CurrentMember %> 1 <% else %> 0 <% end_if %>;
            var summit_id          = {$Summit.ID};

        </script>
        <schedule-full-schedule is_logged_user="{ is_logged_user }" events="{ events }" base_url="{$Top.Link}" summit_id="{ summit_id }" should_show_venues="{ should_show_venues }"></schedule-full-schedule>
    </div>
</div>


<script src="summit/javascript/schedule/full-schedule-view.bundle.js" type="application/javascript"></script>
<% include GoogleCalendar GoogleCalendarClientID=$Top.GoogleCalendarClientID %>