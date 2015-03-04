<div class="row">
    <div class="col-sm-12 survey-top" style="padding-left: 0 !important;padding-right: 0 !important;">
        Logged in as <strong>$CurrentMember.FirstName</strong>. &nbsp; <a href="{$link}logout" class="survey-logout">Log Out</a>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="padding-left: 0 !important;padding-right: 0 !important;">
        <ul class="survey-steps">
            <li><a id="aboutyou" href="{$Link(AboutYou)}" class="survey-step {$SurveyStepClass(AboutYou)}">About You</a></li>
            <li><a id="yourorganization" href="{$Link(YourOrganization)}" class="survey-step {$SurveyStepClass(YourOrganization)}">Your Organization</a></li>
            <li><a id="yourthoughts" href="{$Link(YourThoughts)}" class="survey-step {$SurveyStepClass(YourThoughts)}">Your Thoughts</a></li>
            <li><a id="appdevsurvey" href="{$Link(AppDevSurvey)}" class="survey-step {$SurveyStepClass(AppDevSurvey)}">Application Development</a></li>
            <li><a id="deployments" href="{$Link(Deployments)}" class="survey-step {$SurveyStepClass(Deployments)}">Your OpenStack Deployments</a></li>
            <li><a id="thankyou" href="{$Link(ThankYou)}" class="survey-step {$SurveyStepClass(ThankYou)}">Thank You!</a></li>
        </ul>
    </div>
</div>
