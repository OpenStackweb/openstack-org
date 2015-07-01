<style>
    .cd_filter {margin:10px 75px 0 0;float:left;cursor:pointer;}
    .cb_field {margin:10px 50px 0 0;float:left;}
    .cb_field input {margin:0 3px 0 0;vertical-align:top;}
    #btn2_xls,#btn2_csv,#btn2_mgn {margin-right:10px;}
</style>

<h2>Company Data</h2>
<form method="get" id="form-export-company-data" name="form-export-company-data" action="$Link(exportCompanyData)">
    <span>Fields</span><br>
    <div class="cb_field"><input type="radio" value="sponsorship_type" name="extra_data" />Sponsorship Level for Summits</div>
    <div class="cb_field"><input type="radio" value="member_level" name="extra_data" />Foundation Sponsorship Level</div>
    <div class="cb_field"><input type="radio" value="users_roles" name="extra_data" />Users/Roles</div>
    <div class="cb_field"><input type="radio" value="affiliates" name="extra_data" />Employees/Affiliates</div>
    <div class="cb_field"><input type="radio" value="deployments" name="extra_data" />Deployments</div>
    <div class="cb_field"><input type="radio" value="deployment_surveys" name="extra_data" />Deployment Surveys</div>
    <div class="cb_field"><input type="radio" value="speakers" name="extra_data" />Speakers</div>
    <br>
    <div style="clear:both;margin-top:10px;"></div>
    <div class="cb_field"><input id="fields[]" name="fields[]"  checked type="checkbox" value="Name"/>Name</div>
    <div class="cb_field"><input id="fields[]" name="fields[]"  checked type="checkbox" value="City"/>City</div>
    <div class="cb_field"><input id="fields[]" name="fields[]"  checked type="checkbox" value="State"/>State</div>
    <div class="cb_field"><input id="fields[]" name="fields[]"  checked type="checkbox" value="Country"/>Country</div>
    <div class="cb_field"><input id="fields[]" name="fields[]"  checked type="checkbox" value="Industry"/>Industry</div>
    <div class="cb_field"><input id="fields[]" name="fields[]"  checked type="checkbox" value="ContactEmail"/>ContactEmail</div>
    <div class="cb_field"><input id="fields[]" name="fields[]"  checked type="checkbox" value="AdminEmail"/>AdminEmail</div>
    <div style="clear:both"></div>
    <br><hr>
    <div class="cb_field"><input type="radio" checked value="xls" name="extension" />.xls</div>
    <div class="cb_field"><input type="radio" value="csv" name="extension" />.csv</div>
    <button style="padding: 5px;width:200px">Export</button>
    <div style="clear:both"></div>
</form>
