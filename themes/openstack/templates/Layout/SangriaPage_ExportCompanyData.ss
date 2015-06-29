<style>
    .cb_filter {margin:10px 10px 0 0;float:left;width:160px;}
    .cb_filter input {margin:0 3px 0 0;vertical-align:top;}
    #btn2_xls,#btn2_csv,#btn2_mgn {margin-right:10px;}
</style>

<h2>Company Data</h2>
<form method="get" id="form-export-company-data" name="form-export-company-data" action="$Link(exportCorporateSponsors)">
    <span>Fields</span><br>
    <div class="cb_filter"><input id="fields[]" name="fields[]"  checked type="checkbox" value="Name"/>Name</div>
    <div class="cb_filter"><input id="fields[]" name="fields[]"  checked type="checkbox" value="City"/>City</div>
    <div class="cb_filter"><input id="fields[]" name="fields[]"  checked type="checkbox" value="State"/>State</div>
    <div class="cb_filter"><input id="fields[]" name="fields[]"  checked type="checkbox" value="Country"/>Country</div>
    <div class="cb_filter"><input id="fields[]" name="fields[]"  checked type="checkbox" value="Industry"/>Industry</div>
    <div class="cb_filter"><input id="fields[]" name="fields[]"  checked type="checkbox" value="ContactEmail"/>ContactEmail</div>
    <div class="cb_filter"><input id="fields[]" name="fields[]"  checked type="checkbox" value="AdminEmail"/>AdminEmail</div>
    <div style="clear:both"></div>
    <BR>
    <span>Levels</span><br>
    <div class="cb_filter"><input id="levels[]" name="levels[]"  checked type="checkbox" value="Platinum"/>Platinum</div>
    <div class="cb_filter"><input id="levels[]" name="levels[]"  checked type="checkbox" value="Gold"/>Gold</div>
    <div class="cb_filter"><input id="levels[]" name="levels[]"  checked type="checkbox" value="Startup"/>Startup</div>
    <div class="cb_filter"><input id="levels[]" name="levels[]"  checked type="checkbox" value="Mention"/>Mention</div>
    <div style="clear:both"></div>
    <br><br>
    <input id="ext" name="ext" type="hidden" value="">
    <button style="padding: 5px" id="btn2_xls">Export Company Data (XLS)</button>
    <button style="padding: 5px" id="btn2_csv">Export Company Data (CSV)</button>
</form>
