
<link rel="stylesheet" type="text/css" href="/themes/openstack/css/staff.css" />

<h1>$Title</h1>

$Content

<% loop Children %>

<hr/>

<a name="{$ID}"></a>
<div class="row">
    <div class="col-sm-2 staff-photo-wrapper">
        <div class="photo">$Photo.SetWidth(100)</div>
            <a class="staff-openstack" href="/community/members/profile/{$ID}"></a>
            <% if TwitterName %>
            <a class="staff-twitter" target="_blank" href="https://twitter.com/{$TwitterName}"></a>
            <% end_if %>
            <% if LinkedInProfile %>
            <a class="staff-linkedin" href="http://linkedin.com/in/{$LinkedInProfile}"></a>
            <% end_if %>
    </div>
    <div class="col-sm-10 staff-text-wrapper">
        <h3>$FirstName $LastName</h3>
        
        <h5>Company</h5>
        <div>$JobTitle &nbsp; - $Company &nbsp;</div>
        <h5>Bio</h5>
        <div>
            <p dir="ltr"><span>$Bio &nbsp;</span></p>
        </div>
    </div>
</div>


<% end_loop %>
