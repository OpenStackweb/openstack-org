<h2>Users By Role</h2>
<form method="get" id="form-export-cla-users-data" name="form-export-cla-users-data" action="$Link(exportCLAUsers)">
    <% if Groups %>
        <span>Group Filter </span>
        <input id="status_all" class="all_group" checked  name="status_all" style="margin:0 3px 0 10px;vertical-align: top;" type="checkbox"  value/>Check All<br><br>
        <div>
        <% loop Groups %>
            <div class="cb_wrapper">
                <input id="groups[]" name="groups[]" class="group" type="checkbox" checked value="{$ID}"/>$Title
            </div>
        <% end_loop %>
        </div>
    <% end_if %>
    <hr>
    <span>Fields</span>
    <input id="fields_all" class="all_group" checked style="margin:0 3px 0 10px;vertical-align: top;" type="checkbox"  value/>Check All<br><br>

    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[Member Id]"  checked type="checkbox" value="Member.ID"/>ID
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[FirstName]"  checked type="checkbox" value="Member.FirstName"/>FirstName
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[Surname]"  checked type="checkbox" value="Member.Surname"/>Surname
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[Gender]"  checked type="checkbox" value="Member.Gender"/>Gender
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[Email]"  checked type="checkbox" value="Member.Email"/>Email
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[SecondEmail]"  checked type="checkbox" value="Member.SecondEmail"/>SecondEmail
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[ThirdEmail]"  checked type="checkbox" value="Member.ThirdEmail"/>ThirdEmail
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[Address]"  checked type="checkbox" value="Member.Address"/>Address
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[Suburb]"  checked type="checkbox" value="Member.Suburb"/>Suburb
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[City]"  checked type="checkbox" value="Member.City"/>City
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[State]"  checked type="checkbox" value="Member.State"/>State
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[PostCode]"  checked type="checkbox" value="Member.PostCode"/>PostCode
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[Country]"  checked type="checkbox" value="Member.Country"/>Country
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[JobTitle]"  checked type="checkbox" value="Member.JobTitle"/>Job Title
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[Role]"  checked type="checkbox" value="Member.Role"/>Role
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[Projects]"  checked type="checkbox" value="Member.Projects"/>Projects
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[OtherProject]"  checked type="checkbox" value="Member.OtherProject"/>Other Project
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[CompanyAffliations]"  checked type="checkbox" value="Member.CompanyAffliations"/>Company Affiliations
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[StatementOfInterest]"  checked type="checkbox" value="Member.StatementOfInterest"/>Statement Of Interest
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[IRCHandle]"  checked type="checkbox" value="Member.IRCHandle"/>IRCHandle
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[TwitterName]"  checked type="checkbox" value="Member.TwitterName"/>Twitter Name
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[LinkedInProfile]"  checked type="checkbox" value="Member.LinkedInProfile"/>LinkedIn Profile
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[ShirtSize]"  checked type="checkbox" value="Member.ShirtSize"/>ShirtSize
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[FoodPreference]"  checked type="checkbox" value="Member.FoodPreference"/>Food Preference
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[OtherFood]"  checked type="checkbox" value="Member.OtherFood"/>Other Food
    </div>
    <div class="cb_wrapper">
        <input id="fields[]" class="field_cb" name="fields[SubscribedToNewsletter]"  checked type="checkbox" value="Member.SubscribedToNewsletter"/>Subscribed To Newsletter
    </div>

    <br>
    <input id="ext" name="ext" type="hidden" value="">
    <BR>
    <button style="padding: 5px" id="btn3_xls">Export CLA Users (XLS)</button>
    <button style="padding: 5px" id="btn3_csv">Export CLA Users (CSV)</button>
</form>
<br>
<br>