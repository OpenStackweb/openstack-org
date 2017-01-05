<h1>$Survey.Template.Title : $Survey.CurrentStep.Template.FriendlyName</h1>
<div class="container survey-container">
    <% include Surveys_Header %>
    <div class="row survey-step-container">
        $Top.SurveyDynamicEntityStepForm
    </div>
    <% include Surveys_SaveLaterModal %>
</div>