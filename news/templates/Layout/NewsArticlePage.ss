<div class="newsHome">
    <a href="/news">Back to News</a>
</div>

<div style="text-align:justify;">
    <h1>$Headline</h1>
    $ImageForArticle

    $HTMLBody
    <% if Document.exists %>
        <p class="document">Document: <a href="$Document.Link">$Document.getLinkedURL</a></p>
    <% end_if %>
    <p class="link"><a href="$Link">$Link</a></p>
    <p class="date">$Date</p>
    <p></p>
</div>
