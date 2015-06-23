<div class="newsHome">
    <a href="/news">< Back to News</a>
</div>

<div class="news-container">
    <span>$Query</span>

    <div class="row news-archived-search">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h2>Archived News</h2>
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                    <div class="input-group stylish-input-group">
                        <input type="text" class="form-control" placeholder="search archived news" >
                        <span class="input-group-addon">
                            <button type="submit">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="news-archived-articles" data-total-pages="$ArchivedNewsPages">
                <% if $ArchivedNews.Count() > 0 %>
                    <% include NewsArchivePage_Articles ArchivedNews=$ArchivedNews %>
                <% else %>
                    <div>
                        No articles found.
                    </div>
                <% end_if %>

            </div>
        </div>
    </div>

    <div class="text-center">
        <ul class="news-pager"></ul>
    </div>
</div>