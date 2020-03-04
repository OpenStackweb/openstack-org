<div class="container" id="schedule-page-wrapper">
    <div class="row">
        <div class="col-xs-12">
            <% if CurrentMember %>
                <a class="action btn btn-default" id="login-button" href="/Security/logout/?BackURL={$Top.Link}"><i class="fa fa-sign-out" aria-hidden="true"></i>Log Out</a>
            <% else %>
                <form id="MemberLoginForm_LoginForm" action="Security/login?BackURL={$Top.Link}" method="post" enctype="application/x-www-form-urlencoded">
                    <input type="hidden" name="fragment" id="fragment"/>
                    <div class="Actions">
                        <button class="action btn btn-primary" type="submit" id="login-button" name="action_dologin" title="Log in to create your own Schedule and Watch List">
                            <i class="fa fa-user"></i>
                            Log in
                        </button>
                    </div>
                </form>
            <% end_if %>
        </div>
    </div>

    <summit-schedule
        summit_id="$Summit.ID"
        api_access_token="$getAccessToken()"
        api_url="$getApiUrl()"
        schedule_base="$Top.Link"
        schedule_url="$Top.AbsoluteLink"
    ></summit-schedule>

</div>

