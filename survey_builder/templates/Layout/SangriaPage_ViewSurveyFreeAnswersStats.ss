<h1>Survey Free Text Answers - Stats</h1>
<div id="openstack-sangria-survey-free-text-answerss-stats-app"></div>

<script>

    var labels = [];
    var data = [];

    <% loop $Data %>
        labels.push('{$Tags}');
        data.push({$Count});
    <% end_loop %>

    var options = {
        title: {
            display: true,
            text: '{$QuestionTitle}'
        }
    }
</script>
$ModuleJS("sangria-free-text-answers-list-stats", true , "survey_builder")
$ModuleCSS("sangria-free-text-answers-list-stats", true , "survey_builder")