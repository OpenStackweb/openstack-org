<div class="container">
    <div class="row">
        <div class="col-md-12 survey_builder_statistics_filters">
        <form id="survey_template_form" name="survey_template_form" action="$Link($Action)" method="post">
        <select id="ddl_survey_templates" name="survey_template_id">
            <option value="-1">-- select a survey --</option>
            <% loop SurveyBuilderSurveyTemplates($ClassName) %>
                <option value="{$ID}" <% if $Top.IsSurveyTemplateSelected($ID) %>selected<% end_if %> >$QualifiedName</option>
            <% end_loop %>
        </select>
        </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 survey_builder_questions_values_statistics_filters">
            <h2>{$Top.SurveyBuilderLabelSubmitted} &mdash; {$Top.SurveyBuilderSurveyCount} total</h2>
            $DateFilters
            $RenderCurrentFilters
            <% if $RenderCurrentFilters %>
            <a href="$Top.Link($Top.Action)?clear_filters=1">Clear Filters</a>
            <% end_if %>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <% if SurveyQuestions2Show %>
            <div class="row">
                <% loop SurveyQuestions2Show %>
                    <div class="col-md-3 ">
                        <h3>$Label</h3>
                        <table>
                            <tbody>
                            <% loop Values %>
                                <tr>
                                    <td>
                                        <% if $Top.IsQuestionOnFiltering($Up.ID) %>
                                            <span>$Label</span>
                                        <% else %>
                                        <a href="$Top.Link($Top.Action)?qid=$Up.ID&vid=$ID$Top.SurveyBuilderDateFilterQueryString">$Label</a>
                                        <% end_if %>
                                    </td>
                                    <td>$Top.SurveyBuilderCountAnswers($Up.ID, $ID)</td>
                                </tr>
                            <% end_loop %>
                            </tbody>
                        </table>
                    </div>
                <% end_loop %>
            </div>
        <% end_if %>
        </div>
    </div>
</div>