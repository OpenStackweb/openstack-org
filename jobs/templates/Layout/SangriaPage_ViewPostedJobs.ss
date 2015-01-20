<h2 style="float:left;margin-top:10px;">Posted Jobs List ($PostedJobsCount)</h2>
<div style="float:right;">
    <a href="#" class="add-live-job roundedButton addDeploymentBtn">Create Job</a>
</div>
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
                <td class="url"><a href="{$BaseHref}community/jobs/view/{$ID}/{$TitleForUrl}">Link</a></td>
                <td class="company-name">$JobCompany</td>
                <td class="location_type">$JobLocation</td>
                <td class="is_foundation"><input class="foundation_check" job_id="{$ID}" type="checkbox" <% if FoundationJob == 1 %> checked <% end_if %> /></td>
                <td width="17%">
                    <a href="#" data-job-id="{$ID}" class="edit-live-job roundedButton addDeploymentBtn">Edit</a>
                    &nbsp;
                    <a href="#" data-job-id="{$ID}" class="delete-live-job roundedButton addDeploymentBtn">Delete</a>
                </td>
            </tr>
            <% end_loop %>
        </tbody>
    </table>
<% else %>
    <p>* There are not any Posted Jobs yet.</p>
<% end_if %>
<div id="edit_live_dialog" title="Edit Job Post" style="display: none;">
    $JobForm
</div>

<div id="dialog-delete-post" title="Delete Post ?" style="display: none">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to delete this job ?</p>
</div>