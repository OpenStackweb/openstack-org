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

        </script>
        <reports-admin-container report="presentation_report" limit="10" summit_id="{ $Summit.ID }"></reports-admin-container>

    </div>
</div>

<script src="summit/javascript/schedule/admin/reports-admin-view.bundle.js" type="application/javascript"></script>
