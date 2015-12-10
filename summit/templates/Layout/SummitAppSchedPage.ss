<div class="container-fluid">

    <script type="application/javascript">
        var summit =
        {
            id:   $Summit.ID,
            dates : [],
            events: {},
            summit_types: {},
            speakers : {},
            sponsors : {},
            event_types:{},
            locations : {},
            tags: {},
            tracks : {},
            presentation_levels: {},
            current_user: null
        };

        <% if CurrentMember && CurrentMember.isAttendee($Summit.ID) %>
            <% with CurrentMember %>
            summit.current_user = { id: {$ID}, first_name: '{$FirstName.JS}', last_name: '{$Surname.JS}' };
            <% end_with %>
        <% end_if %>
        <% loop $Summit.Speakers %>
         summit.speakers[{$ID}] =
         {
             id: {$ID},
             name : "{$Name.JS}",
             profile_pic : "{$ProfilePhoto.JS}",
             position : "{$CurrentPosition.JS}",
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
            color : "{$Color}",
        };
        <% end_loop %>

        <% loop $Summit.EventTypes %>
        summit.event_types[{$ID}] =
        {
                type : "{$Type.JS}",
                color : "{$Color}",
        };
        <% end_loop %>

        <% loop $Summit.Types %>
        summit.summit_types[{$ID}] =
        {
           type : "{$Title.JS}",
           description : "{$Description.JS}",
           color : "{$Color}"
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

       <% loop $Summit.DatesWithEvents %>
       summit.dates.push({ label: '{$Label}', date:'{$Date}'});
       summit.events['{$Date}'] = [];
       <% end_loop %>

       <% loop $Top.CurrentSummitEventsBy1stDate() %>
            summit.events[summit.dates[0].date].push(
                    {
                        id              : {$ID},
                        title           : "{$Title.JS}",
                        description     : "{$Description.JS}",
                        start_datetime  : "{$StartDate}",
                        end_datetime    : "{$EndDate}",
                        start_time      : "{$StartTime}",
                        end_time        : "{$EndTime}",
                        allow_feedback  : {$AllowFeedBack},
                        location_id     : {$LocationID},
                        type_id         : {$TypeID},
                        sponsors_id     : [<% loop Sponsors %>{$ID},<% end_loop %>],
                        tags_id         : [<% loop Tags %>{$ID},<% end_loop %>],
                        summit_types_id : [<% loop AllowedSummitTypes %>{$ID},<% end_loop %>],
                        <% if ClassName == Presentation %>
                        moderator_id: {$ModeratorID},
                        speakers_id : [<% loop Speakers %>{$ID},<% end_loop %>],
                        track_id : {$CategoryID},
                        Level : '{$Level}',
                        <% end_if %>
                        <% if $Top.isEventOnMySchedule($ID) %>
                        own      : true,
                        <% else %>
                        own      : false,
                        <% end_if %>
                        favorite : false,
                        show : true
                    }
            );
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
    <schedule-grid summit="{ summit }" default_event_type_color={'#757575'} search_url="{$Top.Link(global-search)}" base_url="{$Top.Link}" month="october"></schedule-grid>
</div>
<div id="fb-root"></div>
<script src="summit/javascript/schedule/schedule.bundle.js" type="application/javascript"></script>