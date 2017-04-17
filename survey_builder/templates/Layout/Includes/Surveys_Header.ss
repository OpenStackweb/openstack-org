<div class="row">
    <div class="col-sm-12 survey-top">
        <p>$_T("survey_ui", "Step <strong>%s of %s</strong> for <strong>%s</strong>",$Survey.getCurrentStepIndexNice, $Survey.getStepsCount, $CurrentMember.FullName)</p>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="padding-left: 0 !important;padding-right: 0 !important;">
        <ul class="survey-steps">
            <% loop Survey.getAvailableSteps %>
                <li class="{$Top.SurveyStepClass($Template.Name)}">
                    <a data-step-name='{$Template.Name}' id="$Template.Name" href="$Top.Link{$Template.Name}" class="survey-step {$Top.SurveyStepClass($Template.Name)}">
                        $_T("survey_template", $Template.FriendlyName)
                        <i class="navigation-icon fa {$Top.SurveyStepClassIcon($Template.Name)}" aria-hidden="true"></i>
                    </a>
                </li>
            <% end_loop %>
        </ul>
    </div>
</div>