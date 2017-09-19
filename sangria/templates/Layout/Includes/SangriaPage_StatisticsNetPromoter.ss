<div class="col-md-4 "style="height:450px">
    <h3>$Question.Label</h3>
    <p>
        N =  $Top.ParentPage.SurveyBuilderSurveyCountNPS($Top.Question.ID)
        &nbsp; / &nbsp;
        <% loop $Top.ParentPage.SurveyBuilderSurveyNPS($Top.Question.ID) %>
            <% if $Label == NPS %>
                $Label: $Value
            <% else %>
                <% if $Top.ParentPage.IsQuestionOnFiltering(nps) %>
                    $Label: $Value%
                <% else %>
                    <a href="$Top.ParentPage.Link($Top.ParentPage.Action)?qid=nps&vid=$Top.Question.ID:$Label$Top.ParentPage.SurveyBuilderDateFilterQueryString">
                        $Label: $Value%
                    </a>
                <% end_if %>
                &nbsp;|&nbsp;
            <% end_if %>
        <% end_loop %>
    </p>

    <div class="row" style="text-align:center">
        <div class="col-md-4">
        <% loop $Question.getFormattedValues %>
            <% if $Label == 7 || $Label == 9 %>
                </div>
                <div class="col-md-4" style="border-left: 1px solid #ddd">
            <% end_if %>
            <div class="row" style="border-top: 1px solid #ddd;padding: 8px;">
                <div class="col-md-6">
                    <% if $Top.ParentPage.IsQuestionOnFiltering($Up.ID) %>
                        $Label
                    <% else %>
                        <a href="$Top.ParentPage.Link($Top.ParentPage.Action)?qid=$Up.ID&vid=$ID$Top.ParentPage.SurveyBuilderDateFilterQueryString">$Label</a>
                    <% end_if %>
                </div>
                <div class="col-md-6">
                    $Top.ParentPage.SurveyBuilderCountNPSAnswers($Top.Question.ID, $ID)
                </div>
            </div>
        <% end_loop %>
        </div>
    </div>


</div>