<div class="container-fluid">
    <div class="container section1">
        <div class="row schedule-title-wrapper">
            <div class="col-md-10 col-xs-12 col-main-title">
                <h1 style="text-align:left;">Synchronize Calendar</h1>
                <% if $goback %>
                <div class="go-back">
                    <a href="#" onclick="window.history.back(); return false;"><< Go back </a>
                </div>
                <% end_if %>
            </div>
            <div class="col-md-2 col-xs-12 col-log-in">
                <a class="action btn btn-default" id="login-button" href="/Security/logout/?BackURL={$Top.Link}"><i class="fa fa-sign-out" aria-hidden="true"></i>Log Out</a>
             </div>
        </div>
        <hr/>
        <script async defer src="https://apis.google.com/js/api.js"></script>

        <script type="application/javascript">
            var summit =
                {
                    id:   $Summit.ID,
                    link: "{$Summit.Link.JS}",
                    schedule_link: "{$Summit.getScheduleLink.JS}",
                    track_list_link: "{$Summit.getTrackListLink.JS}",
                    title: "{$Summit.Title.JS}",
                    year: "{$Summit.getSummitYear().JS}",
                    schedule_default_day: '{$Summit.ScheduleDefaultDate}',
                    current_user: null,
                };

            <% if CurrentMember %>
                <% with CurrentMember %>
                    summit.current_user = {
                        id: {$ID},
                        first_name: '{$FirstName.JS}',
                        last_name: '{$Surname.JS}',
                        is_attendee: <% if CurrentMember && CurrentMember.isAttendee($Top.Summit.ID) %> true <% else %> false <% end_if %>,
                        cal_sync:   <% if CurrentMember && CurrentMember.existCalendarSyncInfoForSummit($Top.Summit.ID) %> true <% else %> false <% end_if %>
                    };
                <% end_with %>
            <% end_if %>

            window.ReactScheduleGridProps = {
                summit: summit,
            };
        </script>

        <div id="os-schedule-sync-calendar"></div>
    </div>
</div>

$ModuleJS('sync-calendar')
$ModuleCSS('sync-calendar')