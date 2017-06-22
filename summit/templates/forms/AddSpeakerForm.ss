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
            <% if $Actions.dataFieldByName(action_doAddSpeaker).Exists() %>
                $Actions.dataFieldByName(action_doAddSpeaker)
            <% end_if %>
            <br><hr><br>
            <div class="col-md-4 col-xs-12 col-sm-4">
                $Actions.dataFieldByName(action_PrevStep)
            </div>
            <div class="col-md-4 col-xs-12 col-sm-4 middle">
                $Actions.dataFieldByName(action_save_later)
            </div>
            <div class="col-md-4 col-xs-12 col-sm-4 last">
                $Actions.dataFieldByName(action_doFinishSpeaker)
            </div>
        </div>
	<% end_if %>
<% if $IncludeFormTag %>
</form>
<% end_if %>
