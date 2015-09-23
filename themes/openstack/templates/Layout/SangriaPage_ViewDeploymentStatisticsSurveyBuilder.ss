<select id="ddl_survey_templates">
    <% loop SurveyTemplates %>
        <option value="{$ID}">$Title</option>
    <% end_loop %>
</select>
<div>
$RenderCurrentFilters
    <% if $RenderCurrentFilters %>
    <a href="$Top.Link(ViewDeploymentStatisticsSurveyBuilder)?clear_filters=1">Clear Filters</a>
    <% end_if %>
</div>
<% loop EntitySurveyQuestions2Show %>
    <div class="span-8 ">
        <h3>$Label</h3>
        <table>
            <tbody>
            <% loop Values %>
                <tr>
                    <td>
                        <% if $Top.IsQuestionOnFiltering($Up.ID) %>
                            <span>$Label</span>
                        <% else %>
                        <a href="$Top.Link(ViewDeploymentStatisticsSurveyBuilder)?qid=$Up.ID&vid=$ID">$Label</a>
                        <% end_if %>
                    </td>
                    <td>$Top.SurveyBuilderCountAnswers($Up.ID, $ID)</td>
                </tr>
            <% end_loop %>
            </tbody>
        </table>
    </div>
<% end_loop %>