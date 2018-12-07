<div class="col-md-4" style="height:450px;">
    <h3>Continent</h3>
    <p>N =  $Top.SurveyBuilderCountContinent(0)</p>
    <table class="table">
        <tbody style="display: block; height: 325px; overflow-y: auto">
            <% loop $Top.getContinentValues %>
            <tr>
                <td>
                    <% if $Top.IsQuestionOnFiltering(continent) %>
                        <span>$Name</span>
                    <% else %>
                        <a href="$Top.Link($Top.Action)?qid=continent&vid={$Name}{$Top.SurveyBuilderDateFilterQueryString}">$Name</a>
                    <% end_if %>
                </td>
                <td>$Top.SurveyBuilderCountContinent($Name)</td>
            </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>