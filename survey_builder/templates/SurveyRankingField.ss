<div id="$ID" class="ranking_container">
    <p>$_T("survey_ui","Please rank up to %s", $Question.MaxItemsToRank)</p>
    $_T("survey_template", $Question.Intro)
    <p><strong>$_T("survey_ui", "Click your options to rank them. Select at least one.")</strong>&nbsp;&nbsp;<a title="clear all options." href="#" class="clear_all_ranking_options">$_T("survey_ui","clear all")</a></p>
    <table class="ranking-table">
        <tbody>
            <% if $Options.Count %>
                <% loop $Options %>
                    <tr>
                        <td id="{$Top.ID}_{$ID}" class="rank-wrapper<% if $Top.AnswerIndex($ID) %> selected-rank<% end_if %>"<% if $Top.AnswerIndex($ID) %> data-sort="{$Top.AnswerIndex($ID)}"<% end_if %> data-answer="{$ID}"><% if $Top.AnswerIndex($ID) %>$Top.AnswerIndex($ID)<% end_if %></td>
                        <td class="rank-text">$Title</td>
                    </tr>
                    <tr class="spacer"></tr>
                <% end_loop %>
            <% else %>
                 <p>$_T("survey_ui","No options available")</p>
            <% end_if %>
        </tbody>
     </table>
    <input type="hidden" name="$Question.Name" id="$Question.Name" value="" class="ctrl_hidden_value" $ValidationAttributesHTML />
</div>
<script type="application/javascript">
    jQuery(document).ready(function($) {
        $('#'+'{$ID}').survey_ranking_field({ max_rank_items : $Question.MaxItemsToRank, rank_order : $AnswerCount });
    });
</script>