<div class="container">

    <script type="application/javascript">

        var summit =
        {
            id:   $Summit.ID,
            link: "{$Summit.Link.JS}",
            schedule_link: "{$Summit.getScheduleLink.JS}",
            title: "{$Summit.Title.JS}",
            year: "{$Summit.getSummitYear().JS}",
            dates : {},
            events: {},
            summit_types: {},
            speakers : {},
            sponsors : {},
            event_types:{},
            locations : {},
            tags: {},
            tracks : {},
            category_groups: {},
            presentation_levels: {},
            current_user: null,
            should_show_venues: <% if $Summit.ShouldShowVenues %>true<% else %>false<% end_if %>
        };

        <% if CurrentMember && CurrentMember.isAttendee($Summit.ID) %>
            <% with CurrentMember %>
            summit.current_user = { id: {$ID}, first_name: '{$FirstName.JS}', last_name: '{$Surname.JS}' };
            <% end_with %>
        <% end_if %>

        <% cached 'frontend_schedule_page', $Summit.ID, $Summit.LastEdited %>

        <% loop $Summit.Sponsors %>
           summit.sponsors[{$ID}] =
           {
                id: {$ID},
                name : "{$Name.JS}",
           };
       <% end_loop %>

        <% loop $Summit.Tags %>
        summit.tags[{$ID}] =
        {
            id: {$ID},
            name : "{$Tag.JS}",
        };
        <% end_loop %>

        <% loop $Summit.Categories %>
        summit.tracks[{$ID}] =
        {
            id: {$ID},
            name : "{$Title.JS}",
        };
        <% end_loop %>

        <% loop $Top.getPresentationLevels %>
        summit.presentation_levels['{$Level}'] =
        {
            level : "{$Level}",
        };
        <% end_loop %>

        <% loop $Summit.EventTypes %>
        summit.event_types[{$ID}] =
        {
            type : "{$Type.JS}",
            color : "{$FormattedColor}",
        };
        <% end_loop %>

        <% loop $Summit.Types %>
        summit.summit_types[{$ID}] =
        {
           type: "{$Type}",
           name : "{$Title.JS}",
           description : "{$Description.JS}",
           color : "{$FormattedColor}"
        };
        <% end_loop %>

        <% loop $Summit.CategoryGroups %>
        summit.category_groups[{$ID}] =
        {
           name : "{$Name.JS}",
           description : "{$Description.JS}",
           color : "{$FormattedColor}"
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
                venue_id : {$Venue.ID},
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

       <% end_cached %>
       <% loop $Summit.DatesWithEvents %>
        summit.dates['{$Date}']  = { label: '{$Label}', date:'{$Date}', has_events:{$Has_Published_Events} , selected: false, is_weekday:{$IsWeekday} };
        summit.events['{$Date}'] = [];
       <% end_loop %>

    </script>
    <div class="row schedule-title-wrapper">
        <div class="col-sm-6 col-main-title">
            <h1 style="text-align:left;">Schedule</h1>
        </div>
        <div class="col-sm-6">
           <schedule-global-filter search_url="{$Top.Link(global-search)}"></schedule-global-filter>
        </div>
    </div>
    <% if CurrentMember %>
        <% if not CurrentMember.isAttendee($Summit.ID)  %>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <p>Hello {$CurrentMember.FullName} ! registration process for <strong>$Top.Summit.Title</strong> Summit is opened from <strong>$Top.Summit.getBeginDateDMY</strong> to <strong>$Top.Summit.getEndDateDMY</strong>.</p>
                <p>Are you a Summit Attendee? Add your Order # to unlock features only available for Registered Summit Attendees <a href="profile/attendeeInfoRegistration" class="alert-link">here</a></p>
            </div>
        <% end_if %>
    <% else %>
        <div class="row">
            <div class="col-xs-12 login-container">
                <form id="MemberLoginForm_LoginForm" action="Security/login?BackURL={$Top.Link}" method="post" enctype="application/x-www-form-urlencoded">
                    <input type="hidden" name="fragment" id="fragment"/>
                    <div class="Actions">
                        <button class="action btn btn-primary" type="submit" id="login-button" name="action_dologin" title="Log in to unlock features only available for registered summit attendees">
                            <i class="fa fa-user"></i>
                            Log in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <% end_if %>
    <schedule-grid summit="{ summit }" default_event_color={'#757575'} search_url="{$Top.Link(global-search)}" base_url="{$Top.Link}" month="{$Summit.Month}"></schedule-grid>
</div>
<div id="fb-root"></div>
<script src="summit/javascript/schedule/schedule.bundle.js?t={$Top.getTime}" type="application/javascript"></script>