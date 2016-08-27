<h2>Surveys</h2>
<% include SangriaPage_SurveyBuilderListFilter %>
<hr>
<table class="table table-hover">
    <thead>
        <tr>
            <th><a href="$Top.Link(SurveyBuilderListSurveys)?$Top.getOrderLink(id)" title="order by Id">Id<i class="fa fa-fw fa-sort"></i></a></th>
            <th><a href="$Top.Link(SurveyBuilderListSurveys)?$Top.getOrderLink(created)" title="order by Created Date">Created<i class="fa fa-fw fa-sort"></i></a></th>
            <th>Created By</th>
            <th>Organization</th>
            <th>Current Step</th>
            <th>Is Completed ?</th>
            <th>Has Deployments ?</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <% loop $Surveys %>
        <tr>
            <td>$ID</td>
            <td>$Created</td>
            <td>$CreatedBy.Email</td>
            <td>$getAnswerFor(Organization)</td>
            <td>$CurrentStep.Template.Name</td>
            <td><% if isCompleted %>true<% else %>false<% end_if %></td>
            <td>$EntitySurveysCount</td>
            <td><a href="$Top.Link(SurveyDetails)/$ID?BackUrl=$Top.Link(SurveyBuilderListSurveys)">view</a></td>
        </tr>
    <% end_loop %>
    </tbody>
</table>
$Pager
