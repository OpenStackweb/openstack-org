<h2>Deployments</h2>
<% include SangriaPage_SurveyBuilderListFilter ExportLink=$Top.getExportLink(SurveyBuilderListDeploymentsExport) %>
<br>
<hr>
<div class="row">
    <div class="col-md-3"> Total: $Total</div>
    <div class="col-md-3"> Completed: $Completed</div>
</div>
<hr>
<table class="table table-hover" style="width: 100% !important;">
    <thead>
    <tr>
        <th><a href="$Top.Link(SurveyBuilderListDeployments)?$Top.getOrderLink(id)" title="order by Id">Id<i class="fa fa-fw fa-sort"></i></a></th>
        <th><a href="$Top.Link(SurveyBuilderListDeployments)?$Top.getOrderLink(created)" title="order by Created Date">Created<i class="fa fa-fw fa-sort"></i></a></th>
        <th><a href="$Top.Link(SurveyBuilderListDeployments)?$Top.getOrderLink(updated)" title="order by Update Date">Last Edited<i class="fa fa-fw fa-sort"></i></a></th>
        <th>Created By</th>
        <th>Label</th>
        <th>Current Step</th>
        <th>Is Completed ?</th>
        <th>Lang</th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
        <% loop $Surveys %>
        <tr>
            <td>$ID</td>
            <td>$Created</td>
            <td>$LastEdited</td>
            <td>$CreatedBy.Email</td>
            <td>$getAnswerFor(Label)</td>
            <td>$CurrentStep.Template.Name</td>
            <td><% if isComplete %>true<% else %>false<% end_if %></td>
            <td>$Lang</td>
            <td><a href="$Top.Link(DeploymentDetails)/$ID?BackUrl=$Top.Link(SurveyBuilderListDeployments)">view</a></td>
        </tr>
        <% end_loop %>
    </tbody>
</table>
$Pager
<script>
    $(document).ready(function() {
        var form = $('#surveys_list_filter_form');
        $("#ddl_page_size").val($('#page_size').val());
        $('#ddl_page_size').change(function(){
            $('#page_size').val($(this).val());
            form.submit();
        });

    });
</script>