<h2>Posted Jobs List ($PostedJobsCount)</h2>
<% if PostedJobs %>
    <table id="posted-jobs-table">
        <thead>
        <tr>
            <th class="title">Title</th>
            <th>Post Date</th>
            <th class="url">Url</th>
            <th>Company</th>
            <th>Job Location</th>
            <th>Foundation Job</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
            <% loop PostedJobs %>
            <tr>
                <td class="title"><a id="job{$ID}" href="#"></a>$Title</td>
                <td class="post-date">$JobPostedDate</td>
                <td class="url"><a href="$JobMoreInfoLink">Link</a></td>
                <td class="company-name">$JobCompany</td>
                <td class="location_type">$JobLocation</td>
                <td class="is_foundation"><input class="foundation_check" job_id="{$ID}" type="checkbox" <% if FoundationJob == 1 %> checked <% end_if %> /></td>
                <td width="23%">
                    <a href="#" data-request-id="{$ID}" class="edit-job roundedButton addDeploymentBtn">Edit</a>
                    &nbsp;
                    <a href="#" data-request-id="{$ID}" class="delete-job roundedButton addDeploymentBtn">Delete</a>
                </td>
            </tr>
            <% end_loop %>
        </tbody>
    </table>
<% else %>
    <p>* There are not any Posted Jobs yet.</p>
<% end_if %>
<div id="edit_dialog" title="Edit Job Post" style="display: none;">
    $JobRegistrationRequestForm
</div>

<div id="dialog-confirm-post" title="Post Job?" style="display: none;">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to post this job request?</p>
</div>

<div id="dialog-reject-post" title="Reject Post ?" style="display: none;">
    <form>
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to reject this job request?</p>
        <div>
            <input id="send_rejection_email" name="send_rejection_email" type="checkbox">send email on rejection to contact point<br>
            <label for"custom_reject_message">Additional Reject Message:</label>
            <textarea style="height: 150px; width: 410px;resize:none;" id="custom_reject_message" name="custom_reject_message"></textarea>
        </div>
    </form>
</div>