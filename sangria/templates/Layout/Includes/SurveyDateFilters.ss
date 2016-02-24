<fieldset>
    <input type="hidden" class="date_filter_action" value="$action" />
    <% if use_subset %>
    <div id="subset" class="field text date inline" style="width: 310px !important;">
        <label for="range">Select version of survey</label>
        <div class="middleColumn">
            <% include SangriaPage_SurveySelector %>
        </div>
    </div>
    <% end_if %>
    <div id="date-from" class="field text date inline">
        <label class="left" for="SurveyDateFilters_date-from">Start Date</label>
        <div class="middleColumn">
            <input type="text" class="text date inline date_filter_date-from" id="SurveyDateFilters_date-from" value="$start_date">
        </div>
    </div>
    <div id="date-to" class="field text date inline">
        <label class="left" for="SurveyDateFilters_date-to">End Date</label>
        <div class="middleColumn">
            <input type="text" class="text date inline date_filter_date-to" id="SurveyDateFilters_date-to" value="$end_date">
        </div>
    </div>
    <div class="Actions inline">
        <input class="action submit_filters date_filter_submit" type="button" value="Go !" title="Go !">
    </div>
    <div class="clear"><!-- --></div>
</fieldset>
