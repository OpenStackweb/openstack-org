<div class="col-md-8 question_container" style="max-height: 325px; overflow-y: auto;">
    <h3>
        $Label
        (<a href="$Top.ParentPage.Link(exportQuestion)?qid={$Top.QuestionID}{$Top.ParentPage.SurveyBuilderDateFilterQueryString}" class="export_table" >export</a>)
    </h3>
    <p>N =  $Top.ParentPage.SurveyBuilderSurveyCountByQuestion($Top.QuestionID)</p>
    <table class="table" >
        <thead style="width: 100%">
            <tr>
                <th>&nbsp;</th>
                <% loop $Columns %>
                    <th colspan="2">$Label</th>
                <% end_loop %>
            </tr>
            <tr class="sub-header">
                <th>&nbsp;</th>
                <% loop $Columns %>
                    <th>#</th><th>%</th>
                <% end_loop %>
            </tr>
        </thead>
        <tbody style="width: 100%">
            <% loop $Rows %>
            <tr>
                <td>$Label</td>
                <% loop $Columns %>
                    <td>
                        <% if $Top.ParentPage.IsQuestionOnFiltering($Top.QuestionID ) %>
                            $Top.ParentPage.SurveyBuilderMatrixCountAnswers($Top.QuestionID, $Up.ID, $ID)
                        <% else %>
                            <a href="$Top.ParentPage.Link($Top.ParentPage.Action)?qid=$Top.QuestionID&vid={$Up.ID}:{$ID}{$Top.ParentPage.SurveyBuilderDateFilterQueryString}">
                                $Top.ParentPage.SurveyBuilderMatrixCountAnswers($Top.QuestionID, $Up.ID, $ID)
                            </a>
                        <% end_if %>
                    </td>
                    <td>
                        <% if $Top.ParentPage.IsQuestionOnFiltering($Top.QuestionID ) %>
                            <span>
                                $Top.ParentPage.SurveyBuilderMatrixPercentAnswers($Top.QuestionID, $Up.ID, $ID)
                            </span>
                        <% else %>
                            <a href="$Top.ParentPage.Link($Top.ParentPage.Action)?qid=$Top.QuestionID&vid={$Up.ID}:{$ID}{$Top.ParentPage.SurveyBuilderDateFilterQueryString}">
                                $Top.ParentPage.SurveyBuilderMatrixPercentAnswers($Top.QuestionID, $Up.ID, $ID)
                            </a>
                        <% end_if %>
                    </td>
                <% end_loop %>
            </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>