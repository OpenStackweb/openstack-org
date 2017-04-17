<div id="$ID" class="entity_survey_editors_team_container" $AttributesHTML>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <input type="text" id="member-auto-complete" class="form-control ss-member-autocomplete-field" placeholder="{$_T("survey_ui","Find your team member in our database")}">
                <input type="hidden" id="new-team-member-id"/>
            </div>
            <div class="col-md-6">
                <button class="btn btn-primary active btn-sm" id="add-new-member">$_T("survey_ui", "+ Add Team Member")</button>
            </div>
        </div>

        <div class="row" <% if not TeamMembers %>style="display:none;"<% end_if %> id="team-members-container">
            <div class="col-md-12">
                <table class="table team-member-table">
                    <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>$_T("survey_ui","First Name")</th>
                        <th>$_T("survey_ui","Last Name")</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="team-members-body">
                    <% loop TeamMembers %>
                    <tr>
                        <td><img width="50" height="50" src="{$ProfilePhotoUrl}"/></td>
                        <td>$FirstName</td>
                        <td>$Surname</td>
                        <td><button class="btn btn-danger active btn-sm delete-team-member" data-member-id="$ID">$_T("survey_ui","Delete")</button></td>
                    </tr>
                    <% end_loop %>
                    </tbody>
                </table>
            </div>
        </div>

        </div>
</div>