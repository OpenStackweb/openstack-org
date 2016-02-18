<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID %>
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
                    locations : {},
                    tags: {},
                    tracks : {},
                    presentation_levels: {},
                    current_user: null,
                    track_lists: {},
                    status_options: [],
                 };
                 <% loop $PresentationStatusOptions %>
                     summit.status_options.push("{$Status}");
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
                <% loop $Summit.TrackGroupLists %>
                summit.track_lists[{$ID}] =
                {
                    name : "{$Category.Title.JS}",
                };
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
                <% loop $Summit.Dates %>
                summit.dates['{$Date}']  = { label: '{$Label}', date:'{$Date}', selected: false };
                summit.events['{$Date}'] = [];
                <% end_loop %>
                $(function() {

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
                <schedule-admin-view-published start_time="06:00" end_time="23:45" interval="15" summit="{ summit }" minute_pixels="3" slot_width="100%"></schedule-admin-view-published>
                <schedule-admin-view-published-results summit="{ summit }"></schedule-admin-view-published-results>
                <schedule-admin-view-empty-spots summit="{ summit }"></schedule-admin-view-empty-spots>
            </div>
            <div class="col-md-6 unpublished-container">
                <schedule-admin-view-unpublished-filters summit="{ summit }"></schedule-admin-view-unpublished-filters>
                <schedule-admin-view-unpublished summit="{ summit }" slot_width="500" interval="15" minute_pixels="3" slot_width="100%"></schedule-admin-view-unpublished>
            </div>
        </div>
    </div>
   <script src="summit/javascript/schedule/admin/schedule-admin-view.bundle.js"  type="application/javascript"></script>
</div>