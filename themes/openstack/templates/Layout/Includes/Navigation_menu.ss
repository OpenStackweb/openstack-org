<li>
    <a href="{$AbsoluteLink}software/" class="drop" id="dropdownMenuSoftware">Software <i class="fa fa-caret-down"></i></a><i class="mobile-expand"></i>
    <ul class="dropdown-menu dropdown-hover" role="menu" aria-labelledby="dropdownMenuSoftware">
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}software/">Overview</a></li>
        <li role="presentation" class="divider"></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}software/openstack-compute/">Compute</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}software/openstack-storage/">Storage</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}software/openstack-networking/">Networking</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}software/openstack-dashboard/">Dashboard</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}software/openstack-shared-services/">Shared Services</a></li>
        <li role="presentation" class="divider"></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="/software/start/">Get Started</a></li>
    </ul>
</li>
<li>
    <a href="{$AbsoluteLink}user-stories/" class="drop" id="dropdownMenuUsers">Users <i class="fa fa-caret-down"></i></a><i class="mobile-expand"></i>
    <ul class="dropdown-menu dropdown-hover" role="menu" aria-labelledby="dropdownMenuUsers">
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}user-stories/">Overview</a></li>
        <li role="presentation" class="divider"></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}enterprise/">OpenStack in the Enterprise</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="//superuser.openstack.org/">Superuser Magazine</a></li>
    </ul>
</li>
<li>
    <a href="{$AbsoluteLink}community/" class="drop" id="dropdownMenuCommunity">Community <i class="fa fa-caret-down"></i></a><i class="mobile-expand"></i>
    <ul class="dropdown-menu dropdown-hover" role="menu" aria-labelledby="dropdownMenuCommunity">
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}community/">Welcome! Start Here</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="//ask.openstack.org/">Ask A Technical Question</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="//wiki.openstack.org/wiki/Main_Page">OpenStack Wiki</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}community/events/">Community Events</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}foundation/">Openstack Foundation</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="//wiki.openstack.org/wiki/Getting_The_Code">Source Code</a></li>
        <li role="presentation" class="divider"></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}foundation/companies/">Supporting Companies</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}community/jobs/">Jobs</a></li>
        <li role="presentation" class="divider"></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}join/">Join The Community</a></li>
    </ul>
</li>
<li>
    <a href="{$AbsoluteLink}marketplace/">Marketplace</a>
</li>
<li>
    <a href="{$AbsoluteLink}events/" class="drop" id="dropdownMenuEvents">Events <i class="fa fa-caret-down"></i></a><i class="mobile-expand"></i>
    <ul class="dropdown-menu dropdown-hover" role="menu" aria-labelledby="dropdownMenuEvents">
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}community/events/">Overview</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}summit/">The OpenStack Summit</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}community/events/">More OpenStack Events</a></li>
    </ul>
</li>
<li>
    <a href="/blog/">Blog</a>
</li>
<li>
    <a href="http://docs.openstack.org/">Docs</a>
</li>
<li>
<% if WidgetCall %>
<li class="join-nav-section">
    <a href="#" id="dropdownMenuJoin">Join <i class="fa fa-caret-down"></i></a>
    <ul class="dropdown-menu dropdown-hover" role="menu" aria-labelledby="dropdownMenuJoin" style="display: none;">
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}join/register/?membership-type=foundation">Sign up for Foundation Membership</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}join/#sponsor">Sponsor the Foundation</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="{$AbsoluteLink}foundation">More about the Foundation</a></li>
    </ul>
</li>
<% end_if %>
</li>