<a href="$BackUrl">Back</a>&nbsp;|&nbsp;<a href="javascript:window.print()">Print This Page</a>
<% with Survey %>
<h1>$Top.Name # {$ID}</h1>
<hr>
<b>Created:</b>&nbsp;$Created<br>
<b>Last Updated:</b>&nbsp;$LastEdited<br>
<b>Email:</b>&nbsp;$CreatedBy.Email<br>
<b>Completed?: </b>&nbsp;<% if isComplete %>true<% else %>false<% end_if %><br>
<% if Parent %>
    <b>Survey:</b>&nbsp;<a href="$Top.Link(SurveyDetails)/{$Parent.ID}?BackURL={$Top.Link(DeploymentDetails)}/{$ID}" title="view associated survey"># $Parent.ID</a><br>
<% end_if %>
<% loop Steps %>
        <% if hasAnswers %>
            <br/>
            <h2>$Template.FriendlyName</h2>
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
<% if EntitiesSurveys %>
    <br/>
    <h2>Deployments</h2>
    <ul>
    <% loop EntitiesSurveys %>
        <li><a href="$Top.Link(DeploymentDetails)/{$ID}?BackURL={$Top.Link(SurveyDetails)}/{$Top.ID}&BackURL=$Top.BackUrl" title="view associated deployment"># $getFriendlyName</a></li>
    <% end_loop %>
    </ul>
<% end_if %>
<% end_with %>
<br/>
<br/>
<br/>
<hr/>