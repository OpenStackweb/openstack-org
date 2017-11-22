<h2>$CompanyName Team Members Administration</h2>

<form name="ccla_teams_form" id="ccla_teams_form">
<div class="status-legend-container">
    <div style="float:left" class="status-base needs-registration"></div><div class="status-legend">Needs Registration</div>
    <div style="float:left" class="status-base needs-confirmation"></div><div class="status-legend">Needs Confirmation</div>
    <div style="float:left" class="status-base member"></div><div class="status-legend">Is Member</div>
</div>
<table id="ccla_teams" class="table table-stripped">
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Team</th>
            <th>Date Added</th>
            <th>&nbsp;</th>
            <th width="125px">&nbsp;</th>
        </tr>
        <tr id="add_member_row">
            <td>
                <input type="text" id="add_member_fname" name="add_member_fname" class="form-control">
            </td>
            <td>
                <input type="text" id="add_member_lname" name="add_member_lname" class="form-control">
            </td>

            <td>
                <input type="text" id="add_member_email" name="add_member_email" class="form-control">
            </td>
            <td>
                $TeamsDLL
            </td>
            <td colspan="3">
                <button id="add_member" class="btn btn-default">Add</button>
            </td>
        </tr>
    </thead>
    <tfoot>
    </tfoot>
    <tbody>
    <% if TeamMembers %>
        <% loop TeamMembers %>
            <tr data-id="{$Id}">
                <td>$FirstName</td>
                <td>$LastName</td>
                <td>$Email</td>
                <td>$TeamName</td>
                <td class="invitation-date">$DateAdded</td>
                <td><div class="status-base {$Status}" title="{$Status}"></div></td>
                <td>
                    <% if $Status != 'member' %>
                        <button class="resend_invitation btn btn-default btn-xs" data-id="{$Id}">
                            Resend
                        </button>
                    <% end_if %>
                    <button class="delete_member btn btn-danger btn-xs" data-team-id="{$TeamId}" data-id="{$Id}" data-status="{$Status}">
                        Delete
                    </button>
                </td>
            </tr>
        <% end_loop %>
    <% end_if %>
    </tbody>
</table>
</form>