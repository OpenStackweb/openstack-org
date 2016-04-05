<h1>The OpenStack Foundation</h1>
$Content
<% if OpenStackFoundationStaffMembers %>
    <h2 class="span-24 last">Open Stack Foundation Staff</h2>
    <% loop OpenStackFoundationStaffMembers %>
          <% include OpenStackFoundationStaffPage_Member %>
    <% end_loop %>
<% end_if %>

<% if SupportingCastMembers %>
<h2 class="span-24 last">Supporting Cast</h2>
    <% loop SupportingCastMembers %>
        <% include OpenStackFoundationStaffPage_CastMember %>
    <% end_loop %>
<% end_if %>
<hr>
<div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 staff-photo-wrapper">
        <div class="photo"><img title="" src="themes/openstack/images/foundation-staff/FNtech.jpg" alt="FNtech"></div>
            <p><a class="staff-twitter" href="https://twitter.com/FNTECH" target="_blank">&nbsp;</a></p>
        </div>
        <div class="col-lg-10 col-md-10 col-sm-10 staff-text-wrapper">
            <h3>FNtech</h3>
            <div>&nbsp;</div>
            <h5>Bio</h5>
            <div class="span-18 last" style="width: 600px;">
            <p>
                FNtech is the Foundation's events production partner working behind the scenes to produce our global OpenStack Summits.
                Based in Los Angeles and San Francisco, FNtech specializes in sponsor fulfillment, audio/video, lighting, staging and
                full-service production for corporate and live events.
            </p>
        </div>
    </div>
</div>

<div class="light">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <h3>Contact Information</h3>
            <p>OpenStack Foundation<br> P.O. Box 1903<br> Austin, TX 78767<br> 512-827-8633<br><a href="mailto:info@openstack.org">Contact Us</a></p>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <h3>More About The Foundation</h3>
            <a href="foundation/board-of-directors">Board of Directors</a><br>
            <a href="foundation/technical-committee">Technical Committee</a><br>
            <a href="foundation/user-committee/">User Committee</a><br>
            <a href="foundation/companies">Supporting Companies</a></div>
        </div>
    </div>
</div>