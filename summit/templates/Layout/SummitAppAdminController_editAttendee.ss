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
                </div>
            </div>
            <label> Current Affiliation </label>
            <div class="form-group">
                <div class="row affiliation" <% if not $Attendee.Member %> style="display:none" <% end_if %>>
                    <div class="col-md-3">
                        <label for="aff_company">Company</label><br>
                        <input id="aff_company" />
                    </div>
                    <div class="col-md-3">
                        <label>From</label><br>
                        <input id="aff_from" value="{$Attendee.Member.currentAffiliation.StartDate}" />
                    </div>
                    <div class="col-md-3">
                        <label>To</label><br>
                        <input id="aff_to" value="{$Attendee.Member.currentAffiliation.EndDate}" />
                    </div>
                    <div class="col-md-3 company_container">
                        <br>
                        <div class="checkbox">
                            <label> <input id="aff_current" type="checkbox" <% if $Attendee.Member.currentAffiliation.Current %> checked <% end_if %>> Current </label>
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
                                $ExternalOrderId <br>
                            <% end_loop %>
                        <% else %>
                            <i>None</i>
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

    <script>
        var member = {};
        var company = {};
        <% if $Attendee.Member %>
        member = {id : "{$Attendee.MemberID}", name : "{$Attendee.Member.FirstName.JS} {$Attendee.Member.Surname.JS} ({$Attendee.Member.ID})"};
        company = {id : "{$Attendee.Member.currentAffiliation.OrganizationID}", name : "{$Attendee.Member.currentAffiliation.Organization.Name}"};
        <% end_if %>
    </script>

</div>