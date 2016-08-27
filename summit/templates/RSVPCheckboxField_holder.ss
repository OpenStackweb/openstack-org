<div id="$Name" class="checkbox $HolderClasses" $HolderAttributes>
    <input $AttributesHTML class="$inputClasses">
    <label class="$labelClasses">
        $Title
    </label>
    <% if $HelpText %>
    <p class="help-block">$HelpText</p>
    <% end_if %>
    <% if $InlineHelpText %>
    <span class="help-inline">$InlineHelpText</span>
    <% end_if %>
</div>