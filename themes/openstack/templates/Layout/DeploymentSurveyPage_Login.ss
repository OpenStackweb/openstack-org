<div class="container">
	<h1>OpenStack User Survey: Welcome!</h1>
	
	<div class="row">
	
		<div class="col-lg-4">
			<div id="user">
				<p>This is the <strong>OpenStack User Survey</strong> for OpenStack cloud users and operators.</p>
			</div>
		</div>
	
		<div class="col-lg-4">
			<div id="time">
				<p>It should only take <strong>10 minutes</strong> to complete.</p>
			</div>
		</div>
	
		<div class="survey-box col-lg-4">
			<div id="private">
				<p>All of the information you provide is <strong>confidential</strong> to the Foundation (unless you specify otherwise).</p>
			</div>
		</div>
	
	</div>
	
	
		<p>This survey provides users an opportunity to influence the community and software
		direction. By sharing information about your configuration and requirements, the OpenStack
		Foundation User Committee will be able to advocate on your behalf.</p>
		<p><a href="{$Link}faq" class="roundedButton">More Information About The Survey</a></p>
		<hr/>
		
		<h1>Get Started</h1>
	
	
	<div class="row">
		<div class="col-lg-6">
		
		<h3>Already have an OpenStack Foundation login?</h3>
		
		<form  id="MemberLoginForm_LoginForm" action="Security/LoginForm" method="post" enctype="application/x-www-form-urlencoded">
		
		
			<p id="MemberLoginForm_LoginForm_error" class="message " style="display: none"></p>
		
		
			<fieldset>
		
					<input class="hidden" type="hidden" id="MemberLoginForm_LoginForm_AuthenticationMethod" name="AuthenticationMethod" value="MemberAuthenticator" />
		
					<div id="Email" class="field text "><label class="left" for="MemberLoginForm_LoginForm_Email">Email</label><div class="middleColumn"><input type="text" class="text" id="MemberLoginForm_LoginForm_Email" name="Email" value="" /></div></div>
		
					<div id="Password" class="field password "><label class="left" for="MemberLoginForm_LoginForm_Password">Password</label><div class="middleColumn"><input class="text" type="password" id="MemberLoginForm_LoginForm_Password" name="Password" value=""   /></div></div>
		
					<input class="hidden" type="hidden" id="MemberLoginForm_LoginForm_BackURL" name="BackURL" value="{$Link}OrgInfo" />
		
				<div class="clear"><!-- --></div>
		
					<input class="hidden" type="hidden" id="MemberLoginForm_LoginForm_SecurityID" name="SecurityID" value="$SecurityID" />
		
			</fieldset>
		
		
			<div class="Actions">
		
					<input class="action " id="MemberLoginForm_LoginForm_action_dologin" type="submit" name="action_dologin" value="Log in" title="Log in" />
		
					<p id="ForgotPassword"><a href="Security/lostpassword">I've lost my password</a></p>
		
			</div>
		
		
		</form>
		
		</div>
		
		<div class="col-lg-6">
		<h3>Don't have a login? Start here.</h3>
		$RegisterForm
		</div>
	</div>
</div>