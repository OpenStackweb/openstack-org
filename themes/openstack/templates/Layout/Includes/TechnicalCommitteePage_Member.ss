<hr/>
<div class="row">
    <div class="col-sm-2 staff-photo-wrapper">
        <div class="photo">$ProfilePhoto</div>
            <% if TwitterName %>
            <a class="staff-twitter" target="_blank" href="https://twitter.com/{$TwitterName}"></a>
            <% end_if %>
            <% if LinkedInProfile %>
            <a class="staff-linkedin" href="http://linkedin.com/in/{$LinkedInProfile}"></a>
            <% end_if %>
            <a class="staff-openstack" href="/community/members{$Link}{$ID}"></a>
    </div>
    <div class="col-sm-10 staff-text-wrapper">
        <h3>$FirstName $Surname</h3>
        <% if Role %>
            <h4 class="role">$Role</h4>
        <% end_if %>
        <h5>Company</h5>
        <div>$CurrentCompanies&nbsp;</div>
        <h5>Bio</h5>
        <div>
            $Bio
        </div>
    </div>
</div>
