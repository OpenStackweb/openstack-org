<nav class="navbar navbar-default" role="navigation">

    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="brand-wrapper">
                <a class="navbar-brand" href="/"></a>
            </div>
            <div class="search-icon show"><i class="fa fa-search"></i> <span class="header-search-text">Search</span></div>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <div class="search-container tiny">
                <div class="openstack-search-bar" data-baseUrl="{$Top.GetEnv('SEARCH_WIDGET_BASE_URL')}" data-context="www-openstack"></div>
           </div>
           <ul class="nav navbar-nav navbar-main show">
            <li>
                <div class="openstack-search-bar ossw-mobile" data-baseUrl="{$Top.GetEnv('SEARCH_WIDGET_BASE_URL')}" data-context="www-openstack"></div>
            </li>

            <% include Navigation_menu %>

            <li>
            <% if CurrentMember %>
                <a href="/profile/" class="drop" id="dropdownMenuEvents">My Account <i class="fa fa-caret-down"></i></a><i class="mobile-expand"></i>
                <ul class="dropdown-menu dropdown-hover" role="menu" aria-labelledby="dropdownMenuEvents">

                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/profile/">Edit Profile</a></li>

                    <li role="presentation">
                        <a role="menuitem" tabindex="-1" href="/community/members/profile/{$CurrentMember.ID}/{$CurrentMember.NameSlug}">
                            View Public Profile
                        </a>
                    </li>

                    <li role="presentation" class="divider"></li>

                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/Security/logout/?BackURL={$Top.Link}">Log Out</a></li>


                </ul>
            <% else %>
                <li class="join-nav-section">
                    <a href="#" id="dropdownMenuJoin">Join <i class="fa fa-caret-down"></i></a>
                    <ul class="dropdown-menu dropdown-hover" role="menu" aria-labelledby="dropdownMenuJoin" style="display: none;">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/join/register/signup ">Sign up for Foundation Membership</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/join/#sponsor">Sponsor the Foundation</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/foundation">More about the Foundation</a></li>
                    </ul>
                </li>
                <li>
                    <a href="/Security/login/?BackURL={$Top.Link}" class="sign-in-btn">Log In</a>
                </li>
            <% end_if %>
            </li>
        </ul>
    </div>
    <!-- /.navbar-collapse -->
</div>
<!-- /.container -->
</nav>
