<% if Surveys %>
    <h2>User Surveys completed</h2>
    <% loop Surveys %>
        <p>
            <b>Org: $OrgName</b><br>
            Updated: $UpdateDate<br>
            <a href="{$Top.SurveysUrl}/{$ID}/edit" target="_blank">View Details</a>
        </p>
    <% end_loop %>
<% end_if %>

<% if Deployments %>
    <h2>Deployment profiles completed</h2>
    <% loop Deployments %>
        <p>
            <b>Org: $OrgName</b><br>
            Updated: $UpdateDate<br>
            Is Public:  $IsPublic<br>
            <% if IsPublic %>
                <a href="{$Top.DeploymentsUrl}/{$ID}/edit" target="_blank">View Details</a>
            <% else %>
                <a href="{$Top.SangriaDeploymentsUrl}?dep={$ID}" target="_blank">View Details</a>
            <% end_if %>
        </p>
    <% end_loop %>
<% end_if %>