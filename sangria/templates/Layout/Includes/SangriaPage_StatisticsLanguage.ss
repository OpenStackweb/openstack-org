<div class="col-md-4" style="height:450px;">
    <h3>Language</h3>
    <p>N =  $Top.ParentPage.SurveyBuilderCountLang(0)</p>
    <table class="table">
        <tbody style="display: block; height: 325px; overflow-y: auto">
            <% loop $Top.ParentPage.getLanguageValues %>
            <tr>
                <td>
                    <% if $Top.ParentPage.IsQuestionOnFiltering(lang) %>
                        <span>$Lang</span>
                    <% else %>
                        <a href="$Top.ParentPage.Link($Top.ParentPage.Action)?qid=lang&vid={$Lang}{$Top.ParentPage.SurveyBuilderDateFilterQueryString}">$Lang</a>
                    <% end_if %>
                </td>
                <td>$Top.ParentPage.SurveyBuilderCountLang($Lang)</td>
            </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>