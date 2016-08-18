<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=11 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="reports-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li class="active">Reports</li>
        </ol>

        <events-bulk-container list="presentations" limit="40" summit_id="{ $Summit.ID }" ></events-bulk-container>

    </div>
</div>

<script src="summit/javascript/schedule/admin/events-bulk-view.bundle.js?t={$Top.Time}" type="application/javascript"></script>
