Submitter
Name: {$SubmitterName}<br>
Email: {$SubmitterEmail}

<h3>{$JobTitle}:</h3>
{$JobSummary}

Review <a href="{$ReviewLink}">here</a>

<% if $Rejected %>
    <br><br>
    <span>THIS JOB WAS AUTOMATICALLY REJECTED</span>
<% end_if %>