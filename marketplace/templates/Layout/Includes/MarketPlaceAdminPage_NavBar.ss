<div class="nav-bar-container">
    <h2 class="profile-tabs">
        <% if canAdmin(implementations) %>
            <a href="{$Link}" <% if CurrentTab=1 %>class="active"<% end_if %> >Distributions/Appliances</a>
        <% end_if %>
        <% if canAdmin(public_clouds) %>
            <a href="{$Link}public_clouds" <% if CurrentTab=2 %>class="active"<% end_if %>>Public Clouds</a>
        <% end_if %>
        <% if canAdmin(private_clouds) %>
            <a href="{$Link}private_clouds" <% if CurrentTab=4 %>class="active"<% end_if %>>Private Clouds</a>
        <% end_if %>
        <% if canAdmin(remote_clouds) %>
            <a href="{$Link}remote_clouds" <% if CurrentTab=5 %>class="active"<% end_if %>>Remotely Managed Private Clouds</a>
        <% end_if %>
        <% if canAdmin(consultants) %>
            <a href="{$Link}consultants"  <% if CurrentTab=3 %>class="active"<% end_if %> >Infra Solutions & Consulting</a>
        <% end_if %>
        <% if canAdmin(books) %>
            <a href="{$Link}books"  <% if CurrentTab=6 %>class="active"<% end_if %> >Books</a>
        <% end_if %>
    </h2>
</div>