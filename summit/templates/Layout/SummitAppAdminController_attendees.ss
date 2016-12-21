<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active='attendees' %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="container-fluid summit-admin-container">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li class="active">Attendees</li>
        </ol>

        <div class="row" style="padding-bottom: 5px;">
            <div class="col-md-12">
                <button class="btn btn-success" data-toggle="modal" data-target="#add_attendee_modal">
                    Add Attendee
                </button>
            </div>
        </div>

        <div class="modal fade" id="add_attendee_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4>Add Attendee</h4>
                    </div>
                    <div class="modal-body">
                        <label for="add_attendee_member">Member</label><br>
                        <input id="add_attendee_member" style="width:100%" />
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default btn-default pull-right" onclick="addAttendee()">
                            Add
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script type="application/javascript">
                var summit_id = $Summit.ID;
                var attendees = [];
                <% loop $Summit.Attendees.Limit(20) %>
                    attendees.push(
                    {
                        id: "{$ID}",
                        member_id : "{$Member.ID}",
                        name : "{$Member.FullName.JS}",
                        email : "{$Member.Email.JS}",
                        eventbrite_id : "{$TicketIDs().JS}",
                        ticket_bought : "{$BoughtDate().JS}",
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

$ModuleJS('attendees-admin-view')
