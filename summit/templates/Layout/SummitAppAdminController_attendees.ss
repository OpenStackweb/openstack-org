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
            <li class="active">Attendees</li>
        </ol>
        <div class="panel panel-default">
            <div class="panel-heading">Attendee</div>
            <table class="table">
                <thead>
                <tr>
                    <th>Member Id</th>
                    <th>FullName</th>
                    <th>Email</th>
                    <th>Bought Date</th>
                    <th>Checked In?</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>John Due</td>
                    <td>jdue@gmail.com</td>
                    <td>5 Jun, 2015</td>
                    <td>No</td>
                    <td><a href="$Top.Link/{$Summit.ID}/attendees/1" class="btn btn-default btn-sm active" role="button">Edit</a></td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>John Due</td>
                    <td>jdue@gmail.com</td>
                    <td>5 Jun, 2015</td>
                    <td>No</td>
                    <td><a href="$Top.Link/{$Summit.ID}/attendees/1" class="btn btn-default btn-sm active" role="button">Edit</a></td>
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
    </div>
</div>