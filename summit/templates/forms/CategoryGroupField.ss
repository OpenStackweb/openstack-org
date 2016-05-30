<select $AttributesHTML>
<option value="" selected disabled>-- Select one --</option>
<% loop $Options %>
	<option class="$GroupType" value="$Value.XML"<% if $Selected %> selected="selected"<% end_if %><% if $Disabled %> disabled="disabled"<% end_if %>>$Title.XML</option>
<% end_loop %>
</select>