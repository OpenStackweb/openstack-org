<form $AttributesHTML>
    <fieldset class="fake">
        <% if $Fields %>
        <div class="alert alert-success" role="alert">
            <div class="row">
                <div class="col-sm-1 col-icon-info"><i class="fa fa-info-circle" aria-hidden="true"></i></div>
                <div class="col-sm-11">
                    <% loop $Fields %>
                        $FieldHolder
                    <% end_loop %>
                </div>
            </div>
        </div>
        <% end_if %>
        <% if CanSkipStep %>
        <p>$_T("survey_ui", "If you do not wish to answer these questions, you may <a href=\"%s\">skip to the next section.</a>" , $SkipStepUr)</p>
        <% end_if %>
        <h2 class="entities-surveys-count-title">$_T("survey_ui", "Your deployments (%s)", $EntitiesSurveys.Count )</h2>
        <hr/>
        <% if EntitiesSurveys.Count == 0%>
        <p class="entites-surveys-subtitle">$_T("survey_ui", "You do not have any OpenStack deployment yet. <a href=\"%s\">Want to add one?</a>", "{$Controller.Link}{$CurrentStep.Template.Title}/add-entity" )</p>
        <% end_if %>
        <% if EntitiesSurveys %>
            <table class="table table-striped">
            <% loop EntitiesSurveys %>
                <tr>
                    <td width="10%">
                        <i class="fa fa-cloud" aria-hidden="true"></i>
                    </td>
                    <td width="70%">
                        <span class="entity-survey-title-row">$Top.EntityFriendlyName($ID)</span>
                    </td>
                    <td width="10%">
                        <a class="edit-entity-survey-btn" href="{$Top.Controller.Link}{$Top.CurrentStep.Template.Title}/edit/{$ID}">$_T("survey_template", $Top.CurrentStep.Template.getEditEntityText)</a>
                    </td>
                    <td width="10%">
                        <% if iAmOwner %>
                            <a class="delete-entity-survey-btn" href="{$Top.Controller.Link}{$Top.CurrentStep.Template.Title}/delete/{$ID}">$_T("survey_template", $Top.CurrentStep.Template.getDeleteEntityText)</a>
                        <% else %>
                            &nbsp;
                        <% end_if %>
                    </td>
                </tr>
            <% end_loop %>
            </table>
        <% end_if %>
    </fieldset>
    <p>
        <a class="roundedButton add-entity-survey-btn" href="{$Controller.Link}{$CurrentStep.Template.Title}/add-entity">{$_T("survey_template", $CurrentStep.Template.getAddEntityText)}&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i></a>&nbsp;
    </p>
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
        </div>
    <% end_if %>
</form>