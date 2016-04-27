<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=5 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="reports-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li class="active">Reports</li>
        </ol>


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
            <% loop $Summit.Categories() %>
                tracks.push({
                    id: $ID,
                    title: "{$Title.JS}",
                });
            <% end_loop %>

        </script>
        <reports-admin-container report="presentation_report" limit="10" summit_id="{ $Summit.ID }" locations="{ locations }" tracks="{ tracks }"></reports-admin-container>

    </div>
</div>

<script src="summit/javascript/schedule/admin/reports-admin-view.bundle.js?t={$Top.Time}" type="application/javascript"></script>
