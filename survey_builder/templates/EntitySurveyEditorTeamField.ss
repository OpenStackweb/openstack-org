<div id="$ID" class="entity_survey_editors_team_container" $AttributesHTML>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <input type="text" id="member-auto-complete" class="form-control ss-member-autocomplete-field" placeholder="Member Identifier">
                <input type="hidden" id="new-team-member-id"/>
            </div>
            <div class="col-md-6">
                <button class="btn btn-primary active btn-sm" id="add-new-member">+ Add Team Member</button>
            </div>
        </div>

        <div class="row" id="team-members-container"   <% if not TeamMembers %>style="display:none;"<% end_if %> >
            <div class="col-md-12">
                <table class="table">
                    <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Username</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="team-members-body">
                    <% loop TeamMembers %>
                    <tr>
                        <td>$FirstName</td>
                        <td>$Surname</td>
                        <td>$Email</td>
                        <td><button class="btn btn-danger active btn-sm delete-team-member" data-member-id="$ID">Delete</button></td>
                    </tr>
                    <% end_loop %>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
</div>