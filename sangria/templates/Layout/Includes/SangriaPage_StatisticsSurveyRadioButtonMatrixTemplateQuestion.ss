<div class="col-md-8" style="max-height: 325px; overflow-y: auto;">
    <h3>$Label</h3>
    <table>
        <table class="table" >
            <thead style="width: 100%">
                <tr>
                    <th>&nbsp;</th>
                    <% loop $Columns %>
                        <th>$Label</th>
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
                                <span>
                                    $Top.ParentPage.SurveyBuilderMatrixCountAnswers($Top.QuestionID, $Up.ID, $ID)&nbsp;$Top.ParentPage.SurveyBuilderMatrixPercentAnswers($Top.QuestionID, $Up.ID, $ID)
                                </span>
                            <% else %>
                                <a href="$Top.ParentPage.Link($Top.ParentPage.Action)?qid=$Top.QuestionID&vid={$Up.ID}:{$ID}{$Top.ParentPage.SurveyBuilderDateFilterQueryString}">
                                    $Top.ParentPage.SurveyBuilderMatrixCountAnswers($Top.QuestionID, $Up.ID, $ID)&nbsp;$Top.ParentPage.SurveyBuilderMatrixPercentAnswers($Top.QuestionID, $Up.ID, $ID)
                                </a>
                            <% end_if %>
                        </td>
                    <% end_loop %>
                </tr>
                <% end_loop %>
            </tbody>
    </table>
</div>