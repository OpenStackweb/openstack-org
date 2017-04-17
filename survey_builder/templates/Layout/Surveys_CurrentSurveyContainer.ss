<h1 class="survey-subtitle">$_T("survey_template", $Survey.Template.Title)</h1>
<div class="container survey-container">
    <% include Surveys_Header %>
    <div class="row survey-step-container">
        $Top.SurveyStepForm
    </div>
    <% include Surveys_SaveLaterModal %>
</div>