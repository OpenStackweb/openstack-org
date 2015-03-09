<form id="MemberLoginForm_LoginForm" action="Security/login?BackURL={$Link}" method="post" enctype="application/x-www-form-urlencoded">
     <div class="clear"><!-- --></div>
    $Fields.dataFieldByName(SecurityID)
    <div class="Actions">
        <input class="action " id="MemberLoginForm_LoginForm_action_dologin" type="submit" name="action_dologin"
               value="Log in" title="Log in" style="margin-bottom: 10px;"/>
        <p id="ForgotPassword"><a href="Security/lostpassword">I've lost my password</a> | <a href="/join/register">Register
            now</a></p>
    </div>
</form>