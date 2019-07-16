<h2>Surveys</h2>
<% include SangriaPage_SurveyBuilderListFilter ExportLink=$Top.getExportLink(SurveyBuilderListSurveysExport) %>
<br>
<hr>
<div class="row">
    <div class="col-md-3"> Total: $Total</div>
    <div class="col-md-3"> Completed: $Completed</div>
    <div class="col-md-3"> Deployments: $Deployments</div>
</div>
<hr>
<table class="table table-hover" style="width: 100% !important;">
    <thead>
        <tr>
            <th><a href="$Top.Link(SurveyBuilderListSurveys)?$Top.getOrderLink(id)" title="order by Id">Id<i class="fa fa-fw fa-sort"></i></a></th>
            <th><a href="$Top.Link(SurveyBuilderListSurveys)?$Top.getOrderLink(updated)" title="order by Updated Date">Updated<i class="fa fa-fw fa-sort"></i></a></th>
            <th>Created By</th>
            <th>Organization</th>
            <th>Current Step</th>
            <th>Is Completed ?</th>
            <th>Has Deployments ?</th>
            <th>Lang</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <% loop $Surveys %>
        <tr>
            <td>$ID</td>
            <td>$LastEdited</td>
            <td>$CreatedBy.Email</td>
            <td>$getAnswerFor(Organization)</td>
            <td>$CurrentStep.Template.Name</td>
            <td><% if isComplete %>true<% else %>false<% end_if %></td>
            <td>$EntitySurveysCount</td>
            <td>$Lang</td>
            <td><a href="$Top.Link(SurveyDetails)/$ID?BackURL=$Top.Link(SurveyBuilderListSurveys)">view</a></td>
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
