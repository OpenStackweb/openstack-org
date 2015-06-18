<div class="container news-container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h2>OpenStack Community</h2>
            <% loop RecentNews %>
                <div class="recentBox">
                    <div class="recentHeadline">
                        <a href="news/view/$ID/$HeadlineForUrl">$RAW_val(Headline)</a> <span class="itemTimeStamp">$formatDate</span>
                    </div>
                    <div class="recentSummary">$HTMLSummary</div>
                </div>

            <% end_loop %>
        </div>
    </div>
</div>