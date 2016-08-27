<div class="user-sidebar">
<% with $CurrentMember.SpeakerProfile %>
    <p class="user-img" style="background-image: url($ProfilePhoto);"></p>
    <h3 class="user-name">$Name</h3>

    <!-- Speaker Portal Navigation -->
    <ul class="user-menu">
        <li><a href="$Top.Link"<% if $Top.ActiveLink=="presentations" %> class="active"<% end_if %>>Presentations <i class="fa fa-chevron-right"></i></a></li>
        <li><a href="$Top.Link(bio)"<% if $Top.ActiveLink=="bio" %> class="active"<% end_if %>>My Speaker Bio <i class="fa fa-chevron-right"></i></a></li>
        <li><a href="/summit/barcelona-2016/categories/">Summit Categories &amp; Tracks <i class="fa fa-chevron-right"></i></a></li>
        <li><a href="$Top.Link(selection-process)">Speaker Selection Process <i class="fa fa-chevron-right"></i></a></li>
        <li><a href="/Security/logout?BackURL={$Top.Link}" class="">Logout <i class="fa fa-chevron-right"></i></a></li>
    </ul>
</div>
<% end_with %>
