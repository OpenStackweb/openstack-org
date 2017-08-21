<% if CurrentMember.isMarketPlaceAdmin %>
<% if canAdmin(books) %>
    $setCurrentTab(6)
    <% include MarketPlaceAdminPage_CreateBox %>
    <% include MarketPlaceAdminPage_NavBar %>
    <% include MarketPlaceAdminPage_books_list %>
    <script type="text/javascript">
        var listing_url = "$Top.Link(books)";
    </script>
<% else %>
    <p>You are not allowed to administer Books.</p>
<% end_if %>
<% else %>
<p>You are not allowed to administer MarketPlace.</p>
<% end_if %>
