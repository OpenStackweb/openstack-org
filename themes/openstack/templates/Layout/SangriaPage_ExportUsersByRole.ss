<h2>Users By Role</h2>
<form method="get" id="form-export-cla-users-data" name="form-export-cla-users-data" action="$Link(exportCLAUsers)">
    <% if Groups %>
        <span>Group Filter </span><input id="status_all" class="all_group" checked  name="status_all"  type="checkbox"  value/>Check All<br>
        <% loop Groups %>
            <li><input id="status[]" name="status[]" class="group"  type="checkbox" checked value="$Code"/>$Title</li>
        <% end_loop %>
    <% end_if %>
    <span>Fields</span><br>
    <input id="fields[]" name="fields[]"  checked type="checkbox" value="ID"/>ID
    <input id="fields[]" name="fields[]"  checked type="checkbox" value="FirstName"/>FirstName
    <input id="fields[]" name="fields[]"  checked type="checkbox" value="SurName"/>SurName
    <input id="fields[]" name="fields[]"  checked type="checkbox" value="Email"/>Email<br>
    <input id="ext" name="ext" type="hidden" value="">
    <BR>
    <button style="padding: 5px" id="btn3_xls">Export CLA Users (XLS)</button>
    <button style="padding: 5px" id="btn3_csv">Export CLA Users (CSV)</button>
</form>