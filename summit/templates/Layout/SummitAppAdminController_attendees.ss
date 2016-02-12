<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="attendees-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li class="active">Attendees</li>
        </ol>
        <input id="summit_id" type="hidden" value="{$Summit.ID}" />
        <div class="panel panel-default">
            <div class="panel-heading">Attendee</div>
            <table id="attendees-table" class="table">
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
                    <% loop $Summit.Attendees.Limit(20) %>
                        <tr>
                            <td>$Member.ID</td>
                            <td>$Member.FullName</td>
                            <td>$Member.Email</td>
                            <td>$Tickets.TicketBoughtDate.Format(j M, Y)</td>
                            <td><% if $SummitHallCheckedIn %> Yes <% else %> No <% end_if %></td>
                            <td><a href="$Top.Link/{$Summit.ID}/attendees/{$ID}" class="btn btn-default btn-sm active" role="button">Edit</a></td>
                        </tr>
                    <% end_loop %>
                </tbody>
            </table>
        </div>
        <nav>
            <ul id="attendees-pager" class="pagination"></ul>
        </nav>
    </div>
</div>

<script>
    var total_attendees = {$Summit.Attendees().count};
</script>