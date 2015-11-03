<form $AttributesHTML>
    <fieldset class="fake">
        <% loop $Fields %>
            $FieldHolder
        <% end_loop %>
        <% if EntitiesSurveys %>
            <div class="container-fluid">
            <% loop EntitiesSurveys %>
                <div class="deployment row">
                    <div class="col-md-1">
                        <img src="{$Top.EntityIconUrl}" width="30px" height="25px"/>
                    </div>
                    <div class="col-md-9">
                    <span>$Top.EntityFriendlyName($ID)</span>
                    </div>
                    <div class="col-md-1">
                        <a class="btn btn-primary active btn-sm" href="{$Top.Controller.Link}{$Top.CurrentStep.Template.Title}/edit/{$ID}">$Top.CurrentStep.Template.getEditEntityText</a>&nbsp;
                    </div>
                    <div class="col-md-1">
                    <% if iAmOwner %>
                    <a class="delete-entity btn btn-danger active btn-sm" href="{$Top.Controller.Link}{$Top.CurrentStep.Template.Title}/delete/{$ID}">$Top.CurrentStep.Template.getDeleteEntityText</a>
                    <% end_if %>
                    </div>
                </div>
            <% end_loop %>
            </div>
        <% end_if %>
    </fieldset>
    <p>
        <a class="roundedButton" href="{$Controller.Link}{$CurrentStep.Template.Title}/add-entity">$CurrentStep.Template.getAddEntityText</a>&nbsp;
        <a class="roundedButton" href="{$Controller.Link}{$CurrentStep.Template.Title}/skip-step">Done</a>
    </p>
</form>