<h1>Track Chairs</h1>
<% if $AvailableSummits.Count %>
<form id="form_current_summit" name="form_current_summit" action="{$Top.Link('summits')}">
<select id="current_summit" name="current_summit">
<option value="0">--SELECT A SUMMIT--</option>
<% loop $AvailableSummits %>
  <option value="{$ID}">{$Title} - {$OpenSelectionPlanForStage.Name}</option>
<% end_loop %>
</select>
</form>
<% else %>
<p>Not Available Summits. Selection Process is Closed</p>
<% end_if %>