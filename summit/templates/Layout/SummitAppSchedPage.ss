<div class="container-fluid">

    <script type="application/javascript">

        var summit =
        {
            id:   $Summit.ID,
            link: "{$Summit.Link.JS}",
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

        <% loop $Summit.Speakers %>
         summit.speakers[{$ID}] =
         {
             id: {$ID},
             name : "{$Name.JS}",
             profile_pic : "{$ProfilePhoto.JS}",
             position : "{$CurrentPosition.JS}",
         };
        <% end_loop %>

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
        summit.dates['{$Date}']  = { label: '{$Label}', date:'{$Date}', selected: false };
        summit.events['{$Date}'] = [];
       <% end_loop %>

    </script>
    <div class="row">
        <div class="col-xs-12 col-main-title">
            <div class="row">
                <div class="col-xs-12"><h1>Schedule</h1></div>
            </div>
           <div class="row">
               <div class="col-xs-12">
                   <schedule-global-filter search_url="{$Top.Link(global-search)}"></schedule-global-filter>
               </div>
           </div>
        </div>
    </div>
    <schedule-grid summit="{ summit }" default_event_color={'#757575'} search_url="{$Top.Link(global-search)}" base_url="{$Top.Link}" month="{$Summit.Month}"></schedule-grid>
</div>
<div id="fb-root"></div>
<script src="summit/javascript/schedule/schedule.bundle.js" type="application/javascript"></script>