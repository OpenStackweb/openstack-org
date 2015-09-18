<div class="container">
    <h1>How the OpenStack Foundation can help</h1>
    <hr>
    <div class="row">
        <div class="main-content col-sm-8">
            <% if LatestSectionLinks %>
                <div class="promoted-content">
                    <% include Marketing_SectionLinks %>
                </div>
            <% end_if %>
            <% if LatestGraphics %>
                <div class="graphics">
                    <% include Marketing_Graphics %>
                </div>
            <% end_if %>
            <% if LatestPresentations %>
                <div class="collateral">
                    <% include Marketing_Collateral_Presentations %>
                </div>
            <% end_if %>
            <% if LatestYouTubeVideos %>
                <div class="videos">
                    <% include Marketing_YouTubeVideos %>
                </div>
            <% end_if %>
            <% if LatestEventsMaterial %>
                <div class="collateral">
                    <% include Marketing_Events_Materials %>
                </div>
            <% end_if %>
        </div>
        <div class="secondary-content col-sm-4">
            <% if LatestAnnouncements %>
                <div class="announcements">
                    <% include Marketing_Announcements %>
                </div>
            <% end_if %>
            <%--
            <% if Feeds %>
                <div class="feed">
                    <% include Marketing_Feeds %>
                </div>
            <% end_if %>
            --%>
            <% if LatestCases %>
                <div class="case-studies">
                    <% include Marketing_Cases %>
                </div>
            <% end_if %>
        </div>
    </div>
</div>