<div id="summit-main-nav" class="navbar">
    <button type="button" aria-expanded="false" data-toggle="collapse"
            class="navbar-toggle collapsed" data-target="#header-navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>

    <div className="collapse navbar-collapse" id="header-navbar-collapse">
        <ul class="nav nav-tabs" >
            <li class="{$MainNavClass} <% if $ClassName == 'SummitAboutPage' %> current<% end_if %>">
                <a href="$SummitAboutLink">About The Summit</a>
            </li>
            <% loop $Menu(3) %>
                <li class="$LinkingMode">
                    <a href="$Link">$MenuTitle</a>
                </li>
            <% end_loop %>
        </ul>
    </div>
</div>
