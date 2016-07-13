<form id="surveys_list_filter_form" class="form-inline">
    <div class="form-group">
        <select id="survey_template_id" name="survey_template_id" class="form-control">
            <option value="">--select a survey template --</option>
            <% loop $Templates %>
                <option value="{$ID}" <% if $Top.getRequestVar(survey_template_id) == $ID %>selected<% end_if %> >$QualifiedName</option>
            <% end_loop %>
        </select>
    </div>
    <div class="form-group">
        <select id="question_id" name="question_id"  class="form-control">
            <option value="">--select a question to filter by --</option>
            <% loop $Questions %>
                <option value="$ID" <% if $Top.getRequestVar(question_id) == $ID %>selected<% end_if %>>$Name</option>
            <% end_loop %>
        </select>
        <input <% if $Top.hasRequestVar(question_id) == 0 %>style="display: none"<% end_if %> class="form-control"  placeholder="set a question value" type="text" id="question_value" name="question_value" value="{$Top.getRequestVar(question_value)}">
    </div>
    <button type="button" id="btn_apply_survey_list_filter" class="btn btn-primary" title="Apply filtering">Apply</button>
</form>