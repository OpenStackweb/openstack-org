<% with Member %>
    <p>Dear $FullName, Thank your for verifying your email ($Email)!</p>
    <% if not CurrentMember %>
        <p>In order to edit your community profile, you will first need to <a href="/Security/login/?BackURL=%2Fprofile%2F">login as a member</a>
    <% end_if %>
<% end_with %>
