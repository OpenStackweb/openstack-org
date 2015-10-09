<h2>Gerrit Users with Foundation Member Status</h2>
<form method="get" id="form-export-gerrit-users-data" name="form-export-gerrit-users-data" action="$Link(exportGerritUsers)">
    <span>Status Filter </span><br>
    <input id="status[]" name="status[]"  type="checkbox" checked value="foundation-members"/>Foundation Members
    <input id="status[]" name="status[]"  type="checkbox" checked value="community-members"/>Community Members
    <input id="ext" name="ext" type="hidden" value="">
    <BR>
    <BR>
    <button style="padding: 5px" id="btn4_xls">Export Gerrit Users (XLS)</button>
    <button style="padding: 5px" id="btn4_csv">Export Gerrit Users (CSV)</button>
</form>