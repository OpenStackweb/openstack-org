<h2>Videos</h2>
<hr>
<div class="row">
    <% loop LatestYouTubeVideos %>
        <% if First %>
            <div class="col-sm-12">
        <% end_if %>
        <div class="item col-sm-6<% if MultipleOf(2)  %> last<% end_if %>">
            <div class="video">
                <a href="$Url" rel="shadowbox">
                    $Preview
                    <span class="caption">Get this Video</span>
                </a>
            </div>
        </div>
        <% if MultipleOf(2)  %>
            </div>
            <div class="col-sm-12">
        <% end_if %>
        <% if Last %>
            </div>
        <% end_if %>
    <% end_loop %>
</div>
