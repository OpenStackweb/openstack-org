<div class="row">
    <div class="col-lg-12">
        <form id="surveys_list_filter_form" class="form-inline">
            <div class="form-group">
                <select id="survey_template_id" name="survey_template_id" class="form-control" style="width: 300px">
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
                        <option title="{$Label.XML}" <% if $JSONValues %>data-values='{$JSONValues}'<% end_if %> data-type="{$Type}" value="$ID" <% if $Top.getRequestVar(question_id) == $ID %>selected<% end_if %>>$Name</option>
                    <% end_loop %>
                </select>
                <input style="display: none;width: 300px;" class="form-control"  placeholder="set a question value" type="text" id="question_text_value"  name="question_text_value" value="">
                <select multiple style="display: none; width: 300px;" class="form-control" placeholder="select a question value" type="text" id="question_select_values" name="question_select_values[]" value=""></select>
            </div>
            <div class="form-group">
                <select id="survey_lang" name="survey_lang" class="form-control">
                    <option selected value="ALL">All Languages</option>
                    <option value="zh_TW">Chinese Traditional</option>
                    <option value="ko_KR">Korean</option>
                    <option value="zh_CN">Chinese Simplified</option>
                    <option value="id_ID">Indonesian</option>
                    <option value="ja_JP">Japanese</option>
                    <option value="de_DE">German</option>
                    <option value="fr_FR">French</option>
                    <option value="ru_RU">Russian</option>
                </select>
            </div>
            <input type="hidden" name="page_size" id="page_size" value="{$Top.PageSize}">
            <button type="button" id="btn_apply_survey_list_filter" class="btn btn-primary" title="Apply filtering">Apply</button>
        </form>
    </div>
</div>
<div class="row" style="padding-top: 20px">
    <div class="col-lg-12">
        <a class="btn btn-default" role="button" href="$Top.ExportLink" title="export data">Export</a>
    </div>
</div>
