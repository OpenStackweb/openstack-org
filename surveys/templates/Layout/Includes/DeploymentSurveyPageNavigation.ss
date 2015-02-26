<div class="row">
    <div class="col-sm-12 survey-top" style="padding-left: 0 !important;padding-right: 0 !important;">
        Logged in as <strong>$CurrentMember.FirstName</strong>. &nbsp; <a href="{$link}logout" class="survey-logout">Log Out</a>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="padding-left: 0 !important;padding-right: 0 !important;">
        <ul class="survey-steps">
            <li><a id="aboutyou" href="{$Link(AboutYou)}" class="current survey-step">About You</a></li>
            <li><a id="yourorganization" href="{$Link(YourOrganization)}" class="future survey-step">Your Organization</a></li>
            <li><a id="yourthoughts" href="{$Link(YourThoughts)}" class="future survey-step">Your Thoughts</a></li>
            <li><a id="appdevsurvey" href="{$Link(AppDevSurvey)}" class="future survey-step">Application Development</a></li>
            <li><a id="deployments" href="{$Link(Deployments)}" class="future survey-step">Your OpenStack Deployments</a></li>
            <li><a id="thankyou" href="{$Link(ThankYou)}" class="future survey-step">Thank You!</a></li>
        </ul>
    </div>
</div>
