<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=3 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li class="active">Schedule</li>
        </ol>
        <script type="application/javascript">
                var summit =
                {
                    id:   $Summit.ID,
                    dates : {},
                    events: {},
                    summit_types: {},
                    speakers : {},
                    sponsors : {},
                    event_types:{},
                    locations : [],
                    locations_dictionary: {},
                    tags: {},
                    tracks : [],
                    tracks_dictionary : {},
                    presentation_levels: {},
                    current_user: null,
                    track_lists: [],
                    status_options: [],
                    selection_status_options: [],
                 };
                 <% loop $PresentationStatusOptions %>
                     summit.status_options.push("{$Status}");
                 <% end_loop %>
                 <% loop $PresentationSelectionStatusOptions %>
                    summit.selection_status_options.push("{$Status}");
                 <% end_loop %>
                 <% loop $Summit.getCategories() %>
                 summit.tracks.push(
                 {
                    id: {$ID},
                    name : "{$Title.JS}",
                 });
                 summit.tracks_dictionary[{$ID}]= {
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
                <% loop $Summit.TrackGroupLists %>
                summit.track_lists.push(
                {
                    name : "{$Category.Title.JS}",
                    id : "{$ID}",
                });
                <% end_loop %>
                <% loop $Summit.Types %>
                summit.summit_types[{$ID}] =
                {
                    type: "{$Type}",
                    name : "{$Title.JS}",
                    description : "{$Description.JS}",
                    color : "{$Color}"
                };
                <% end_loop %>
                <% loop $Summit.getTopVenues() %>
                    <% if ClassName == SummitVenue || ClassName == SummitExternalLocation || ClassName == SummitHotel  %>


                    summit.locations.push({
                        id:$ID,
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
                    });

                    summit.locations_dictionary[$ID] = {
                        id:$ID,
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
                            <% loop Rooms.sort('Name', 'ASC') %>
                            summit.locations.push({
                                id         : $ID,
                                class_name : "{$ClassName}",
                                name       : "{$Name.JS}",
                                capacity   : {$Capacity},
                                venue_id   : {$VenueID},
                            });

                            summit.locations_dictionary[$ID] = {
                                id         : $ID,
                                class_name : "{$ClassName}",
                                name       : "{$Name.JS}",
                                capacity   : {$Capacity},
                                venue_id   : {$VenueID},
                            };
                            <% end_loop %>
                        <% end_if %>
                    <% end_if %>
                <% end_loop %>
                <% loop $Summit.Dates %>
                summit.dates['{$Date}']  = { label: '{$Label}', date:'{$Date}', selected: false };
                summit.events['{$Date}'] = [];
                <% end_loop %>
                $(function() {

                    $('.unpublished-container').css('height', $(window).height());
                    $( window ).resize(function() {
                         $('.unpublished-container').css('height', $(window).height());
                    });

                    $(window).scroll(function () {
                        console.log('scroll '+$(this).scrollTop());
                        if ($(this).scrollTop() > 318) {
                            $('.unpublished-container').addClass('fixed');
                        } else {
                            $('.unpublished-container').removeClass('fixed');
                        }
                    });
                });

        </script>

        <div class="page-header">
            <h1>$Summit.Title<small></small></h1>
        </div>

        <div class="row" style="width:100%">
            <div class="col-md-6 published-container">
                <schedule-admin-view-published-filters summit="{ summit }"></schedule-admin-view-published-filters>
                <schedule-admin-view-published-bulk-actions summit="{ summit }" bulk_edition_url="{$Top.Link}/{$Summit.ID}/events/bulk-action" ></schedule-admin-view-published-bulk-actions>
                <schedule-admin-view-published start_time="07:00" end_time="22:00" interval="15" step="5" summit="{ summit }" minute_pixels="3" slot_width="100%"></schedule-admin-view-published>
                <schedule-admin-view-published-results summit="{ summit }"></schedule-admin-view-published-results>
                <schedule-admin-view-empty-spots summit="{ summit }"></schedule-admin-view-empty-spots>
            </div>
            <div class="col-md-6 unpublished-container">
                <schedule-admin-view-unpublished-filters summit="{ summit }"></schedule-admin-view-unpublished-filters>
                <schedule-admin-view-unpublished-bulk-actions bulk_edition_url="{$Top.Link}/{$Summit.ID}/events/bulk-action"></schedule-admin-view-unpublished-bulk-actions>
                <schedule-admin-view-unpublished summit="{ summit }" slot_width="500" interval="15" minute_pixels="3" slot_width="100%"></schedule-admin-view-unpublished>
            </div>
        </div>
    </div>
   <script src="summit/javascript/schedule/admin/schedule-admin-view.bundle.js?t={$Top.Time}"  type="application/javascript"></script>
</div>