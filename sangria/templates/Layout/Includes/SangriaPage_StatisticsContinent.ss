<div class="col-md-4" style="height:450px;">
    <h3>Continent</h3>
    <p>N =  $Top.ParentPage.SurveyBuilderCountContinent(0)</p>
    <table class="table">
        <tbody style="display: block; height: 325px; overflow-y: auto">
            <% loop $Top.ParentPage.getContinentValues %>
            <tr>
                <td>
                    <% if $Top.ParentPage.IsQuestionOnFiltering(continent) %>
                        <span>$Name</span>
                    <% else %>
                        <a href="$Top.ParentPage.Link($Top.ParentPage.Action)?qid=continent&vid={$Name}{$Top.ParentPage.SurveyBuilderDateFilterQueryString}">$Name</a>
                    <% end_if %>
                </td>
                <td>$Top.ParentPage.SurveyBuilderCountContinent($Name)</td>
            </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>