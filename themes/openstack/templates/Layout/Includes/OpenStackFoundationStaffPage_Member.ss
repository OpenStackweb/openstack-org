<hr>
<div class="row">
    <div class="col-sm-2 staff-photo-wrapper">
        <div class="photo">
            $ProfilePhoto
        </div>
        <% if $TwitterName %>
            <a class="staff-twitter" target="_blank" href="https://twitter.com/{$TwitterName}">
                <i class="fa fa-twitter" aria-hidden="true"></i>
            </a>
        <% end_if %>
        <% if $LinkedInProfile %>
            <a class="staff-linkedin" href="{$LinkedInProfile}">
                <i class="fa fa-linkedin-square" aria-hidden="true"></i>
            </a>
        <% end_if %>
        <% if $WeChatUser %>
            <a class="staff-wechat" href="{$WeChatUser}">
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
        <% if CurrentJobTitle %>
            <h5>Job Title</h5>
            <div>$CurrentJobTitle</div>
        <% end_if %>
        <% if Bio %>
            <h5>Bio</h5>
            <div>$Bio&nbsp;</div>
        <% end_if %>
    </div>
</div>
