<% require themedCSS(conference) %>
<% require javascript(themes/openstack/javascript/tag-it.js) %>
$Content
<div class="membership-level-container">
    <button type="button" class="btn btn-primary foundation-member active">Foundation Member</button>
    <button type="button" class="btn btn-primary community-member">Community Member</button>
</div>
<h2 id="terms-title">1. Read over the terms of becoming an OpenStack Foundation Individual Member.</h2>
<div class="termsBox">
    <% loop LegalTerms %>
        $Content
    <% end_loop %>
</div>
<p style="margin-top:40px;"></p>
<h2 id="member-application-title">2. Complete The Individual Member Application.</h2>
<p id="foundation-disclaimer">By completing the application and creating an account, you agree to the terms of the Individual Member Agreement above.</p>
$RegistrationForm
<div id="affiliation-edition-dialog">
    $AffiliationEditForm
</div>

