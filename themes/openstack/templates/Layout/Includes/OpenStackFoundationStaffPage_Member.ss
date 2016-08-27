<hr>
<div class="row">
    <div class="col-sm-2 staff-photo-wrapper">
        <div class="photo">
            $ProfilePhoto
        </div>
        <% if TwitterName %>
        <a class="staff-twitter" target="_blank" href="https://twitter.com/{$TwitterName}"></a>
        <% end_if %>
        <% if LinkedInProfile %>
        <a class="staff-linkedin" href="http://linkedin.com/in/{$LinkedInProfile}"></a>
        <% end_if %>
        <a class="staff-openstack" href="/community/members{$Link}{$ID}"></a>
    </div>
    <div class="col-sm-10 staff-text-wrapper">
        <h3>$FullName</h3>
        <% if CurrentJobTitle %>
            <h5>Job Title</h5>
            <div>$CurrentJobTitle&nbsp;</div>
        <% end_if %>
        <% if Bio %>
            <h5>Bio</h5>
            <div>$Bio&nbsp;</div>
        <% end_if %>
    </div>
</div>
