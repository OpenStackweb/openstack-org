<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active=2 %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="container-fluid summit-admin-container" >
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/attendees/">Attendees</a></li>
            <li class="active">$Attendee.ID</li>
        </ol>

        <form id="edit-attendee-form">
            <input type="hidden" id="summit_id" value="$Summit.ID" />
            <input type="hidden" id="attendee_id" value="$Attendee.ID" />

            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 member_container">
                        <label for="member">Member</label><br>
                        <input id="member" />
                    </div>
                    <div id="member_error" class="col-md-4" style="display:none">
                        Please select a member
                    </div>
                </div>
            </div>
            <label> Current Affiliation </label>
            <div class="form-group">
                <div class="row affiliation" <% if not $Attendee.Member %> style="display:none" <% end_if %>>
                    <div class="col-md-3">
                        <label for="aff_title">Title</label><br>
                        <input id="aff_title" class="form-control" value="{$Attendee.Member.currentAffiliation.JobTitle}"/>
                    </div>
                    <div class="col-md-3">
                        <label for="aff_company">Company</label><br>
                        <input id="aff_company" class="form-control" />
                    </div>
                    <div class="col-md-2">
                        <label>From</label><br>
                        <input id="aff_from" class="form-control" value="{$Attendee.Member.currentAffiliation.StartDate}" />
                    </div>
                    <div class="col-md-2">
                        <label>To</label><br>
                        <input id="aff_to" class="form-control" value="{$Attendee.Member.currentAffiliation.EndDate}" />
                    </div>
                    <div class="col-md-2 company_container">
                        <br>
                        <div class="checkbox">
                            <input id="aff_current" type="checkbox" <% if $Attendee.Member.currentAffiliation.Current %> checked <% end_if %>>
                            <label for="aff_current"> Current </label>
                        </div>
                    </div>
                </div>
                <div class="row no_affiliation" <% if $Attendee.Member %> style="display:none" <% end_if %>>
                    <div class="col-md-12">
                        No member selected
                    </div>
                </div>
            </div>
            <label> Attendee </label>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <input id="share_info" type="checkbox" <% if $Attendee.SharedContactInfo %> checked <% end_if %>>
                            <label for="share_info"> Shared Contact Info </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <input id="checked_in" type="checkbox" <% if $Attendee.SummitHallCheckedIn %> checked <% end_if %>>
                            <label for="checked_in"> Checked In </label>
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
            <label>Tickets </label> ( EventBrite Order# )<br>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <% if $Attendee.Tickets %>
                            <% loop $Attendee.Tickets %>
                                <div class="btn-group btn-group-xs ticket-btn">
                                    <a href="" class="ticket btn btn-default" data-toggle="modal" data-target="#ticket-modal" data-ticket="{$ID}">
                                        $ExternalOrderId
                                    </a>
                                    <a href="" class="del-ticket btn btn-danger" data-ticket="{$ID}">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                    </a>
                                </div>
                            <% end_loop %>
                        <% end_if %>
                        <a href="" id="add-ticket-btn" class="btn btn-default btn-xs" data-toggle="modal" data-target="#add-ticket-modal">
                            Add Ticket
                        </a>
                    </div>
                </div>
            </div>
            <br>
            <label> RSVPs </label><br>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <% if $Attendee.RSVPs() %>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Created</th>
                                    <th>Seat</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <% loop $Attendee.RSVPs() %>
                                    <tr>
                                        <td>$Event.Title</td>
                                        <td>$Created</td>
                                        <td>$SeatType</td>
                                        <td>
                                            <a href="" class="del-rsvp btn btn-xs btn-danger" data-rsvp="{$ID}">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <% end_loop %>
                            </tbody>
                        </table>
                        <% else %>
                            <i>No RSVPs</i>
                        <% end_if %>
                    </div>
                </div>
            </div>

            <hr>
            <h4> Speaker Details </h4>
            <br>

            <div class="speaker_details" <% if $Attendee.Member.Speaker.ID == 0 %> style="display:none" <% end_if %>>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Title</label>
                            <input id="title" class="form-control" value="$Attendee.Member.Speaker.Title" />
                        </div>
                        <div class="col-md-4">
                            <label>First Name</label>
                            <input id="first_name" class="form-control" value="$Attendee.Member.Speaker.FirstName" />
                        </div>
                        <div class="col-md-4">
                            <label>Last Name</label>
                            <input id="last_name" class="form-control" value="$Attendee.Member.Speaker.LastName" />
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

    <div id="ticket-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Ticket Details</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ticket_id" />
                    <div class="row">
                        <div class="col-md-4">
                            <label>EventBrite Order#</label><br>
                            <div id="ticket-external"></div>
                        </div>
                        <div class="col-md-4">
                            <label>EventBrite Attendee ID</label><br>
                            <div id="ticket-external-attendee"></div>
                        </div>
                        <div class="col-md-4">
                            <label>Ticket Type</label><br>
                            <div id="ticket-type"></div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6 ticket_member_container">
                            <label for="ticket-member">Assign to another Member</label><br>
                            <input id="ticket-member" />
                        </div>
                        <div id="ticket_member_error" class="col-md-6" style="display:none">
                            Please select a member
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="save_ticket" type="button" class="btn btn-default" data-dismiss="modal">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div id="add-ticket-modal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">New Ticket</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>EventBrite Order#</label><br>
                                <input id="add-ticket-id" class="form-control"/>
                            </div>
                            <div class="col-md-4">
                                <label>EventBrite Attendee ID</label><br>
                                <input id="add-ticket-attendee" class="form-control"/>
                            </div>
                            <div class="col-md-4">
                                <label>Ticket Type</label><br>
                                <select id="add-ticket-type" class="form-control">
                                <% loop $Summit.SummitTicketTypes() %>
                                    <option value="{$ID}"> $Name </option>
                                <% end_loop %>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="add_ticket" type="button" class="btn btn-default" data-dismiss="modal">Save</button>
                    </div>
                </div>
            </div>
        </div>

    <script type="text/javascript">
        var member = {};
        var company = {};
        <% if $Attendee.Member %>
            member = {id : "{$Attendee.MemberID}", name : "{$Attendee.Member.FirstName.JS} {$Attendee.Member.Surname.JS} ({$Attendee.Member.ID})"};
            <% if $Attendee.Member.currentAffiliation %>
                company = {id : "{$Attendee.Member.currentAffiliation.OrganizationID}", name : "{$Attendee.Member.currentAffiliation.Organization.Name}"};
            <% end_if %>
        <% end_if %>
    </script>

</div>