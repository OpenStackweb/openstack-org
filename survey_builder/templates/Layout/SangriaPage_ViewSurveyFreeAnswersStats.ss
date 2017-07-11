<h1>Survey Free Text Answers - Stats</h1>
<div id="openstack-sangria-survey-free-text-answerss-stats-app"></div>

<script>

    var labels = [];
    var data = [];
    var tags = [];

    <% loop $Data %>
        labels.push('{$Tag}');
        data.push({$Count});
    <% end_loop %>

    <% loop $Data.Sort(Tag) %>
        tags.push({
            id: {$ID},
            name: '{$Tag}',
            qty: '{$Count}',
            active: true
        });
    <% end_loop %>

    var question_title = '{$QuestionTitle}';

</script>
$ModuleJS("sangria-free-text-answers-list-stats", true , "survey_builder")
$ModuleCSS("sangria-free-text-answers-list-stats", true , "survey_builder")