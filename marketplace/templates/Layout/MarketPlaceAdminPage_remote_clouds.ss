<% if CurrentMember.isMarketPlaceAdmin %>
<% if canAdmin(remote_clouds) %>
    $setCurrentTab(5)
    <% include MarketPlaceAdminPage_CreateBox %>
    <% include MarketPlaceAdminPage_NavBar %>
    <% include MarketPlaceAdminPage_remote_clouds_list %>
    <script type="text/javascript">
        var listing_url = "$Top.Link(remote_clouds)";
    </script>
<% else %>
    <p>You are not allowed to administer Remote Clouds.</p>
<% end_if %>
<% else %>
    <p>You are not allowed to administer MarketPlace.</p>
<% end_if %>