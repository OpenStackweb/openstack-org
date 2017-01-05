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
        <p>If you do not wish to answer these questions, you may <a href="{$SkipStepUrl}">skip to the next section.</a></p>
        <% end_if %>
        <h2 class="entities-surveys-count-title">Your {$EntityNameLowerCasePlural} ({$EntitiesSurveys.Count})</h2>
        <hr/>
        <% if EntitiesSurveys.Count == 0%>
        <p class="entites-surveys-subtitle">You do not have any OpenStack {$EntityNameLowerCasePlural} yet. <a href="{$Controller.Link}{$CurrentStep.Template.Title}/add-entity">Want to add one?</a></p>
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
                        <a class="edit-entity-survey-btn" href="{$Top.Controller.Link}{$Top.CurrentStep.Template.Title}/edit/{$ID}">$Top.CurrentStep.Template.getEditEntityText</a>
                    </td>
                    <td width="10%">
                        <% if iAmOwner %>
                            <a class="delete-entity-survey-btn" href="{$Top.Controller.Link}{$Top.CurrentStep.Template.Title}/delete/{$ID}">$Top.CurrentStep.Template.getDeleteEntityText</a>
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
        <a class="roundedButton add-entity-survey-btn" href="{$Controller.Link}{$CurrentStep.Template.Title}/add-entity">{$CurrentStep.Template.getAddEntityText}&nbsp;&nbsp;<i class="fa fa-plus-circle" aria-hidden="true"></i></a>&nbsp;
    </p>
    <% if $Actions %>
        <div class="Actions">
            <% loop $Actions %>
                $Field
            <% end_loop %>
        </div>
    <% end_if %>
</form>