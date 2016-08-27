<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=1 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" style="width: 80%;">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li class="active">$Summit.Name</li>
        </ol>
        <div class="page-header">
            <h1>$Summit.Title<small></small></h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span><h2 class="text-left"><i class="fa fa-calendar"></i>&nbsp;From&nbsp;$Summit.SummitBeginDate&nbsp;To&nbsp;$Summit.SummitEndDate</h2></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span><h2 class="text-left"><i class="fa fa-users"></i>&nbsp;Attendees&nbsp;$Summit.Attendees.Count</h2></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span><h2 class="text-left"><i class="fa fa-users"></i>&nbsp;Speakers&nbsp;$Summit.Speakers.Count</h2></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span><h2 class="text-left"><i class="fa fa-calendar-plus-o"></i>&nbsp;Submitted Events&nbsp;$Summit.Events.Count</h2></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span><h2 class="text-left"><i class="fa fa-calendar-check-o"></i>&nbsp;Published Events&nbsp;$Summit.PublishedEvents.Count</h2></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span><h2 class="text-left"><i class="fa fa-building"></i>&nbsp;Venues&nbsp;$Summit.VenuesCount</h2></span>
            </div>
        </div>
    </div>
<!-- /#page-content-wrapper -->
</div>