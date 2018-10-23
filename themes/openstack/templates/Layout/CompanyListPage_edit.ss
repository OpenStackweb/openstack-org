<h1>Edit Your Company's OpenStack Profile</h1>
<% if isCompanyAdmin %>
    $CompanyEditForm
    $EditorToolbar
<% else %>
    <p>You must be logged in as someone with permission to edit this company.</p>
<% end_if %>
