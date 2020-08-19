<hr>
<div class="row">
    <div class="col-sm-2 staff-photo-wrapper">
        <div class="photo">
            $ProfilePhoto
        </div>
        <% if $TwitterName %>
            <a class="staff-twitter" target="_blank" href="https://twitter.com/{$TwitterName}">
                <i class="fab fa-twitter" aria-hidden="true"></i>
            </a>
        <% end_if %>
        <% if $LinkedInProfile %>
            <a class="staff-linkedin" href="{$LinkedInProfile}">
                <i class="fab fa-linkedin" aria-hidden="true"></i>
            </a>
        <% end_if %>
        <% if $WeChatUser %>
            <a class="staff-wechat" href="javascript:copyWeChatID('{$WeChatUser}')">
                <i class="fa fa-weixin" aria-hidden="true"></i>
            </a>
        <% end_if %>
        <% if $ContactEmail %>
            <a class="staff-contact-email" rel="nofollow" href="mailto:{$ContactEmail}">
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </a>
        <% end_if %>
        <a class="staff-openstack" href="/community/members{$Link}{$ID}"></a>
    </div>
    <div class="col-sm-10 staff-text-wrapper">
        <h3>$FullName</h3>
        <% if TypeOfDirector %>
            <h5>Type of Director</h5>
            <div>$TypeOfDirector&nbsp;</div>
        <% end_if %>
        <% if $CurrentCompanies %>
            <h4>
                <% if $Role %> $Role at <% end_if %>
                $CurrentCompanies
            </h4>
        <% end_if %>
        <% if Bio %>
            <h5>Bio</h5>
            <div>$Bio&nbsp;</div>
        <% end_if %>
    </div>
</div>
