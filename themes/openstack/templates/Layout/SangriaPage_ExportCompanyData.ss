<style>
    .cb_field,.cb_report {margin:10px 50px 0 0;float:left;}
    .cb_field input {margin:0 3px 0 0;vertical-align:top;}
    .cb_report input {margin:0 3px 0 0;vertical-align:top;}
    #btn2_xls,#btn2_csv,#btn2_mgn {margin-right:10px;}
</style>

<h2>Company Data</h2>
<form method="get" id="form-export-company-data" name="form-export-company-data" action="$Link(exportCompanyData)">
    <span>Reports</span><br>
    <div class="cb_report"><input type="radio" checked class="company_report" value="sponsorship_type" name="report_name" />Sponsorship Level for Summits</div>
    <div class="cb_report"><input type="radio" class="company_report" value="member_level" name="report_name" />Foundation Sponsorship Level</div>
    <div class="cb_report"><input type="radio" class="company_report" value="users_roles" name="report_name" />Users/Roles</div>
    <div class="cb_report"><input type="radio" class="company_report" value="affiliates" name="report_name" />Employees/Affiliates</div>
    <div class="cb_report"><input type="radio" class="company_report" value="deployments" name="report_name" />Deployments</div>
    <div class="cb_report"><input type="radio" class="company_report" value="deployment_surveys" name="report_name" />Deployment Surveys</div>
    <div class="cb_report"><input type="radio" class="company_report" value="speakers" name="report_name" />Speakers</div>
    <div style="clear:both"></div>
    <br>
    <div style="clear:both;margin-top:10px;"></div>
    <span>Optional Fields</span><br>
    <div class="cb_field company"><input name="fields[Company Id]" checked type="checkbox" value="Company.ID"/>Company Id</div>
    <div class="cb_field company"><input name="fields[Company]" checked type="checkbox" value="Company.Name"/>Company Name</div>
    <div class="cb_field company"><input name="fields[City]" checked type="checkbox" value="Company.City"/>City</div>
    <div class="cb_field company"><input name="fields[State]" checked type="checkbox" value="Company.State"/>State</div>
    <div class="cb_field company"><input name="fields[Country]" checked type="checkbox" value="Company.Country"/>Country</div>
    <div class="cb_field company"><input name="fields[Industry]" checked type="checkbox" value="Company.Industry"/>Industry</div>
    <div class="cb_field company"><input name="fields[Contact Email]" checked type="checkbox" value="Company.ContactEmail"/>Contact Email</div>
    <div class="cb_field company"><input name="fields[Admin Email]" checked type="checkbox" value="Company.AdminEmail"/>Admin Email</div>

    <div class="cb_field org" style="display:none"><input name="fields[Organization Id]" checked type="checkbox" value="Org.ID"/>Organization Id</div>
    <div class="cb_field org" style="display:none"><input name="fields[Organization]" checked type="checkbox" value="Org.Name"/>Organization Name</div>

    <div class="cb_field member" style="display:none"><input name="fields[Member Id]" checked type="checkbox" value="Member.ID"/>Member Id</div>
    <div class="cb_field member" style="display:none"><input name="fields[Member Name]" checked type="checkbox" value="Member.FirstName"/>Member First Name</div>
    <div class="cb_field member" style="display:none"><input name="fields[Member Surname]" checked type="checkbox" value="Member.Surname"/>Member Last Name</div>
    <div class="cb_field member" style="display:none"><input name="fields[Member Email]" checked type="checkbox" value="Member.Email"/>Member Email</div>

    <div style="clear:both"></div>
    <br><hr>
    <div class="cb_report"><input type="radio" checked value="xls" name="extension" />.xls</div>
    <div class="cb_report"><input type="radio" value="csv" name="extension" />.csv</div>
    <button style="padding: 5px;width:200px">Export</button>
    <div style="clear:both"></div>
</form>
