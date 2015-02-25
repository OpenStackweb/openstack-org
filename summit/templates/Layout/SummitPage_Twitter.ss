<% if Twitter_Data %>
    <% with Twitter_Data %>
        <script src="//platform.twitter.com/oct.js" type="text/javascript"></script>
        <script type="text/javascript">
            twttr.conversion.trackPid('{$TwitterPixelId}');</script>
        <noscript>
            <img height="1" width="1" style="display:none;" alt="" src="https://analytics.twitter.com/i/adsct?txn_id={$TwitterPixelId}&p_id=Twitter" />
            <img height="1" width="1" style="display:none;" alt="" src="//t.co/i/adsct?txn_id={$TwitterPixelId}&p_id=Twitter" />
        </noscript>
    <% end_with %>
<% end_if %>