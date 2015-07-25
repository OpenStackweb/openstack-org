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
            <li class="active">Published Events</li>
        </ol>

        <div class="page-header">
            <h1>$Summit.Title<small></small></h1>
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
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
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
                        <span class="fa-stack fa-lg pull-left"><i class="fa fa-users fa-stack-1x "></i></span>0
                    </td>
                    <td>
                        <a href="$Top.Link/{$Summit.ID}/events/1" class="btn btn-default btn-sm active" role="button">Edit</a>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger active btn-sm">Unpublish</button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger active btn-sm">Delete</button>
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
                    <td>
                        <a href="$Top.Link/{$Summit.ID}/events/2" class="btn btn-default btn-sm active" role="button">Edit</a>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger active btn-sm">Unpublish</button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger active btn-sm">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <nav>
            <ul class="pagination">
                <li class="disabled">
                    <a href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li  class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li>
                    <a href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <!-- /#page-content-wrapper -->
</div>