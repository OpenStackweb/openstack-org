<div class="newsHome">
    <a href="/news<% if $IsArchivedNews %>/archived<% end_if %>">< Back to <% if $IsArchivedNews %>Archived<% end_if %> News</a>
</div>

<div style="text-align:justify;">
    <h1>$Article.Headline</h1>
    $Article.ImageForArticle

    $Article.HTMLBody
    <% if Document.exists %>
        <p class="document">Document: <a href="$Article.Document.Link">$Article.Document.getLinkedURL</a></p>
    <% end_if %>
    <p class="link"><a href="$Article.Link">$Article.Link</a></p>
    <p class="date">$Article.Date</p>
    <p></p>
</div>
