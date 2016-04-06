<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-main-title search-header">
            <div class="row">
                <div class="col-xs-12"><h1>Search Result</h1></div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <schedule-global-filter search_url="{$Top.Link(global-search)}" value="{$SearchTerm}" clear_url="{$Top.Link}"></schedule-global-filter>
                </div>
            </div>
        </div>
    </div>
    <% if SpeakerResults && SpeakerResults.Count %>
    <div class="container">
        <div class="col-md-12">
            <h2 class="subtitle" >Speaker Matches</h2>
        </div>
    </div>
    <div class="row people-results">
        <div class="container">
            <% loop SpeakerResults %>
                <div class="col-xs-4 col-md-4">
                    <div class="row speaker-result">
                        <div class="col-md-4">
                            <a href="{$Top.Link(speakers)}/{$ID}">
                                <img src="{$ProfilePhoto(100)}" class="img-circle big-profile-pic" alt="{$Name}">
                            </a>
                        </div>
                        <div class="col-md-8 result-speaker-name-div">
                            <div class="row speaker-name-row">
                                <div class="col-md-12">
                                    <a href="{$Top.Link(speakers)}/{$ID}">$Name</a>
                                </div>
                            </div>
                            <div class="row speaker-position-row"><div class="col-md-12">{$getTitleNice()}</div></div>
                        </div>
                    </div>
                </div>
            <% end_loop %>
        </div>
    </div>
    <% end_if %>

    <% if EventResults && EventResults.Count %>
    <div class="row">
        <div class="container">
            <div class="col-md-12">
                <h2 class="subtitle" >Schedule Matches</h2>
            </div>
            <div class="col-md-12">
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
                         profile_pic : "{$ProfilePhoto(60).JS}",
                         position : "{$TitleNice.JS}",
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

                    <% loop EventResults %>
                        summit.events.push(
                                {
                                    id              : {$ID},
                                    title           : "{$Title.JS}",
                                    description     : "{$Description.JS}",
                                    abstract        : "{$ShortDescription.JS}",
                                    date_nice       : "{$StartDate().Format(D j)}",
                                    start_datetime  : "{$StartDate}",
                                    end_datetime    : "{$EndDate}",
                                    start_time      : "{$StartTime}",
                                    end_time        : "{$EndTime}",
                                    allow_feedback  : {$AllowFeedBack},
                                    location_id     : {$LocationID},
                                    type_id         : {$TypeID},
                                    rsvp_link       : "{$RSVPLink.JS}",
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
    </div>
    <% end_if %>

    <% if SpeakerResults.Count = 0 && EventResults.Count = 0 %>
    <div class="row">
        <div class="container">
            <div class="col-md-12">
                <h2> We're sorry. There are no results for this search query. </h2>
            </div>
        </div>
    </div>
    <% end_if %>
</div>
<script src="summit/javascript/schedule/schedule.bundle.js" type="application/javascript"></script>

<script src="summit/javascript/schedule/event-list.bundle.js" type="application/javascript"></script>