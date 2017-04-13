<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-main-title search-header">
            <div class="row">
                <div class="col-xs-6">
                    <h1 style="text-align:left;">Search Result</h1>
                </div>
                <div class="col-xs-6">
                    <schedule-global-filter search_url="{$Top.Link(global-search)}" value="{$SearchTerm}" clear_url="{$Top.Link}"></schedule-global-filter>
                </div>
            </div>
        </div>
    </div>

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
                            id:   $Summit.ID,
                            link: "{$Summit.Link.JS}",
                            schedule_link: "{$Summit.getScheduleLink.JS}",
                            track_list_link: "{$Summit.getTrackListLink.JS}",
                            title: "{$Summit.Title.JS}",
                            year: "{$Summit.getSummitYear().JS}",
                            dates : [],
                            events: [],
                            dic_events:{},
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

                    summit.share_info =   {
                        fb_app_name: "OpenStack",
                        fb_app_id : "{$Top.SiteConfig.getOGApplicationID()}",
                        tweet: '<%t Summit.TweetText %>'
                    };

                    <% if $CurrentMember %>
                        <% with $CurrentMember %>
                            summit.current_user = { id: {$ID}, first_name: '{$FirstName.JS}', last_name: '{$Surname.JS}', is_attendee: <% if CurrentMember.isAttendee($Top.Summit.ID) %>true<% else %>false<% end_if %> };
                        <% end_with %>
                    <% end_if %>

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

                    <% loop $Top.Summit.getCategories %>
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

                    <% loop $Top.Summit.Locations %>
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

                    <% loop EventResults %>
                        var event_{$ID} =   {
                            id              : {$ID},
                            title           : "{$Title.JS}",
                            abstract        : "{$Abstract.JS}",
                            date_nice       : "{$StartDate().Format(D j)}",
                            start_datetime  : "{$StartDate}",
                            end_datetime    : "{$EndDate}",
                            start_time      : "{$StartTime}",
                            end_time        : "{$EndTime}",
                            allow_feedback  : {$AllowFeedBack},
                            location_id     : {$LocationID},
                            type_id         : {$TypeID},
                            rsvp_link       : "{$getRSVPURL().JS}",
                            has_rsvp        : <%if hasRSVP() %>true<% else %>false<% end_if %>,
                            rsvp_external   : <%if isExternalRSVP() %>true<% else %>false<% end_if %>,
                            rsvp_seat_type  : "{$CurrentRSVPSubmissionSeatType}",
                            sponsors_id     : [<% loop Sponsors %>{$ID},<% end_loop %>],
                            tags_id         : [<% loop Tags %>{$ID},<% end_loop %>],
                            track_id : {$CategoryID},
                            <% if ClassName == Presentation %>
                                moderator_id: {$ModeratorID},
                                speakers_id : [<% loop Speakers %>{$ID},<% end_loop %>],
                                level : '{$Level}',
                                to_record : {$ToRecord},
                            <% end_if %>
                            <% if $CurrentMember && $CurrentMember.isOnMySchedule($ID) %>
                                going      : true,
                            <% else %>
                                going      : false,
                            <% end_if %>
                            <% if $CurrentMember && $CurrentMember.isOnFavorites($ID) %>
                                favorite : true,
                            <% else %>
                                favorite : false,
                            <% end_if %>
                            show : true,
                            attachment_url : "<% if $Attachment().Exists() %>{$Attachment().getUrl().JS}<% end_if %>",
                            category_group_ids:[
                                <% if Category && Category.CategoryGroups %>
                                    <% loop Category.CategoryGroups %>
                                        {$ID},
                                    <% end_loop %>
                                <% end_if %>
                            ],
                            url: "{$getLink(show)}",
                            social_summary : "{$SocialSummary.JS}",
                        };
                        summit.events.push(event_{$ID});
                        summit.dic_events[{$ID}] = event_{$ID};
                    <% end_loop %>
                </script>

                <event-list summit="{ summit }" default_event_color={'#757575'} search_url="{$Top.Link(global-search)}" base_url="{$Top.Link}" ></event-list>

            </div>
        </div>
    </div>
    <% end_if %>

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
$ModuleJS('event-list')
