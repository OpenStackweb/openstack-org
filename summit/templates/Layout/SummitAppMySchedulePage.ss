<div class="container-fluid">
    <div class="container section1">
        <div class="row schedule-title-wrapper">
            <div class="col-sm-5 col-main-title">
                <h1 style="text-align:left;">My Schedule</h1>
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
            <div class="col-sm-2 col-sync-calendar">
                <% if CurrentMember && CurrentMember.isAttendee($Summit.ID) %>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default">Sync to Calendar</button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" class="link-google-sync" id="link_google_sync"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>&nbsp;Google&nbsp;sync</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#" class="link-export-ics" id="link_export_ics"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>&nbsp;Export&nbsp;ICS</a></li>
                        </ul>
                    </div>
                <% else %>
                    &nbsp;
                <% end_if %>
            </div>
            <div class="col-sm-5">
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
