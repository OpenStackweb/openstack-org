<% if $IsWelcome %>
<div class="presentation-app-header success">
    <div class="container">
        <p class="status">Welcome to OpenStack!</p>
    </div>
</div>
<% end_if %>            
<div class="presentation-app-header">
	<div class="container">
		<p class="status"><i class="fa fa-calendar"></i>&nbsp;Currently accepting presentation submissions until <strong>January 3rd, 2015</strong>.</p>
	</div>
</div>
<div class="presentation-app-body">
	<div class="container">
            <% if $URLSegment == 'Security' %>
                <div class="presentation-app-login-panel">
                    $Form
                </div>
            <% end_if %>
    </div>
</div>
