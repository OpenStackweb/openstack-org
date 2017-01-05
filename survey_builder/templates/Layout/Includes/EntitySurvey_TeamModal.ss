<!-- Modal -->
<div class="modal fade" id="TeamModal" tabindex="-1" role="dialog" aria-labelledby="TeamModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="TeamModalLabel"><strong>Add team member to this deployment
                </strong></h4>
            </div>
            <div class="modal-body">
                <p><strong>Search by name</strong></p>
                <form id="formSearchTeamMember">
                    <input class="txt_autocomplete_member" type="text" placeholder="Find your team member on our database" value="" id="txt_autocomplete_member">
                    <input type="hidden" id="new-team-member-id"/>
                    <button type="button" class="btn select-team-member-btn">Add&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                </form>
                <div class="row" <% if not $TeamMembers %>style="display:none;"<% end_if %> id="team-members-container">
                    <div class="col-md-12">
                    <table class="table table-striped team-member-table">
                        <tbody id="team-members-body">
                            <% loop $TeamMembers %>
                            <tr>
                                <td><img width="50" height="50" src="{$ProfilePhotoUrl}"/></td>
                                <td>$FirstName</td>
                                <td>$Surname</td>
                                <td><a class="delete-team-member-btn" href="#" data-member-id="$ID">Delete</a></td>
                            </tr>
                            <% end_loop %>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <p>
                </p>
            </div>
        </div>
    </div>
</div>