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
                    current_user: null
                };
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
                <% loop $Summit.DatesWithEvents %>
                summit.dates['{$Date}']  = { label: '{$Label}', date:'{$Date}', selected: false };
                summit.events['{$Date}'] = [];
                <% end_loop %>
        </script>

        <div class="page-header">
            <h1>$Summit.Title<small></small></h1>
        </div>

        <div class="row">
            <div class="col-md-6">
                <schedule-admin-view-filters summit="{ summit }"></schedule-admin-view-filters>
                <schedule-admin-view start_time="06:00" end_time="24:00" interval="15" summit="{ summit }" minute_pixels="3" slot_width="500"></schedule-admin-view>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
   <script src="summit/javascript/schedule/admin/schedule-admin-view.bundle.js"  type="application/javascript"></script>
</div>