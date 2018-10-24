<h1>Survey Free Text Answers - Stats</h1>
<div id="openstack-sangria-survey-free-text-answerss-stats-app"></div>

<script>
    var data = {
        tags: [],
        question_title: "{$QuestionTitle}",
        total_answers: {$AnswerCount},
        template_id: {$TemplateID},
        question_id: {$QuestionID}
    }

    <% loop $Data %>
        data.tags.push({
            id: {$ID},
            name: "{$Tag}",
            qty: "{$Count}",
            active: true,
            answer_ids: "{$AnswerIDs}".split(',')
        });
    <% end_loop %>


</script>
$ModuleJS("sangria-free-text-answers-list-stats", true , "survey_builder")
$ModuleCSS("sangria-free-text-answers-list-stats", true , "survey_builder")