</div>

<div class="grey-bar news">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <a href="/news<% if $IsArchivedNews %>/archived<% end_if %>">< Back to <% if $IsArchivedNews %>Archived<% end_if %> News</a>
            </div>
            <div class="col-sm-6">
                <div class="facebook share_icon" onclick="shareFacebook('{$ArticleUrl}')">
                    <span class="fa-stack fa-lg">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
                    </span>
                </div>
                <div class="twitter share_icon" onclick="shareTwitter('{$ArticleUrl}')">
                    <span class="fa-stack fa-lg">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <% if $Article.ShowDeclaimer %>
    <div class="row">
        <div class="col-lg-8 col-lg-push-2 col-md-10 col-md-push-1 col-sm-10 col-sm-push-1 declaimer">
            <i>This content has been submitted by a company in the OpenStack ecosystem, not the Open Infrastructure Foundation.</i>
        </div>
    </div>
    <% end_if %>
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
