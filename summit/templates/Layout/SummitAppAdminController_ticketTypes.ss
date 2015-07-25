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
            <li class="active">Ticket Types</li>
        </ol>
        <div class="panel panel-default">
            <div class="panel-heading">Ticket Types</div>
            <div class="panel-body">
                <p>Create tickets using <img style="height:30px;position:relative;top:8px;" src="http://eventbrite-s3.s3.amazonaws.com/marketing/landingpages/partnerdirectory/images/eb-logo.png"></p>
                <button type="button" class="btn btn-primary active btn-sm">
                    + Add Ticket Type
                </button>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th># Total</th>
                    <th>$ Cost</th>
                    <th>Sale Start Date</th>
                    <th>Sale End Date</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Full Summit Pass</td>
                    <td>2000</td>
                    <td>600 USD</td>
                    <td>5 Jun, 2015</td>
                    <td>20 Oct, 2015</td>
                </tr>
                <tr>
                    <td>Design Summit Pass</td>
                    <td>500</td>
                    <td>300 USD</td>
                    <td>5 Jun, 2015</td>
                    <td>20 Oct, 2015</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>