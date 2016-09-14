<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=12 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="reports-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li class="active">Room Metrics</li>
        </ol>


    </div>
</div>

