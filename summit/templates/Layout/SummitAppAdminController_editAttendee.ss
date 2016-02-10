<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="edit-attendee-wrapper" >
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/events/published">Published Events</a></li>
            <li class="active">$Event.Title</li>
        </ol>

        <form id="edit-attendee-form">
            <input type="hidden" id="summit_id" value="$Summit.ID" />
            <input type="hidden" id="attendee_id" value="$Attendee.ID" />

            <h1> Attendee </h1>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 member_container">
                        <label for="member">Member</label><br>
                        <input id="member" />
                    </div>
                </div>
            </div>
            <label> Attendee </label>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label> <input id="share_info" type="checkbox" <% if $Attendee.SharedContactInfo %> checked <% end_if %>> Shared Contact Info </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label> <input id="checked_in" type="checkbox" <% if $Attendee.SummitHallCheckedIn %> checked <% end_if %>> Checked In </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <% if $Attendee.SummitHallCheckedIn %>
                        <label> Checked In Time </label>
                        $Attendee.SummitHallCheckedInDate
                        <% end_if %>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label>Tickets</label><br>
                        <% if $Attendee.Tickets %>
                            <% loop $Attendee.Tickets %>
                                <a href="">$ExternalOrderId</a>
                            <% end_loop %>
                        <% else %>
                            <i>None</i>
                        <% end_if %>
                    </div>
                </div>
            </div>
            <hr>

            $AffiliationField.FieldHolder()

            <hr>
            <h1> Speaker Details </h1>

            <div class="speaker_details" <% if $Attendee.Member.Speaker.ID == 0 %> style="display:none" <% end_if %>>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Title</label>
                            <input id="title" value="$Attendee.Member.Speaker.Title" />
                        </div>
                        <div class="col-md-4">
                            <label>First Name</label>
                            <input id="first_name" value="$Attendee.Member.Speaker.FirstName" />
                        </div>
                        <div class="col-md-4">
                            <label>Last Name</label>
                            <input id="last_name" value="$Attendee.Member.Speaker.LastName" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Bio</label><br>
                            <textarea id="bio">
                                $Attendee.Member.Speaker.Bio
                            </textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group no_speaker" <% if $Attendee.Member.Speaker.ID > 0 %> style="display:none" <% end_if %> >
                <div class="row">
                    <div class="col-md-12 no_speaker_msg">
                    <% if $Attendee.Member %> This member is not a speaker <% else %> No member selected <% end_if %>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
    <!-- /#page-content-wrapper -->

    <script>
        var member = {};
        <% if $Attendee.Member %>
        member = {id : "{$Attendee.MemberID}", name : "{$Attendee.Member.FirstName.JS} {$Attendee.Member.Surname.JS} ({$Attendee.Member.ID})"};
        <% end_if %>
    </script>

</div>