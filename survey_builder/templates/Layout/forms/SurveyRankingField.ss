<div id="$ID" class="ranking_container">
    <p>Please rank up to $Question.MaxItemsToRank</p>
    $Question.Intro
    <p><strong>Click your options to rank them. Select at least one.</strong>&nbsp;&nbsp;<a title="clear all options." href="#" class="clear_all_ranking_options">clear all</a></p>
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
                 <p>No options available</p>
            <% end_if %>
        </tbody>
     </table>
    <input type="hidden" name="$Name" id="$Name" value="" class="ctrl_hidden_value"/>
</div>
<script type="application/javascript">
    jQuery(document).ready(function($) {

        $('#'+'{$ID}').survey_ranking_field({ max_rank_items : $Question.MaxItemsToRank, rank_order : $AnswerCount });
    });
</script>