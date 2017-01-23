<form $AttributesHTML>
    <fieldset>
        <% loop $Fields %>
            $FieldHolder
        <% end_loop %>
        <div class="clear"><!-- --></div>
    </fieldset>
    <% if $Actions %>
        <div class="Actions row">
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
</form>
