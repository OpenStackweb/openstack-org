<script>
    var pageSize         = 25;
    var templates = [
        <% loop SurveyTemplates %>
            {
                id: $ID,
                name: '{$Title}'
            },
        <% end_loop %>
    ];
</script>
<div id="openstack-sangria-survey-free-text-answers-app"></div>
$ModuleJS("sangria-free-text-answers-list", false , "survey_builder")
$ModuleCSS("sangria-free-text-answers-list", false , "survey_builder")