<h1>The Open Infrastructure Foundation</h1>
$Content
<% if OpenStackFoundationStaffMembers %>
    <h2 class="span-24 last">Open Infrastructure Foundation Staff</h2>
    <% loop OpenStackFoundationStaffMembers %>
          <% include OpenStackFoundationStaffPage_Member %>
    <% end_loop %>
<% end_if %>

$ExtraFoundation

<% if SupportingCastMembers %>
<h2 class="span-24 last">Supporting Cast</h2>
    <% loop SupportingCastMembers %>
        <% include OpenStackFoundationStaffPage_CastMember %>
    <% end_loop %>
<% end_if %>

$ExtraSupporting

$ExtraFooter

