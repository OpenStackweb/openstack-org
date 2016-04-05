<h1>$Title</h1>
$Content
<% if UserCommitteeMembers %>
        <% loop UserCommitteeMembers %>
            <% include BoardOfDirectorsPage_Member %>
        <% end_loop %>
<% end_if %>
