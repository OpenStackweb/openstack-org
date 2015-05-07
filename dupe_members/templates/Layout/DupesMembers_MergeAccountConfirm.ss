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
<a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-top:1em;" class="roundedButton merge" href="#" id="merge">Merge Account</a>
<table id="merge_table" class="table table-condensed" data-confirmation-token="$ConfirmationToken">
<caption style="padding: 1em;">Merge Account</caption>
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
        <div class="checkbox">
        <label>
        <input type="radio" class="second_email" name="second_email" data-member-id="{$ID}"
            <% if SecondEmail %>
                       value="$SecondEmail" checked>$SecondEmail
            <% else %>
                       value="NULL" checked>NOT SET
             <% end_if %>
            </label>
        </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="second_email" name="second_email" data-member-id="{$ID}"
                    <% if SecondEmail %>
                       value="$SecondEmail">$SecondEmail
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox second_email_div" id="second_email_{$CurrentAccount.ID}">
            <% if CurrentAccount.SecondEmail %>
                <label>$CurrentAccount.SecondEmail</label>
            <% else %>
                    <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox second_email_div hidden" id="second_email_{$DupeAccount.ID}">
            <% if DupeAccount.SecondEmail %>
                <label>$DupeAccount.SecondEmail</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="third_email_row">
    <td>Third Email</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="third_email" name="third_email" data-member-id="{$ID}"
                    <% if ThirdEmail %>
                       value="$ThirdEmail" checked>$ThirdEmail
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="third_email" name="third_email" data-member-id="{$ID}"
                    <% if ThirdEmail %>
                       value="$ThirdEmail">$ThirdEmail
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox third_email_div" id="third_email_{$CurrentAccount.ID}">
            <% if CurrentAccount.ThirdEmail %>
                <label>$CurrentAccount.ThirdEmail</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox third_email_div hidden" id="third_email_{$DupeAccount.ID}">
            <% if DupeAccount.ThirdEmail %>
                <label>$DupeAccount.ThirdEmail</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="shirt_size_row">
    <td>Shirt Size</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="shirt_size" name="shirt_size" data-member-id="{$ID}"
                    <% if ShirtSize %>
                       value="$ShirtSize" checked>$ShirtSize
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="shirt_size" name="shirt_size" data-member-id="{$ID}"
                    <% if ShirtSize %>
                       value="$ShirtSize">$ShirtSize
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox shirt_size_div" id="shirt_size_{$CurrentAccount.ID}">
            <% if CurrentAccount.ShirtSize %>
                <label>$CurrentAccount.ShirtSize</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox shirt_size_div hidden" id="shirt_size_{$DupeAccount.ID}">
            <% if DupeAccount.ShirtSize %>
                <label>$DupeAccount.ShirtSize</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="statement_interest_row">
    <td>Statement Of Interest</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="statement_interest" name="statement_interest" data-member-id="{$ID}"
                    <% if StatementOfInterest %>
                       value="$StatementOfInterest" checked>$StatementOfInterest
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="statement_interest" name="statement_interest" data-member-id="{$ID}"
                    <% if StatementOfInterest %>
                       value="$StatementOfInterest">$StatementOfInterest
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox statement_interest_div" id="statement_interest_{$CurrentAccount.ID}">
            <% if CurrentAccount.StatementOfInterest %>
                <label>$CurrentAccount.StatementOfInterest</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox statement_interest_div hidden" id="statement_interest_{$DupeAccount.ID}">
            <% if DupeAccount.StatementOfInterest %>
                <label>$DupeAccount.StatementOfInterest</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="bio_row">
    <td>Bio</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="bio" name="bio" data-member-id="{$ID}"
                    <% if Bio %>
                       value="$Bio" checked>$Bio
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="bio" name="bio" data-member-id="{$ID}"
                    <% if Bio %>
                       value="$Bio">$Bio
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox bio_div" id="bio_{$CurrentAccount.ID}">
            <% if CurrentAccount.Bio %>
                <label>$CurrentAccount.Bio</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox bio_div hidden" id="bio_{$DupeAccount.ID}">
            <% if DupeAccount.Bio %>
                <label>$DupeAccount.Bio</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="food_preference_row">
    <td>Food Preference</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="food_preference" name="food_preference" data-member-id="{$ID}"
                    <% if FoodPreference %>
                       value="$FoodPreference" checked>$FoodPreference
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="food_preference" name="food_preference" data-member-id="{$ID}"
                    <% if FoodPreference %>
                       value="$FoodPreference">$FoodPreference
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox food_preference_div" id="food_preference_{$CurrentAccount.ID}">
            <% if CurrentAccount.FoodPreference %>
                <label>$CurrentAccount.FoodPreference</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox food_preference_div hidden" id="food_preference_{$DupeAccount.ID}">
            <% if DupeAccount.FoodPreference %>
                <label>$DupeAccount.FoodPreference</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="other_food_row">
    <td>Other Food</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="other_food" name="other_food" data-member-id="{$ID}"
                    <% if OtherFood %>
                       value="$OtherFood" checked>$OtherFood
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="other_food" name="other_food" data-member-id="{$ID}"
                    <% if OtherFood %>
                       value="$OtherFood">$OtherFood
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox other_food_div" id="other_food_{$CurrentAccount.ID}">
            <% if CurrentAccount.OtherFood %>
                <label>$CurrentAccount.OtherFood</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox other_food_div hidden" id="other_food_{$DupeAccount.ID}">
            <% if DupeAccount.OtherFood %>
                <label>$DupeAccount.OtherFood</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="irc_handle_row">
    <td>IRC Handle</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="irc_handle" name="irc_handle" data-member-id="{$ID}"
                    <% if IRCHandle %>
                       value="$IRCHandle" checked>$IRCHandle
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="irc_handle" name="irc_handle" data-member-id="{$ID}"
                    <% if IRCHandle %>
                       value="$IRCHandle">$IRCHandle
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox irc_handle_div" id="irc_handle_{$CurrentAccount.ID}">
            <% if CurrentAccount.IRCHandle %>
                <label>$CurrentAccount.IRCHandle</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox irc_handle_div hidden" id="irc_handle_{$DupeAccount.ID}">
            <% if DupeAccount.IRCHandle %>
                <label>$DupeAccount.IRCHandle</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="twitter_name_row">
    <td>Twitter Name</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="twitter_name" name="twitter_name" data-member-id="{$ID}"
                    <% if TwitterName %>
                       value="$TwitterName" checked>$TwitterName
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="twitter_name" name="twitter_name" data-member-id="{$ID}"
                    <% if TwitterName %>
                       value="$TwitterName">$TwitterName
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox twitter_name_div" id="twitter_name_{$CurrentAccount.ID}">
            <% if CurrentAccount.TwitterName %>
                <label>$CurrentAccount.TwitterName</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox twitter_name_div hidden" id="twitter_name_{$DupeAccount.ID}">
            <% if DupeAccount.TwitterName %>
                <label>$DupeAccount.TwitterName</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="linkedin_profile_row">
    <td>LinkedIn Profile</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="linkedin_profile" name="linkedin_profile" data-member-id="{$ID}"
                    <% if LinkedInProfile %>
                       value="$LinkedInProfile" checked>$LinkedInProfile
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="linkedin_profile" name="linkedin_profile" data-member-id="{$ID}"
                    <% if LinkedInProfile %>
                       value="$LinkedInProfile">$LinkedInProfile
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox linkedin_profile_div" id="linkedin_profile_{$CurrentAccount.ID}">
            <% if CurrentAccount.LinkedInProfile %>
                <label>$CurrentAccount.LinkedInProfile</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox linkedin_profile_div hidden" id="linkedin_profile_{$DupeAccount.ID}">
            <% if DupeAccount.LinkedInProfile %>
                <label>$DupeAccount.LinkedInProfile</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="projects_row">
    <td>Projects</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="projects" name="projects" data-member-id="{$ID}"
                    <% if Projects %>
                       value="$Projects" checked>$Projects
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="projects" name="projects" data-member-id="{$ID}"
                    <% if Projects %>
                       value="$Projects">$Projects
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox projects_div" id="projects_{$CurrentAccount.ID}">
            <% if CurrentAccount.Projects %>
                <label>$CurrentAccount.Projects</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox projects_div hidden" id="projects_{$DupeAccount.ID}">
            <% if DupeAccount.Projects %>
                <label>$DupeAccount.Projects</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="other_project_row">
    <td>Other Project</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="other_project" name="other_project" data-member-id="{$ID}"
                    <% if OtherProject %>
                       value="$OtherProject" checked>$OtherProject
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="other_project" name="other_project" data-member-id="{$ID}"
                    <% if OtherProject %>
                       value="$OtherProject">$OtherProject
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox other_project_div" id="other_project_{$CurrentAccount.ID}">
            <% if CurrentAccount.OtherProject %>
                <label>$CurrentAccount.OtherProject</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox other_project_div hidden" id="other_project_{$DupeAccount.ID}">
            <% if DupeAccount.OtherProject %>
                <label>$DupeAccount.OtherProject</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="address_row">
    <td>Street Address (Line1)</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="address" name="address" data-member-id="{$ID}"
                    <% if Address %>
                       value="$Address" checked>$Address
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="address" name="address" data-member-id="{$ID}"
                    <% if Address %>
                       value="$Address">$Address
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox address_div" id="address_{$CurrentAccount.ID}">
            <% if CurrentAccount.Address %>
                <label>$CurrentAccount.Address</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox address_div hidden" id="address_{$DupeAccount.ID}">
            <% if DupeAccount.Address %>
                <label>$DupeAccount.Address</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="suburb_row">
    <td>Street Address (Line2)</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="suburb" name="suburb" data-member-id="{$ID}"
                    <% if Suburb %>
                       value="$Suburb" checked>$Suburb
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="suburb" name="suburb" data-member-id="{$ID}"
                    <% if Suburb %>
                       value="$Suburb">$Suburb
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox suburb_div" id="suburb_{$CurrentAccount.ID}">
            <% if CurrentAccount.Suburb %>
                <label>$CurrentAccount.Suburb</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox suburb_div hidden" id="suburb_{$DupeAccount.ID}">
            <% if DupeAccount.Suburb %>
                <label>$DupeAccount.Suburb</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="state_row">
    <td>State</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="state" name="state" data-member-id="{$ID}"
                    <% if State %>
                       value="$State" checked>$State
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="state" name="state" data-member-id="{$ID}"
                    <% if State %>
                       value="$State">$State
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox state_div" id="state_{$CurrentAccount.ID}">
            <% if CurrentAccount.State %>
                <label>$CurrentAccount.State</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox state_div hidden" id="state_{$DupeAccount.ID}">
            <% if DupeAccount.State %>
                <label>$DupeAccount.State</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="postcode_row">
    <td>Postcode</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="postcode" name="postcode" data-member-id="{$ID}"
                    <% if Postcode %>
                       value="$Postcode" checked>$Postcode
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="postcode" name="postcode" data-member-id="{$ID}"
                    <% if Postcode %>
                       value="$Postcode">$Postcode
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox postcode_div" id="postcode_{$CurrentAccount.ID}">
            <% if CurrentAccount.Postcode %>
                <label>$CurrentAccount.Postcode</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox postcode_div hidden" id="postcode_{$DupeAccount.ID}">
            <% if DupeAccount.Postcode %>
                <label>$DupeAccount.Postcode</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="country_row">
    <td>Country</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="country" name="country" data-member-id="{$ID}"
                    <% if Country %>
                       value="$Country" checked>$Country
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="country" name="country" data-member-id="{$ID}"
                    <% if Country %>
                       value="$Country">$Country
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox country_div" id="country_{$CurrentAccount.ID}">
            <% if CurrentAccount.Country %>
                <label>$CurrentAccount.Country</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox country_div hidden" id="country_{$DupeAccount.ID}">
            <% if DupeAccount.Country %>
                <label>$DupeAccount.Country</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="city_row">
    <td>City</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="city" name="city" data-member-id="{$ID}"
                    <% if City %>
                       value="$City" checked>$City
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="city" name="city" data-member-id="{$ID}"
                    <% if City %>
                       value="$City">$City
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox city_div" id="city_{$CurrentAccount.ID}">
            <% if CurrentAccount.City %>
                <label>$CurrentAccount.City</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox city_div hidden" id="city_{$DupeAccount.ID}">
            <% if DupeAccount.City %>
                <label>$DupeAccount.City</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="gender_row">
    <td>Gender</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="gender" name="gender" data-member-id="{$ID}"
                    <% if Gender %>
                       value="$Gender" checked>$Gender
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="gender" name="gender" data-member-id="{$ID}"
                    <% if Gender %>
                       value="$Gender">$Gender
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox gender_div" id="gender_{$CurrentAccount.ID}">
            <% if CurrentAccount.Gender %>
                <label>$CurrentAccount.Gender</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox gender_div hidden" id="gender_{$DupeAccount.ID}">
            <% if DupeAccount.Gender %>
                <label>$DupeAccount.Gender</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
    </td>
</tr>
<tr class="photo_row">
    <td>Photo</td>
    <td>
        <% with CurrentAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="photo" name="photo" data-member-id="{$ID}"
                    <% if Photo.exists %>
                       value="$Photo.ID" checked>$ProfilePhoto
                    <% else %>
                        value="NULL" checked>NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <% with DupeAccount %>
            <div class="checkbox">
                <label>
                <input type="radio" class="photo" name="photo" data-member-id="{$ID}"
                    <% if Photo.exists %>
                       value="$Photo.ID">$ProfilePhoto
                    <% else %>
                        value="NULL">NOT SET
                    <% end_if %>
                </label>
            </div>
        <% end_with %>
    </td>
    <td>
        <div class="checkbox photo_div" id="photo_{$CurrentAccount.ID}">
            <% if CurrentAccount.Photo.exists %>
                <label>$CurrentAccount.ProfilePhoto</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
        <div class="checkbox photo_div hidden" id="photo_{$DupeAccount.ID}">
            <% if DupeAccount.Photo.exists %>
                <label>$DupeAccount.ProfilePhoto</label>
            <% else %>
                <label>NOT SET</label>
            <% end_if %>
        </div>
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
            </div>
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