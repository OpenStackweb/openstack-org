<ul id="$ID" class="$extraClass" style="padding:0;">
	<% if $Options.Count %>
		<% loop $Options %>
			<li class="$Class checkbox">
				<input id="$ID" class="checkbox" name="$Name" type="checkbox" value="$Value"<% if $isChecked %> checked="checked"<% end_if %><% if $isDisabled %> disabled="disabled"<% end_if %> />
				<label for="$ID">$Title</label>
			</li> 
		<% end_loop %>
	<% else %>
		<li>No options available</li>
	<% end_if %>
</ul>
