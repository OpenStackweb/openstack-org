<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active='reports' %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="reports-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li><a href="$Top.Link">Home</a></li>
                <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
                <% if $Report %>
                    <li><a href="$Top.Link/{$Summit.ID}/reports">Reports</a></li>
                    <li class="active">$ReportName</li>
                <% else %>
                    <li class="active">Reports</li>
                <% end_if %>
            </ol>

            <% if not $Report %>
                <div class="row">
                    <div class="col-md-6">
                        <div class="list-group">
                            <a href="$Top.Link/{$Summit.ID}/reports/presentation_report" class="list-group-item">Presentation Report</a>
                            <a href="$Top.Link/{$Summit.ID}/reports/presentations_company_report" class="list-group-item">Presentations by Company</a>
                            <a href="$Top.Link/{$Summit.ID}/reports/presentations_by_track_report" class="list-group-item">Presentations by Track</a>
                            <a href="$Top.Link/{$Summit.ID}/reports/track_questions_report" class="list-group-item">Track Questions Report</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="list-group">
                            <a href="$Top.Link/{$Summit.ID}/reports/rsvp_report" class="list-group-item">RSVP Report</a>
                            <a href="$Top.Link/{$Summit.ID}/reports/room_report" class="list-group-item">Room Report</a>
                            <a href="$Top.Link/{$Summit.ID}/reports/speaker_report" class="list-group-item">Speaker Report </a>
                            <a href="$Top.Link/{$Summit.ID}/reports/video_report" class="list-group-item">Video Output List</a>
                        </div>
                    </div>
                </div>
            <% else %>
                <script type="application/javascript">
                    var locations = [];
                    <% loop $Summit.getTopVenues() %>
                        <% if ClassName == SummitVenue || ClassName == SummitExternalLocation || ClassName == SummitHotel  %>
                        locations.push({
                            id:$ID,
                            class_name : "{$ClassName}",
                            name       : "{$Name.JS}",
                        });

                            <% if ClassName == SummitVenue %>
                                <% loop Rooms.sort('Name', 'ASC') %>
                                locations.push({
                                    id         : $ID,
                                    class_name : "{$ClassName}",
                                    name       : "{$Name.JS}",
                                    venue_id   : {$VenueID},
                                });
                                <% end_loop %>
                            <% end_if %>
                        <% end_if %>
                    <% end_loop %>

                var tracks = [];
                    <% loop $Summit.getCategories() %>
                        tracks.push({
                            id: $ID,
                            title: "{$Title.JS}",
                        });
                    <% end_loop %>

                var report = "{$Report.JS}";

                </script>
                <reports-admin-container report="{ report }" limit="40" base_url="{$Top.Link}/{$Summit.ID}" summit_id="{$Summit.ID}" locations="{ locations }" tracks="{ tracks }"></reports-admin-container>

            <% end_if %>
        </div>
    </div>
</div>

<script src="summit/javascript/schedule/admin/reports-admin-view.bundle.js?t={$Top.Time}" type="application/javascript"></script>
