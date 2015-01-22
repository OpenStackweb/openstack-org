<link rel="stylesheet" href="/themes/openstack/css/bootstrap.min.css" type="text/css" media="screen, projection">

<style>
    input[type="radio"], input[type="checkbox"] {
        line-height: normal !important;
        margin: 4px 0 0 !important;
        margin-left: -20px !important;
        position: absolute !important;
        top: 0 !important;
    }

    caption {
        background: none repeat scroll 0 0;
    }

    thead th {
        background: none repeat scroll 0 0;
    }

    .radio + .radio, .checkbox + .checkbox {
        margin-top: 10px !important;
    }
</style>

<div class="container">
<a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center"
   class="roundedButton merge" href="#" id="merge">Merge Account</a>
<table id="merge_table" class="table table-condensed" data-confirmation-token="$ConfirmationToken">
<caption>Merge Account</caption>
<thead>
<tr>
    <th>#</th>
    <th>$CurrentAccount.getEmail&nbsp;<span style="cursor: pointer" title="current account"
                                            class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></th>
    <th>$DupeAccount.getEmail&nbsp;<span style="cursor: pointer" title="duplicate account"
                                         class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></th>
    <th>Merge Result</th>
</tr>
</thead>
<tbody>
<tr class="gerrit_id_row">
    <td>Gerrit&nbsp;ID&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign"
                                  aria-hidden="true"></span></td>
    <td>
        <% if currentRequestAnyAccountHasGerrit %>
            <% with CurrentAccount %>
                <div class="checkbox">
                    <label>
                        <input id="gerrit_id_{$ID}" type="radio" class="gerrit_id" name="gerrit_id" data-member-id="{$ID}"
                               value="<% if isGerritUser %>$getGerritId<% else %>0<% end_if %>" checked><% if isGerritUser %>$getGerritId<% else %>NOT SET<% end_if %>
                    </label>
                </div>
            <% end_with %>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
    <td>
        <% if currentRequestAnyAccountHasGerrit %>
        <% with DupeAccount %>
                <div class="checkbox">
                    <label>
                        <input id="gerrit_id_{$ID}" type="radio" class="gerrit_id" name="gerrit_id" data-member-id="{$ID}"
                               value="<% if isGerritUser %>$getGerritId<% else %>0<% end_if %>" ><% if isGerritUser %>$getGerritId<% else %>NOT SET<% end_if %>
                    </label>
                </div>
        <% end_with %>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
    <td>
        <% if currentRequestAnyAccountHasGerrit %>
            <div class="checkbox gerrit_id_div" id="gerrit_id_{$CurrentAccount.ID}">
                <label><% if CurrentAccount.isGerritUser %>$CurrentAccount.getGerritId<% else %>NOT SET<% end_if %></label>
            </div>
            <div class="checkbox hidden gerrit_id_div" id="gerrit_id_{$DupeAccount.ID}">
                <label><% if DupeAccount.isGerritUser %>$DupeAccount.getGerritId<% else %>NOT SET<% end_if %></label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="first_name_row">
    <td>First Name</td>
    <td>
        <% with CurrentAccount %>
            <% if FirstName %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="first_name" name="first_name" data-member-id="{$ID}"
                               value="$FirstName" checked>$FirstName
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if FirstName %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="first_name" name="first_name" data-member-id="{$ID}"
                               value="$FirstName">$FirstName
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.FirstName && DupeAccount.FirstName %>
            <div class="checkbox first_name_div" id="first_name_{$CurrentAccount.ID}">
                <label>$CurrentAccount.FirstName</label>
            </div>
            <div class="checkbox hidden first_name_div" id="first_name_{$DupeAccount.ID}">
                <label>$DupeAccount.FirstName</label>
            </div>
        <% else_if  CurrentAccount.FirstName %>
            $CurrentAccount.FirstName
        <% else_if  DupeAccount.FirstName %>
            $DupeAccount.FirstName
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="surname_row">
    <td>Last Name</td>
    <td>
        <% with CurrentAccount %>
            <% if Surname %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="surname" name="surname" data-member-id="{$ID}" value="$Surname"
                               checked>$Surname
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if Surname %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="surname" name="surname" data-member-id="{$ID}" value="$Surname"
                               <% if not Top.CurrentAccount.Surname %>checked<% end_if%>>$Surname
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.Surname && DupeAccount.Surname %>
            <div class="checkbox surname_div" id="surname_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Surname</label>
            </div>
            <div class="checkbox hidden surname_div" id="surname_{$DupeAccount.ID}">
                <label>$DupeAccount.Surname</label>
            </div>
        <% else_if  CurrentAccount.Surname %>
            $CurrentAccount.Surname
        <% else_if  DupeAccount.Surname %>
            $DupeAccount.Surname
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="email_row">
    <td>Primary Email Address</td>
    <td>
        <% with CurrentAccount %>
            <% if Email %>
                <div class="checkbox">
                    <label>
                        <input id="email_{$ID}" type="radio" class="email" name="email" data-member-id="{$ID}" value="$Email"
                               checked>$Email
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if Email %>
                <div class="checkbox">
                    <label>
                        <input id="email_{$ID}" type="radio" class="email" name="email" data-member-id="{$ID}" value="$Email"
                               <% if not Top.CurrentAccount.Email %>checked<% end_if%>>$Email
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.Email && DupeAccount.Email %>
            <div class="checkbox email_div" id="email_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Email</label>
            </div>
            <div class="checkbox hidden email_div" id="email_{$DupeAccount.ID}">
                <label>$DupeAccount.Email</label>
            </div>
        <% else_if  CurrentAccount.Email %>
            $CurrentAccount.Email
        <% else_if  DupeAccount.Email %>
            $DupeAccount.Email
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="second_email_row">
    <td>Second Email</td>
    <td>
        <% with CurrentAccount %>
            <% if SecondEmail %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="second_email" name="second_email" data-member-id="{$ID}"
                               value="$SecondEmail" checked>$SecondEmail
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if SecondEmail %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="second_email" name="second_email" data-member-id="{$ID}"
                               value="$SecondEmail"
                               <% if not Top.CurrentAccount.SecondEmail %>checked<% end_if%>>$SecondEmail
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.SecondEmail && DupeAccount.SecondEmail %>
            <div class="checkbox second_email_div" id="second_email_{$CurrentAccount.ID}">
                <label>$CurrentAccount.SecondEmail</label>
            </div>
            <div class="checkbox hidden second_email_div" id="second_email_{$DupeAccount.ID}">
                <label>$DupeAccount.SecondEmail</label>
            </div>
        <% else_if  CurrentAccount.SecondEmail %>
            $CurrentAccount.SecondEmail
        <% else_if  DupeAccount.SecondEmail %>
            $DupeAccount.SecondEmail
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="third_email_row">
    <td>Third Email</td>
    <td>
        <% with CurrentAccount %>
            <% if ThirdEmail %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="third_email" name="third_email" data-member-id="{$ID}"
                               value="$ThirdEmail" checked>$ThirdEmail
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if ThirdEmail %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="third_email" name="third_email" data-member-id="{$ID}"
                               value="$ThirdEmail"
                               <% if not Top.CurrentAccount.ThirdEmail %>checked<% end_if%>>$ThirdEmail
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.ThirdEmail && DupeAccount.ThirdEmail %>
            <div class="checkbox third_email_div" id="third_email_{$CurrentAccount.ID}">
                <label>$CurrentAccount.ThirdEmail</label>
            </div>
            <div class="checkbox hidden third_email_div" id="third_email_{$DupeAccount.ID}">
                <label>$DupeAccount.ThirdEmail</label>
            </div>
        <% else_if  CurrentAccount.ThirdEmail %>
            $CurrentAccount.ThirdEmail
        <% else_if  DupeAccount.ThirdEmail %>
            $DupeAccount.ThirdEmail
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="shirt_size_row">
    <td>Shirt Size</td>
    <td>
        <% with CurrentAccount %>
            <% if ShirtSize %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="shirt_size" name="shirt_size" data-member-id="{$ID}"
                               value="$ShirtSize" checked>$ShirtSize
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if ShirtSize %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="shirt_size" name="shirt_size" data-member-id="{$ID}"
                               value="$ShirtSize" <% if not Top.CurrentAccount.ShirtSize %>checked<% end_if%>>$ShirtSize
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.ShirtSize && DupeAccount.ShirtSize %>
            <div class="checkbox shirt_size_div" id="shirt_size_{$CurrentAccount.ID}">
                <label>$CurrentAccount.ShirtSize</label>
            </div>
            <div class="checkbox hidden shirt_size_div" id="shirt_size_{$DupeAccount.ID}">
                <label>$DupeAccount.ShirtSize</label>
            </div>
        <% else_if  CurrentAccount.ShirtSize %>
            $CurrentAccount.ShirtSize
        <% else_if  DupeAccount.ShirtSize %>
            $DupeAccount.ShirtSize
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="statement_interest_row">
    <td>Statement Of Interest</td>
    <td>
        <% with CurrentAccount %>
            <% if StatementOfInterest %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="statement_interest" name="statement_interest" data-member-id="{$ID}"
                               value="$StatementOfInterest" checked>$StatementOfInterest
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if StatementOfInterest %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="statement_interest" name="statement_interest" data-member-id="{$ID}"
                               value="$StatementOfInterest"
                               <% if not Top.CurrentAccount.StatementOfInterest %>checked<% end_if%>>$StatementOfInterest
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.StatementOfInterest && DupeAccount.StatementOfInterest %>
            <div class="checkbox statement_interest_div" id="statement_interest_{$CurrentAccount.ID}">
                <label>$CurrentAccount.StatementOfInterest</label>
            </div>
            <div class="checkbox hidden statement_interest_div" id="statement_interest_{$DupeAccount.ID}">
                <label>$DupeAccount.StatementOfInterest</label>
            </div>
        <% else_if  CurrentAccount.StatementOfInterest %>
            $CurrentAccount.StatementOfInterest
        <% else_if  DupeAccount.StatementOfInterest %>
            $DupeAccount.StatementOfInterest
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="bio_row">
    <td>Bio</td>
    <td>
        <% with CurrentAccount %>
            <% if Bio %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="bio" name="bio" data-member-id="{$ID}" value="$Bio" checked>$Bio
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if Bio %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="bio" name="bio" data-member-id="{$ID}" value="$Bio"
                               <% if not Top.CurrentAccount.Bio %>checked<% end_if%>>$Bio
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.Bio && DupeAccount.Bio %>
            <div class="checkbox bio_div" id="bio_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Bio</label>
            </div>
            <div class="checkbox hidden bio_div" id="bio_{$DupeAccount.ID}">
                <label>$DupeAccount.Bio</label>
            </div>
        <% else_if  CurrentAccount.Bio %>
            $CurrentAccount.Bio
        <% else_if  DupeAccount.Bio %>
            $DupeAccount.Bio
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="food_preference_row">
    <td>Food Preference</td>
    <td>
        <% with CurrentAccount %>
            <% if FoodPreference %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="food_preference" name="food_preference" data-member-id="{$ID}"
                               value="$FoodPreference" checked>$FoodPreference
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if FoodPreference %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="food_preference" name="food_preference" data-member-id="{$ID}"
                               value="$FoodPreference"
                               <% if not Top.CurrentAccount.FoodPreference %>checked<% end_if%>>$FoodPreference
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.FoodPreference && DupeAccount.FoodPreference %>
            <div class="checkbox food_preference_div" id="food_preference_{$CurrentAccount.ID}">
                <label>$CurrentAccount.FoodPreference</label>
            </div>
            <div class="checkbox hidden food_preference_div" id="food_preference_{$DupeAccount.ID}">
                <label>$DupeAccount.FoodPreference</label>
            </div>
        <% else_if  CurrentAccount.FoodPreference %>
            $CurrentAccount.FoodPreference
        <% else_if  DupeAccount.FoodPreference %>
            $DupeAccount.FoodPreference
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="other_food_row">
    <td>Other Food</td>
    <td>
        <% with CurrentAccount %>
            <% if OtherFood %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="other_food" name="other_food" data-member-id="{$ID}"
                               value="$OtherFood" checked>$OtherFood
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if OtherFood %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="other_food" name="other_food" data-member-id="{$ID}"
                               value="$OtherFood" <% if not Top.CurrentAccount.OtherFood %>checked<% end_if%>>$OtherFood
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.OtherFood && DupeAccount.OtherFood %>
            <div class="checkbox other_food_div" id="other_food_{$CurrentAccount.ID}">
                <label>$CurrentAccount.OtherFood</label>
            </div>
            <div class="checkbox hidden other_food_div" id="other_food_{$DupeAccount.ID}">
                <label>$DupeAccount.OtherFood</label>
            </div>
        <% else_if  CurrentAccount.OtherFood %>
            $CurrentAccount.OtherFood
        <% else_if  DupeAccount.OtherFood %>
            $DupeAccount.OtherFood
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="irc_handle_row">
    <td>IRC Handle</td>
    <td>
        <% with CurrentAccount %>
            <% if IRCHandle %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="irc_handle" name="irc_handle" data-member-id="{$ID}"
                               value="$IRCHandle" checked>$IRCHandle
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if IRCHandle %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="irc_handle" name="irc_handle" data-member-id="{$ID}"
                               value="$IRCHandle" <% if not Top.CurrentAccount.IRCHandle %>checked<% end_if%>>$IRCHandle
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.IRCHandle && DupeAccount.IRCHandle %>
            <div class="checkbox irc_handle_div" id="irc_handle_{$CurrentAccount.ID}">
                <label>$CurrentAccount.IRCHandle</label>
            </div>
            <div class="checkbox hidden irc_handle_div" id="irc_handle_{$DupeAccount.ID}">
                <label>$DupeAccount.IRCHandle</label>
            </div>
        <% else_if  CurrentAccount.IRCHandle %>
            $CurrentAccount.IRCHandle
        <% else_if  DupeAccount.IRCHandle %>
            $DupeAccount.IRCHandle
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="twitter_name_row">
    <td>Twitter Name</td>
    <td>
        <% with CurrentAccount %>
            <% if TwitterName %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="twitter_name" name="twitter_name" data-member-id="{$ID}"
                               value="$TwitterName" checked>$TwitterName
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if TwitterName %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="twitter_name" name="twitter_name" data-member-id="{$ID}"
                               value="$TwitterName"
                               <% if not Top.CurrentAccount.TwitterName %>checked<% end_if%>>$TwitterName
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.TwitterName && DupeAccount.TwitterName %>
            <div class="checkbox twitter_name_div" id="twitter_name_{$CurrentAccount.ID}">
                <label>$CurrentAccount.TwitterName</label>
            </div>
            <div class="checkbox hidden twitter_name_div" id="twitter_name_{$DupeAccount.ID}">
                <label>$DupeAccount.TwitterName</label>
            </div>
        <% else_if  CurrentAccount.TwitterName %>
            $CurrentAccount.TwitterName
        <% else_if  DupeAccount.TwitterName %>
            $DupeAccount.TwitterName
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="linkedin_profile_row">
    <td>LinkedIn Profile</td>
    <td>
        <% with CurrentAccount %>
            <% if LinkedInProfile %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="linkedin_profile" name="linkedin_profile" data-member-id="{$ID}"
                               value="$LinkedInProfile" checked>$LinkedInProfile
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if LinkedInProfile %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="linkedin_profile" name="linkedin_profile" data-member-id="{$ID}"
                               value="$LinkedInProfile"
                               <% if not Top.CurrentAccount.LinkedInProfile %>checked<% end_if%>>$LinkedInProfile
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.LinkedInProfile && DupeAccount.LinkedInProfile %>
            <div class="checkbox linkedin_profile_div" id="linkedin_profile_{$CurrentAccount.ID}">
                <label>$CurrentAccount.LinkedInProfile</label>
            </div>
            <div class="checkbox hidden linkedin_profile_div" id="linkedin_profile_{$DupeAccount.ID}">
                <label>$DupeAccount.LinkedInProfile</label>
            </div>
        <% else_if  CurrentAccount.LinkedInProfile %>
            <div class="checkbox linkedin_profile_div" id="linkedin_profile_{$CurrentAccount.ID}">
                <label>$CurrentAccount.LinkedInProfile</label>
            </div>
        <% else_if  DupeAccount.LinkedInProfile %>
            <div class="checkbox linkedin_profile_div" id="linkedin_profile_{$DupeAccount.ID}">
                <label>$DupeAccount.LinkedInProfile</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="projects_row">
    <td>Projects</td>
    <td>
        <% with CurrentAccount %>
            <% if Projects %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="projects" name="projects" data-member-id="{$ID}" value="$Projects"
                               checked>$Projects
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if Projects %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="projects" name="projects" data-member-id="{$ID}" value="$Projects"
                               <% if not Top.CurrentAccount.Projects %>checked<% end_if%>>$Projects
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.Projects && DupeAccount.Projects %>
            <div class="checkbox projects_div" id="projects_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Projects</label>
            </div>
            <div class="checkbox hidden projects_div" id="projects_{$DupeAccount.ID}">
                <label>$DupeAccount.Projects</label>
            </div>
        <% else_if  CurrentAccount.Projects %>
            <div class="checkbox projects_div" id="projects_{$CurrentAccount.ID}">
                <label>$CurrentAccount.ShirtSize</label>
            </div>
        <% else_if  DupeAccount.Projects %>
            <div class="checkbox projects_div" id="projects_{$DupeAccount.ID}">
                <label>$DupeAccount.Projects</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="other_project_row">
    <td>Other Project</td>
    <td>
        <% with CurrentAccount %>
            <% if OtherProject %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="other_project" name="other_project" data-member-id="{$ID}"
                               value="$OtherProject" checked>$OtherProject
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if OtherProject %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="other_project" name="other_project" data-member-id="{$ID}"
                               value="$OtherProject"
                               <% if not Top.CurrentAccount.OtherProject %>checked<% end_if%>>$OtherProject
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.OtherProject && DupeAccount.OtherProject %>
            <div class="checkbox other_project_div" id="other_project_{$CurrentAccount.ID}">
                <label>$CurrentAccount.OtherProject</label>
            </div>
            <div class="checkbox hidden other_project_div" id="other_project_{$DupeAccount.ID}">
                <label>$DupeAccount.OtherProject</label>
            </div>
        <% else_if  CurrentAccount.OtherProject %>
            <div class="checkbox other_project_div" id="other_project_{$CurrentAccount.ID}">
                <label>$CurrentAccount.OtherProject</label>
            </div>
        <% else_if  DupeAccount.OtherProject %>
            <div class="checkbox other_project_div" id="other_project_{$DupeAccount.ID}">
                <label>$DupeAccount.OtherProject</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="address_row">
    <td>Street Address (Line1)</td>
    <td>
        <% with CurrentAccount %>
            <% if Address %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="address" name="address" data-member-id="{$ID}" value="$Address"
                               checked>$Address
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if Address %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="address" name="address" data-member-id="{$ID}" value="$ShirtSize"
                               <% if not Top.CurrentAccount.Address %>checked<% end_if%>>$Address
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.Address && DupeAccount.Address %>
            <div class="checkbox address_div" id="address_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Address</label>
            </div>
            <div class="checkbox hidden address_div" id="address_{$DupeAccount.ID}">
                <label>$DupeAccount.Address</label>
            </div>
        <% else_if  CurrentAccount.Address %>
            <div class="checkbox address_div" id="address_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Address</label>
            </div>
        <% else_if  DupeAccount.Address %>
            <div class="checkbox address_div" id="address_{$DupeAccount.ID}">
                <label>$DupeAccount.Address</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="suburb_row">
    <td>Street Address (Line2)</td>
    <td>
        <% with CurrentAccount %>
            <% if Suburb %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="suburb" name="suburb" data-member-id="{$ID}" value="$Suburb"
                               checked>$Suburb
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if Suburb %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="suburb" name="suburb" data-member-id="{$ID}" value="$Suburb"
                               <% if not Top.CurrentAccount.Suburb %>checked<% end_if%>>$Suburb
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.Suburb && DupeAccount.Suburb %>
            <div class="checkbox suburb_div" id="suburb_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Suburb</label>
            </div>
            <div class="checkbox hidden suburb_div" id="suburb_{$DupeAccount.ID}">
                <label>$DupeAccount.Suburb</label>
            </div>
        <% else_if  CurrentAccount.Suburb %>
            <div class="checkbox suburb_div" id="suburb_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Suburb</label>
            </div>
        <% else_if  DupeAccount.Suburb %>
            <div class="checkbox suburb_div" id="suburb_{$DupeAccount.ID}">
                <label>$DupeAccount.Suburb</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="state_row">
    <td>State</td>
    <td>
        <% with CurrentAccount %>
            <% if State %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="state" name="state" data-member-id="{$ID}" value="$State"
                               checked>$State
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if State %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="state" name="state" data-member-id="{$ID}" value="$State"
                               <% if not Top.CurrentAccount.State %>checked<% end_if%>>$State
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.State && DupeAccount.State %>
            <div class="checkbox state_div" id="state_{$CurrentAccount.ID}">
                <label>$CurrentAccount.State</label>
            </div>
            <div class="checkbox hidden state_div" id="state_{$DupeAccount.ID}">
                <label>$DupeAccount.State</label>
            </div>
        <% else_if  CurrentAccount.State %>
            <div class="checkbox state_div" id="state_{$CurrentAccount.ID}">
                <label>$CurrentAccount.State</label>
            </div>
        <% else_if  DupeAccount.State %>
            <div class="checkbox state_div" id="state_{$DupeAccount.ID}">
                <label>$DupeAccount.State</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="postcode_row">
    <td>Postcode</td>
    <td>
        <% with CurrentAccount %>
            <% if Postcode %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="postcode" name="postcode" data-member-id="{$ID}" value="$Postcode"
                               checked>$Postcode
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if Postcode %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="postcode" name="postcode" data-member-id="{$ID}" value="$Postcode"
                               <% if not Top.CurrentAccount.Postcode %>checked<% end_if%>>$Postcode
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.Postcode && DupeAccount.Postcode %>
            <div class="checkbox postcode_div" id="postcode_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Postcode</label>
            </div>
            <div class="checkbox hidden postcode_div" id="postcode_{$DupeAccount.ID}">
                <label>$DupeAccount.Postcode</label>
            </div>
        <% else_if  CurrentAccount.Postcode %>
            <div class="checkbox postcode_div" id="postcode_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Postcode</label>
            </div>
        <% else_if  DupeAccount.Postcode %>
            <div class="checkbox postcode_div" id="postcode_{$DupeAccount.ID}">
                <label>$DupeAccount.Postcode</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="country_row">
    <td>Country</td>
    <td>
        <% with CurrentAccount %>
            <% if Country %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="country" name="country" data-member-id="{$ID}" value="$Country"
                               checked>$Country
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if Country %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="country" name="country" data-member-id="{$ID}" value="$Country"
                               <% if not Top.CurrentAccount.Country %>checked<% end_if%>>$Country
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.Country && DupeAccount.Country %>
            <div class="checkbox country_div" id="country_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Country</label>
            </div>
            <div class="checkbox hidden country_div" id="country_{$DupeAccount.ID}">
                <label>$DupeAccount.Country</label>
            </div>
        <% else_if  CurrentAccount.Country %>
            <div class="checkbox country_div" id="country_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Country</label>
            </div>
        <% else_if  DupeAccount.Country %>
            <div class="checkbox country_div" id="country_{$DupeAccount.ID}">
                <label>$DupeAccount.Country</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="city_row">
    <td>City</td>
    <td>
        <% with CurrentAccount %>
            <% if City %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="city" name="city" data-member-id="{$ID}" value="$City" checked>$City
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>T
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if City %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="city" name="city" data-member-id="{$ID}" value="$City"
                               <% if not Top.CurrentAccount.City %>checked<% end_if%>>$City
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.City && DupeAccount.City %>
            <div class="checkbox city_div" id="city_{$CurrentAccount.ID}">
                <label>$CurrentAccount.City</label>
            </div>
            <div class="checkbox hidden city_div" id="city_{$DupeAccount.ID}">
                <label>$DupeAccount.City</label>
            </div>
        <% else_if  CurrentAccount.City %>
            <div class="checkbox city_div" id="city_{$CurrentAccount.ID}">
                <label>$CurrentAccount.City</label>
            </div>
        <% else_if  DupeAccount.City %>
            <div class="checkbox city_div" id="city_{$DupeAccount.ID}">
                <label>$DupeAccount.City</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="gender_row">
    <td>Gender</td>
    <td>
        <% with CurrentAccount %>
            <% if Gender %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="gender" name="gender" data-member-id="{$ID}" value="$Gender"
                               checked>$Gender
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if Gender %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="gender" name="gender" data-member-id="{$ID}" value="$Gender"
                               <% if not Top.CurrentAccount.Gender %>checked<% end_if%>>$Gender
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.Gender && DupeAccount.Gender %>
            <div class="checkbox gender_div" id="gender_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Gender</label>
            </div>
            <div class="checkbox hidden gender_div" id="gender_{$DupeAccount.ID}">
                <label>$DupeAccount.Gender</label>
            </div>
        <% else_if  CurrentAccount.Gender %>
            <div class="checkbox gender_div" id="gender_{$CurrentAccount.ID}">
                <label>$CurrentAccount.Gender</label>
            </div>
        <% else_if  DupeAccount.Gender %>
            <div class="checkbox gender_div" id="gender_{$DupeAccount.ID}">
                <label>$DupeAccount.Gender</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr class="photo_row">
    <td>Photo</td>
    <td>
        <% with CurrentAccount %>
            <% if Photo.exists %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="photo" name="photo" data-member-id="{$ID}" value="$Photo.ID"
                               checked>$ProfilePhoto
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if Photo.exists %>
                <div class="checkbox">
                    <label>
                        <input type="radio" class="photo" name="photo" data-member-id="{$ID}" value="$Photo.ID"
                               <% if not Top.CurrentAccount.Photo.exists %>checked<% end_if%>>$ProfilePhoto
                    </label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>NOT SET</label>
                </div>
            <% end_if %> <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.Photo.exists && DupeAccount.Photo.exists %>
            <div class="checkbox photo_div" id="photo_{$CurrentAccount.ID}">
                <label>$CurrentAccount.ProfilePhoto</label>
            </div>
            <div class="checkbox hidden photo_div" id="photo_{$DupeAccount.ID}">
                <label>$DupeAccount.ProfilePhoto</label>
            </div>
        <% else_if  CurrentAccount.Photo.exists %>
            <div class="checkbox photo_div" id="photo_{$CurrentAccount.ID}">
                <label>$CurrentAccount.ProfilePhoto</label>
            </div>
        <% else_if  DupeAccount.Photo.exists %>
            <div class="checkbox photo_div" id="photo_{$DupeAccount.ID}">
                <label>$DupeAccount.ProfilePhoto</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>NOT SET</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr>
    <td>Is Candidate&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"
                                title="this merge will be performed automatically"></span></td>
    <td>
        <% with CurrentAccount %>
            <% if isCandidate %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if isCandidate %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.isCandidate && DupeAccount.isCandidate %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  CurrentAccount.isCandidate %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  DupeAccount.isCandidate %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>No</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr>
    <td>Is Speaker&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"
                              title="this merge will be performed automatically"></span></td>
    <td>
        <% with CurrentAccount %>
            <% if isSpeaker %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>s
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if isSpeaker %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.isSpeaker && DupeAccount.isSpeaker %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  CurrentAccount.isSpeaker %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  DupeAccount.isSpeaker %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>No</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr>
    <td>Is MarketPlace Admin&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign"
                                        aria-hidden="true" title="this merge will be performed automatically"></span>
    </td>
    <td>
        <% with CurrentAccount %>
            <% if isMarketPlaceAdmin %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if isMarketPlaceAdmin %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.isMarketPlaceAdmin && DupeAccount.isMarketPlaceAdmin %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  CurrentAccount.isMarketPlaceAdmin %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  DupeAccount.isMarketPlaceAdmin %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>No</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr>
    <td>Is Company Admin&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign"
                                    aria-hidden="true" title="this merge will be performed automatically"></span></td>
    <td>
        <% with CurrentAccount %>
            <% if isCompanyAdmin %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if isCompanyAdmin %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.isCompanyAdmin && DupeAccount.isCompanyAdmin %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  CurrentAccount.isCompanyAdmin %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  DupeAccount.isCompanyAdmin %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>No</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr>
    <td>Is Training Admin&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign"
                                     aria-hidden="true" title="this merge will be performed automatically"></span></td>
    <td>
        <% with CurrentAccount %>
            <% if isTrainingAdmin %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if isTrainingAdmin %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.isTrainingAdmin && DupeAccount.isTrainingAdmin %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  CurrentAccount.isTrainingAdmin %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  DupeAccount.isTrainingAdmin %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>No</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr>
    <td>Has Deployment Surveys&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign"
                                          aria-hidden="true" title="this merge will be performed automatically"></span>
    </td>
    <td>
        <% with CurrentAccount %>
            <% if hasDeploymentSurveys %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if hasDeploymentSurveys %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.hasDeploymentSurveys && DupeAccount.hasDeploymentSurveys %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  CurrentAccount.hasDeploymentSurveys %>
            <div class="checkbox">
                <label>Yes</label>
            </div>s
        <% else_if  DupeAccount.hasDeploymentSurveys %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>No</label>
            </div>
        <% end_if %>
    </td>
</tr>
<tr>
    <td>Has App Dev Surveys&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign"
                                       aria-hidden="true" title="this merge will be performed automatically"></span>
    </td>
    <td>
        <% with CurrentAccount %>
            <% if hasAppDevSurveys %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <% if hasAppDevSurveys %>
                <div class="checkbox">
                    <label>Yes</label>
                </div>
            <% else %>
                <div class="checkbox">
                    <label>No</label>
                </div>
            <% end_if %>
        <% end_with %>
    </td>
    <td>
        <% if CurrentAccount.hasAppDevSurveys && DupeAccount.hasAppDevSurveys %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  CurrentAccount.hasAppDevSurveys %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else_if  DupeAccount.hasAppDevSurveys %>
            <div class="checkbox">
                <label>Yes</label>
            </div>
        <% else %>
            <div class="checkbox">
                <label>No</label>
            </div>
        <% end_if %>
    </td>
</tr>
</tbody>
</table>
<a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center"
   class="roundedButton merge" href="#" id="merge2">Merge Account</a>
</div>