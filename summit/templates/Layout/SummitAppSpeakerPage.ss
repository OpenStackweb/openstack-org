<div class="container-fluid">
    <% with Speaker %>
        <div class="row header">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-2">
                        <div class="row">
                            <img src="$ProfilePhoto(150)" width="150" class="img-circle" />
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-10">
                        <div class="speaker_name row"> $FirstName $LastName </div>
                        <div class="speaker_job_title row"> $Member.getCurrentPosition() </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <schedule-global-filter search_url="{$Top.Link(global-search)}"></schedule-global-filter>
            </div>
        </div>
        <div class="row section1">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-10">
                        <div class="row speaker_bio"> $Bio </div>
                    </div>
                    <div class="col-xs-12 col-md-2 speaker_info">
                        <div class="info_item row">
                            <div class="info_item_icon"><i class="fa fa-2x fa-map-marker"></i></div>
                            <div class="info_item_text">$CountryName()</div>
                        </div>
                        <% if IRCHandle %>
                        <div class="info_item row">
                            <div class="info_item_icon">
                                <span class="irc_icon">IRC</span>
                            </div>
                            <div class="info_item_text">$IRCHandle</div>
                        </div>
                        <% end_if %>
                        <% if TwitterName %>
                        <div class="info_item row">
                            <div class="info_item_icon"><i class="fa fa-2x fa-twitter"></i></div>
                            <div class="info_item_text">$TwitterName</div>
                        </div>
                        <% end_if %>
                    </div>
                </div>
            </div>
        </div>

    <div class="container">
        <div class="row sessions">

                <div class="col-md-12 col-xs-12 sessions_title">
                    <div class="row">
                        Sessions
                    </div>
                </div>

                <script type="application/javascript">

                    var summit =
                            {
                                id:   $Top.Summit.ID,
                                dates : [],
                                events: [],
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

                            <% if CurrentMember && CurrentMember.isAttendee($Top.Summit.ID) %>
                                <% with CurrentMember %>
                                summit.current_user = { id: {$ID}, first_name: '{$FirstName.JS}', last_name: '{$Surname.JS}' };
                                <% end_with %>
                            <% end_if %>

                            <% loop $Top.Summit.Speakers %>
                             summit.speakers[{$ID}] =
                             {
                                 id: {$ID},
                                 name : "{$Name.JS}",
                                 profile_pic : "{$ProfilePhoto.JS}",
                                 position : "{$CurrentPosition.JS}",
                             };
                            <% end_loop %>

                           <% loop $Top.Summit.Sponsors %>
                               summit.sponsors[{$ID}] =
                               {
                                    id: {$ID},
                                    name : "{$Name.JS}",
                               };
                           <% end_loop %>

                            <% loop $Top.Summit.Tags %>
                            summit.tags[{$ID}] =
                            {
                                id: {$ID},
                                name : "{$Tag.JS}",
                            };
                            <% end_loop %>

                            <% loop $Top.Summit.Categories %>
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

                            <% loop $Top.Summit.EventTypes %>
                            summit.event_types[{$ID}] =
                            {
                                type : "{$Type.JS}",
                                color : "{$Color}",
                            };
                            <% end_loop %>


                            <% loop $Top.Summit.Types %>
                            summit.summit_types[{$ID}] =
                            {
                               type: "{$Type}",
                               name : "{$Title.JS}",
                               description : "{$Description.JS}",
                               color : "{$Color}"
                            };
                            <% end_loop %>

                            <% loop $Top.Summit.Locations %>
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

                           <% loop PublishedPresentations($Top.Summit.ID) %>
                                summit.events.push(
                                        {
                                            id              : {$ID},
                                            title           : "{$Title.JS}",
                                            description     : "{$Description.JS}",
                                            abstract        : "{$ShortDescription.JS}",
                                            date_nice       : "{$StartDate().Format(D j)}",
                                            short_desc      : "{$getShortDescription(600).JS}",
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
                                            level : '{$Level}',
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

                <event-list summit="{ summit }" default_event_color={'#757575'} search_url="{$Top.Link(global-search)}" base_url="{$Top.Link}" ></event-list>


            </div>
        </div>
    <% end_with %>

</div>

<script src="summit/javascript/schedule/schedule.bundle.js" type="application/javascript"></script>
<script src="summit/javascript/schedule/event-list.bundle.js" type="application/javascript"></script>

