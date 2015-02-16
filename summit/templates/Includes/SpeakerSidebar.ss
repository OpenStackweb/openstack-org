<div class="user-sidebar">
<% with $CurrentMember.CurrentSpeakerProfile %>
    <p class="user-img" <% if $Photo %>style="background-image: url($Photo.URL);"<% else %>style="background-image: url(/summit/images/generic-speaker-icon.png);"<% end_if %>></p>
    <h3 class="user-name">$Name</h3>

    <!-- Speaker Portal Navigation -->
    <ul class="user-menu">
        <li><a href="$Top.Link"<% if $Top.ActiveLink=="presentations" %> class="active"<% end_if %>>Presentations <i class="fa fa-chevron-right"></i></a></li>
        <li><a href="$Top.Link(bio)"<% if $Top.ActiveLink=="bio" %> class="active"<% end_if %>>My Speaker Bio <i class="fa fa-chevron-right"></i></a></li>
        <li><a href="Security/logout" class="">Logout <i class="fa fa-chevron-right"></i></a></li>
    </ul>
</div>
<% end_with %>