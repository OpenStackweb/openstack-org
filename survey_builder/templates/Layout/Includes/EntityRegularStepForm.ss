<form $AttributesHTML>
    <fieldset>
        <div class="row">
            <div class="col-md-12">
                <h3><span class="entity-survey-title-details">$_T("survey_ui", "Your Deployments")&nbsp;-&nbsp;$_T("survey_ui", "Part %1$s of %2$s", $CurrentStepIndex, $MaxStepIndex)</span></h3>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3 created-by">$_T("survey_ui","Created by")&nbsp;<strong>{$Survey.createdBy.Email}</strong></div>
            <div class="col-md-3 edited-by"><span class="team-separator">&#124;$_T("survey_ui","Edited by")&nbsp;</span><strong>{$Survey.EditedBy.Email}</strong></div>
            <div class="col-md-6 teams-container"><% if AllowTeams %><span class="team-separator">&#124;&nbsp;</span><i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;<a class="add-team-member-btn" href="#" title="Add another team member">$_T("survey_ui", "Add another team member")</a><% end_if %></div>
        </div>
        <hr>
        <% loop $Fields %>
            $FieldHolder
        <% end_loop %>
    </fieldset>
    <% if $Actions %>
        <div class="Actions row">
            <% if Actions.Count == 2 %>
                <div class="col-md-4 col-xs-12 col-sm-4">
                    &nbsp;
                </div>
            <% end_if %>
            <% loop $Actions %>
                <div class="col-md-4 col-xs-12 col-sm-4 <% if Last %>last<% end_if %><% if Mid %>middle<% end_if %>">
                    $Field
                </div>
            <% end_loop %>
            <div class="clear"><!-- --></div>
        </div>
    <% end_if %>
</form>
<% include EntitySurvey_TeamModal Survey=$Survey %>