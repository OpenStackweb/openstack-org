<% if $IncludeFormTag %>
<form $AttributesHTML role="form">
<% end_if %>
	<% if $Message %>
		<% if $MessageType == "good" %>
                <div id="{$FormName}_error" class="alert alert-success" role="alert">$Message</div>
		<% else_if MessageType == "info" %>
                <div id="{$FormName}_error" class="alert alert-info" role="alert">$Message</div>
		<% else_if MessageType == "bad" %>
                <div id="{$FormName}_error" class="alert alert-danger" role="alert">$Message</div>
		<% end_if %>
	<% end_if %>
	
	<fieldset>
		<% if $Legend %><legend>$Legend</legend><% end_if %> 
		<% loop $Fields %>
			$FieldHolder
		<% end_loop %>
		<div class="clear"><!-- --></div>
	</fieldset>

	<% if $Actions %>
	<div class="form-actions row">
		<% if Actions.Count == 2 %>
            <div class="col-md-4 col-xs-12 col-sm-4">
                &nbsp;
            </div>
        <% end_if %>
        <% loop $Actions %>
            <div class="col-md-4 col-xs-12 col-sm-4 <% if Last %>last<% end_if %><% if Mid %>middle<% end_if %>">
                $Field
            </div>
        <% end_loop %>
	</div>
	<% end_if %>
<% if $IncludeFormTag %>
</form>
<% end_if %>
