<div class="row">
    <fieldset class="fake survey_step_form">
        $_T("survey_template", $CurrentStep.Template.Content)
        <% if CurrentMember.Password %>
        <% else %>
            <h2>$_T("survey_ui", "Create A Password For Return Visits")</h2>
            <p>$_T("survey_ui", "If you'd like to be able to return and update this information in the future, you may choose a password below.")</p>
            $SavePasswordForm
        <% end_if %>
    </fieldset>
</div>