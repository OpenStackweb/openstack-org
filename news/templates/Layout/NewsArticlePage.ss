</div>

<div class="grey-bar news">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <a href="/news<% if $IsArchivedNews %>/archived<% end_if %>">< Back to <% if $IsArchivedNews %>Archived<% end_if %> News</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-push-2 col-md-10 col-md-push-1 col-sm-10 col-sm-push-1">
            <div style="text-align:left;">
                <h1 class="article-h1">$Article.Headline</h1>
                $Article.ImageForArticle

                $Article.HTMLBody
                <% if Document.exists %>
                    <p class="document">Document: <a href="$Article.Document.Link">$Article.Document.getLinkedURL</a></p>
                <% end_if %>
                <p class="link"><a href="$Article.Link">$Article.Link</a></p>
                <p class="date news-article">$Article.Date</p>
                <p></p>
            </div>
        </div>
    </div>
</div> 
