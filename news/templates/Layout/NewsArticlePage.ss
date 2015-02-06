<div class="newsHome">
    <a href="/news">Back to News</a>
</div>

<div>
    <h1>$Headline</h1>
    $Image.CroppedImage(300,200)
    <!--<p class="summary">$RAW_val(Summary)</p>-->
    <div class="body">$HTMLBody</div>
    <% if Document.exists %>
        <p class="document">Document: <a href="$Document.Link">$Document.getLinkedURL</a></p>
    <% end_if %>
    <p class="link"><a href="$Link">$Link</a></p>
    <p class="date">$Date</p>
    <p></p>
</div>
