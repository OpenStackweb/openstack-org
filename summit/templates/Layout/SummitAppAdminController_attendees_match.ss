<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active='attendees_match' %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="attendees-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/attendees">Attendees</a></li>
            <li class="active">Match</li>
        </ol>

        <div class="modal fade" id="match_attendee_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4>Match Attendee</h4>
                    </div>
                    <div class="modal-body">
                        <label for="match_attendee_member">Member</label><br>
                        <input id="match_attendee_member" style="width:100%" />
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default btn-default pull-right" onclick="matchAttendee()">
                            Match
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script type="application/javascript">
                var summit_id = $Summit.ID;
                var attendees = [];
                <% loop $Attendees %>
                    attendees.push(
                    {
                        name : "{$FirstName.JS} {$LastName.JS}",
                        email : "{$Email.JS}",
                        eventbrite_id : "{$ExternalAttendeeId.JS}",
                        amount_paid : {$Price},
                    });
                <% end_loop %>

                var total_attendees = {$TotalAttendees};
                var page_data = {page: 1, limit: 20, total_items: total_attendees}
        </script>

        <attendee-eventbrite-list attendees="{ attendees }" page_data="{ page_data }" summit_id="{ $Summit.ID }"></attendee-eventbrite-list>
    </div>
</div>

<script src="summit/javascript/schedule/admin/attendees-admin-view.bundle.js" type="application/javascript"></script>
