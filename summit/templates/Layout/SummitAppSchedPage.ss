<div class="container">

    <script type="application/javascript">

        var summit =
        {
            id:   $Summit.ID,
            link: "{$Summit.Link.JS}",
            schedule_link: "{$Summit.getScheduleLink.JS}",
            track_list_link: "{$Summit.getTrackListLink.JS}",
            title: "{$Summit.Title.JS}",
            year: "{$Summit.getSummitYear().JS}",
            dates : {},
            events: {},
            speakers : {},
            sponsors : {},
            event_types:{},
            event_type_ids: [],
            locations : {},
            tags: {},
            tag_ids:[],
            tracks : {},
            track_ids : [],
            category_groups: {},
            category_group_ids: [],
            presentation_levels: {},
            presentation_level_ids: [],
            current_user: null,
            should_show_venues: <% if $Summit.ShouldShowVenues %>true<% else %>false<% end_if %>,
            schedule_default_day: '{$Summit.ScheduleDefaultDate}',
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

        <% loop $Summit.Tags.Sort("Tag", "ASC") %>
        summit.tags[{$ID}] =
        {
            id: {$ID},
            name : "{$Tag.JS}",
        };
        summit.tag_ids.push($ID);
        <% end_loop %>

        <% loop $Summit.getCategories().Sort("Title", "ASC") %>
            <% if hasEventsPublished %>
                summit.tracks[{$ID}] =
                {
                    id: {$ID},
                    name : "{$Title.JS}",
                };
                summit.track_ids.push($ID);
            <% end_if %>
        <% end_loop %>

        <% loop $Top.getPresentationLevels %>
        summit.presentation_levels['{$Level}'] =
        {
            level : "{$Level}",
        };
        <% end_loop %>

        <% loop $Summit.EventTypes.Sort("Type", "ASC")  %>
        summit.event_types[{$ID}] =
        {
            type : "{$Type.JS}",
            color : "{$FormattedColor}",
        };
        summit.event_type_ids.push($ID);
        <% end_loop %>

        <% loop $Summit.CategoryGroups.Sort(Name, ASC)  %>
            <% if hasEventsPublished %>
            summit.category_groups[{$ID}] =
            {
               name : "{$Name.JS}",
               description : "{$Description.JS}",
               color : "{$FormattedColor}",
               tracks: [
                       <% loop Categories %>
                           <% if hasEventsPublished %>
                                $ID,
                           <% end_if %>
                       <% end_loop %>
               ],
            };
            summit.category_group_ids.push($ID);
            <% end_if %>
        <% end_loop %>

        <% loop $Summit.Locations %>
            <% if ClassName == SummitVenue || ClassName == SummitExternalLocation %>
            summit.locations[{$ID}] =
            {
                class_name  : "{$ClassName}",
                name        : "{$Name.JS}",
                name_nice   : "{$Name.JS}",
                description : "{$Description.JS}",
                address_1   : "{$Address1.JS}",
                address_2   : "{$Address2.JS}",
                city        : "{$City}",
                state       : "{$State}",
                country     : "{$Country}",
                lng         : '{$Lng}',
                lat         : '{$Lat}',
                venue_id    : {$Venue.ID},
                link        : "{$Link.JS}",
            };
            <% if ClassName == SummitVenue %>
            <% loop Rooms %>
                summit.locations[{$ID}] =
                {
                    class_name : "{$ClassName}",
                    name       : "{$Name.JS}",
                    name_nice  : "{$FullName.JS}",
                    capacity   : {$Capacity},
                    venue_id   : {$VenueID},
                    link       : "{$Link.JS}",
                };
            <% end_loop %>
            <% loop Floors %>
                <% loop Rooms %>
                    summit.locations[{$ID}] =
                    {
                        class_name : "{$ClassName}",
                        name       : "{$Name.JS}",
                        name_nice  : "{$FullName.JS}",
                        capacity   : {$Capacity},
                        venue_id   : {$Up.VenueID},
                        link       : "{$Link.JS}",
                    };
                <% end_loop %>
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
        <div class="row">
            <div class="col-xs-12 logout-container">
                <a class="action btn btn-default" id="login-button" href="/Security/logout/?BackURL={$Top.Link}"><i class="fa fa-sign-out" aria-hidden="true"></i>Log Out</a>
            </div>
         </div>
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
$ModuleJSI('schedule')
<% include GoogleCalendar GoogleCalendarClientID=$Top.GoogleCalendarClientID %>
