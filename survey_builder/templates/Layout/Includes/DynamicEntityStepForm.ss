<form $AttributesHTML>
    <fieldset class="fake">
        <% loop $Fields %>
            $FieldHolder
        <% end_loop %>
        <% if EntitiesSurveys %>
            <% loop EntitiesSurveys %>
                <div class="deployment">
                    <img src="{$Top.EntityIconUrl}" width="30px" height="25px"/>
                    <a href="/surveys/current/{$Top.CurrentStep.Template.Title}/edit/{$ID}"
                       class="deployment-name">$Top.CurrentStep.Template.getEditEntityText</a>&nbsp;
                    <a class="delete-entity" href="/surveys/current/{$Top.CurrentStep.Template.Title}/delete/{$ID}">$Top.CurrentStep.Template.getDeleteEntityText</a>
                </div>
            <% end_loop %>
        <% end_if %>
    </fieldset>
    <p>
        <a class="roundedButton"
           href="/surveys/current/{$CurrentStep.Template.Title}/add-entity">$CurrentStep.Template.getAddEntityText</a>&nbsp;
        <a class="roundedButton" href="/surveys/current/{$CurrentStep.Template.Title}/skip-step">Done</a>
    </p>
</form>