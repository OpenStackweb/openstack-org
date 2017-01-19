<% loop $ArchivedNews %>
    <div class="recentBox" id="article_$ID">
        <div class="recentHeadline">
            <a href="news/view/$ID/$HeadlineForUrl?ar=1">$RAW_val(Headline)</a>
            <span class="itemTimeStamp">$getDateEmbargoCentral('M d, g:i a')</span>
            <% if $DateExpire %>
                <span class="itemTimeStamp">$getDateExpireCentral('M d, g:i a')</span>
            <% end_if %>
            <span class="newsRestore" article_id="$ID">Restore</span>
            <% if $Top.CurrentMember.isAdmin() %>
                <span class="newsDelete" article_id="$ID">Delete</span>
            <% end_if %>
        </div>
        <div class="recentSummary">$HTMLSummary</div>
    </div>
<% end_loop %>