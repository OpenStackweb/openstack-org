<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active='events_bulk' %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="container-fluid summit-admin-container">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/events/schedule">Events</a></li>
            <li class="active">Bulk Update</li>
        </ol>

        <events-bulk-container list="presentations" limit="40" summit_id="{ $Summit.ID }" ></events-bulk-container>

    </div>
</div>

$ModuleJS('events-bulk-view')
