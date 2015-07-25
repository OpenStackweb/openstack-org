<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li class="active">$Summit.Name</li>
        </ol>
        <div class="page-header">
            <h1>$Summit.Title<small></small></h1>
        </div>
        <span><h2><i class="fa fa-users"></i>&nbsp;Attendees&nbsp;2000</h2></span>
        <span><h2><i class="fa fa-users"></i>&nbsp;Speakers&nbsp;200</h2></span>
        <span><h2><i class="fa fa-calendar-check-o"></i>&nbsp;Published Events&nbsp;100</h2></span>

        <div class="panel panel-default">
            <div class="panel-heading">Top Events</div>
            <div class="panel-body">
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Attendance</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        1
                    </td>
                    <td>
                        <a href="$Top.Link/{$Summit.ID}/events/2" >Working Group Session: Community App Catalog</a>
                    </td>
                    <td>
                        May 20 - 11:50am
                    </td>
                    <td>
                        May 20 - 12:30am
                    </td>
                    <td>
                        Presentation
                    </td>
                    <td>
                        Room 116/117
                    </td>
                    <td>
                        <span class="fa-stack fa-lg pull-left"><i class="fa fa-users fa-stack-1x "></i></span>200
                    </td>
                </tr>
                <tr>
                    <td>
                        2
                    </td>
                    <td>
                        <a href="$Top.Link/{$Summit.ID}/events/2" >Working Group Session: Community App Catalog</a>
                    </td>
                    <td>
                        May 20 - 11:50am
                    </td>
                    <td>
                        May 20 - 12:30am
                    </td>
                    <td>
                        Presentation
                    </td>
                    <td>
                        Room 116/117
                    </td>
                    <td>
                        <span class="fa-stack fa-lg pull-left"><i class="fa fa-users fa-stack-1x "></i></span>10
                    </td>
                </tr>
                </tbody>
            </table>
         </div>
    </div>
<!-- /#page-content-wrapper -->
</div>