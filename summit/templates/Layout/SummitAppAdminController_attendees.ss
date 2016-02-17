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

        <script type="application/javascript">
                var attendees = [];
                <% loop $Summit.Attendees.Limit(20) %>
                    attendees.push(
                    {
                        member_id : "{$Member.ID}",
                        name : "{$Member.FullName.JS}",
                        email : "{$Member.Email.JS}",
                        ticket_bought : "{$Tickets.TicketBoughtDate.Format(j M, Y).JS}",
                        checked_in : <% if $SummitHallCheckedIn %> "Yes" <% else %> "No" <% end_if %>,
                        link: "$Top.Link/{$Summit.ID}/attendees/{$ID}"
                    });
                <% end_loop %>

                var total_attendees = {$Summit.Attendees().count};
                var page_data = {page: 1, limit: 20, total_items: total_attendees}
        </script>

        <attendee-list attendees="{ attendees }" page_data="{ page_data }" summit_id="{ $Summit.ID }"></attendee-list>
    </div>
</div>

<script src="summit/javascript/schedule/admin/attendees-admin-view.bundle.js" type="application/javascript"></script>
