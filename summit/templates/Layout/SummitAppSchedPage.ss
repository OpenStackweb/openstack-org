<div class="container" id="schedule-page-wrapper">
    <div class="row schedule-title-wrapper">
        <div class="col-sm-6 col-main-title">
            <h1 style="text-align:left;">Schedule</h1>
        </div>
        <div class="col-sm-6">
            <div id="os-schedule-global-search" class="os-schedule-global-search" data-search-url="{$Top.Link(global-search)}" data-schedule-url="" data-search-value=""></div>
        </div>
    </div>
    <% if CurrentMember %>
        <div class="row">
            <div class="col-xs-12 logout-container">
                <a class="action btn btn-default" id="login-button" href="/Security/logout/?BackURL={$Top.Link}"><i class="fa fa-sign-out" aria-hidden="true"></i>Log Out</a>
            </div>
         </div>
    <% else %>
        <div class="row">
            <div class="col-xs-12 login-container">
                <form id="MemberLoginForm_LoginForm" action="Security/login?BackURL={$Top.Link}" method="post" enctype="application/x-www-form-urlencoded">
                    <input type="hidden" name="fragment" id="fragment"/>
                    <div class="Actions">
                        <button class="action btn btn-primary" type="submit" id="login-button" name="action_dologin" title="Log in to create your own Schedule and Watch List">
                            <i class="fa fa-user"></i>
                            Log in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <% end_if %>

    <div id="os-schedule-react"></div>
</div>

$ModuleJS('schedule')
$ModuleCSS('schedule')
