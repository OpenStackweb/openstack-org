<h1 class="survey-subtitle">$_T("survey_template", $Survey.Template.Title)</h1>
<div class="container container-thx-u-end">
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <p class="thx-u-end-txt">
                $_T("survey_ui", "Thank you for your submission. The OpenStack User Survey is open all year. As your
                cloud grows, visit <a href=\"%1$s\">%1$s</a> to update your deployment profile or update your existing
                survey.", $Link)
            </p>
            <p class="thx-u-end-txt">
                $_T("survey_ui", "If you'd like to review the results of the survey to see how other deployments stack
                up, check out our <a href=\"%1$s\">Survey Analytics page</a>.", $SurveyReportPage.Link)
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <a class="btn btn-primary back-home-button" href="{$Link}landing"> $_T("survey_ui", "Back to Home")</a>
        </div>
    </div>
</div>

