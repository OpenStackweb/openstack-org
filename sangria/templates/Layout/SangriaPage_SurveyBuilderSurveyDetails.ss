<a href="$BackUrl">Back</a>&nbsp;|&nbsp;<a href="javascript:window.print()">Print This Page</a>
<% with Survey %>
<h1>Survey # {$ID}</h1>
<hr>
<b>Created:</b>&nbsp;$Created<br>
<b>Last Updated:</b>&nbsp;$LastEdited<br>
<b>Email:</b>&nbsp;$CreatedBy.Email<br>
<% if Parent %>
    <b>Survey:</b>&nbsp;<a href="$Top.Link(SurveyDetails)/{$Parent.ID}?BackUrl={$Top.Link(DeploymentDetails)}/{$ID}" title="view associated survey"># $Parent.ID</a><br>
<% end_if %>
<% loop Steps %>

        <% if hasAnswers %>
            <h2>$Template.Name</h2>
        <% loop Answers %>
            <% if $Value %>
            <div>
            <% with Question %>
                <b>$Label :</b>
            <% end_with %>
            $FormattedAnswer
            </div>
            <% end_if %>
        <% end_loop %>
        <% end_if %>
<% end_loop %>
<% end_with %>