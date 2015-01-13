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
    <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center" class="roundedButton merge" href="#" id="merge">Merge Account</a>
    <table class="table table-condensed">
        <caption>Merge Account</caption>
        <thead>
        <tr>
            <th>#</th>
            <th>$CurrentAccount.getEmail&nbsp;<span style="cursor: pointer" title="current account" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></th>
            <th>$DupeAccount.getEmail&nbsp;<span style="cursor: pointer" title="duplicate account" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></th>
            <th>Merge Result</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Gerrit&nbsp;ID&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></td>
            <td>
                <% with CurrentAccount %>
                    <% if isGerritUser %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="gerrit_id" name="gerrit_id" data-member-id="{$ID}" value="$getGerritId" checked>$getGerritId
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if isGerritUser %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="gerrit_id" name="gerrit_id" data-member-id="{$ID}" value="$getGerritId" <% if not Top.CurrentAccount.isGerritUser %>checked<% end_if%>>$getGerritId
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.isGerritUser && DupeAccount.isGerritUser %>
                <div class="checkbox gerrit_id_div" id="gerrit_id_{$CurrentAccount.ID}">
                    <span>$CurrentAccount.getGerritId</span>
                </div>
                <div class="checkbox hidden gerrit_id_div" id="gerrit_id_{$DupeAccount.ID}">
                    <span>$DupeAccount.getGerritId</span>
                </div>
                <% else_if  CurrentAccount.isGerritUser %>
                    $CurrentAccount.getGerritId
                <% else_if  DupeAccount.isGerritUser %>
                    $DupeAccount.getGerritId
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>First Name</td>
            <td>
                <% with CurrentAccount %>
                    <% if FirstName %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="first_name" name="first_name" data-member-id="{$ID}" value="$FirstName" checked>$FirstName
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if isGerritUser %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="first_name" name="first_name" data-member-id="{$ID}" value="$FirstName" <% if not Top.CurrentAccount.FirstName %>checked<% end_if%>>$FirstName
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.FirstName && DupeAccount.FirstName %>
                    <div class="checkbox first_name_div" id="first_name_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.FirstName</span>
                    </div>
                    <div class="checkbox hidden first_name_div" id="first_name_{$DupeAccount.ID}">
                        <span>$DupeAccount.FirstName</span>
                    </div>
                <% else_if  CurrentAccount.FirstName %>
                    $CurrentAccount.FirstName
                <% else_if  DupeAccount.FirstName %>
                    $DupeAccount.FirstName
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Last Name</td>
            <td>
                <% with CurrentAccount %>
                    <% if Surname %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="surname" name="surname" data-member-id="{$ID}" value="$Surname" checked>$Surname
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if Surname %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="surname" name="surname" data-member-id="{$ID}" value="$Surname" <% if not Top.CurrentAccount.Surname %>checked<% end_if%>>$Surname
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.Surname && DupeAccount.Surname %>
                    <div class="checkbox surname_div" id="surname_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Surname</span>
                    </div>
                    <div class="checkbox hidden surname_div" id="surname_{$DupeAccount.ID}">
                        <span>$DupeAccount.Surname</span>
                    </div>
                <% else_if  CurrentAccount.Surname %>
                    $CurrentAccount.Surname
                <% else_if  DupeAccount.Surname %>
                    $DupeAccount.Surname
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Email</td>
            <td>
                <% with CurrentAccount %>
                    <% if Email %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="email" name="email" data-member-id="{$ID}" value="$Email" checked>$Email
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if Email %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="email" name="email" data-member-id="{$ID}" value="$Email" <% if not Top.CurrentAccount.Email %>checked<% end_if%>>$Email
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.Email && DupeAccount.Email %>
                    <div class="checkbox email_div" id="email_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Email</span>
                    </div>
                    <div class="checkbox hidden email_div" id="email_{$DupeAccount.ID}">
                        <span>$DupeAccount.Email</span>
                    </div>
                <% else_if  CurrentAccount.Email %>
                    $CurrentAccount.Email
                <% else_if  DupeAccount.Email %>
                    $DupeAccount.Email
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Second Email</td>
            <td>
                <% with CurrentAccount %>
                    <% if SecondEmail %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="second_email" name="second_email" data-member-id="{$ID}" value="$SecondEmail" checked>$SecondEmail
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if SecondEmail %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="second_email" name="second_email" data-member-id="{$ID}" value="$SecondEmail" <% if not Top.CurrentAccount.SecondEmail %>checked<% end_if%>>$SecondEmail
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.SecondEmail && DupeAccount.SecondEmail %>
                    <div class="checkbox second_email_div" id="second_email_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.SecondEmail</span>
                    </div>
                    <div class="checkbox hidden second_email_div" id="second_email_{$DupeAccount.ID}">
                        <span>$DupeAccount.SecondEmail</span>
                    </div>
                <% else_if  CurrentAccount.SecondEmail %>
                    $CurrentAccount.SecondEmail
                <% else_if  DupeAccount.SecondEmail %>
                    $DupeAccount.SecondEmail
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Third Email</td>
            <td>
                <% with CurrentAccount %>
                    <% if ThirdEmail %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="third_email" name="third_email" data-member-id="{$ID}" value="$ThirdEmail" checked>$ThirdEmail
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if ThirdEmail %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="third_email" name="third_email" data-member-id="{$ID}" value="$ThirdEmail" <% if not Top.CurrentAccount.ThirdEmail %>checked<% end_if%>>$ThirdEmail
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.ThirdEmail && DupeAccount.ThirdEmail %>
                    <div class="checkbox third_email_div" id="third_email_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.ThirdEmail</span>
                    </div>
                    <div class="checkbox hidden third_email_div" id="third_email_{$DupeAccount.ID}">
                        <span>$DupeAccount.ThirdEmail</span>
                    </div>
                <% else_if  CurrentAccount.ThirdEmail %>
                    $CurrentAccount.ThirdEmail
                <% else_if  DupeAccount.ThirdEmail %>
                    $DupeAccount.ThirdEmail
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Shirt Size</td>
            <td>
                <% with CurrentAccount %>
                    <% if ShirtSize %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="shirt_size" name="shirt_size" data-member-id="{$ID}" value="$ShirtSize" checked>$ShirtSize
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if ShirtSize %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="shirt_size" name="shirt_size" data-member-id="{$ID}" value="$ShirtSize" <% if not Top.CurrentAccount.ShirtSize %>checked<% end_if%>>$ShirtSize
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.ShirtSize && DupeAccount.ShirtSize %>
                    <div class="checkbox shirt_size_div" id="shirt_size_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.ShirtSize</span>
                    </div>
                    <div class="checkbox hidden shirt_size_div" id="shirt_size_{$DupeAccount.ID}">
                        <span>$DupeAccount.ShirtSize</span>
                    </div>
                <% else_if  CurrentAccount.ShirtSize %>
                    $CurrentAccount.ShirtSize
                <% else_if  DupeAccount.ShirtSize %>
                    $DupeAccount.ShirtSize
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Statement Of Interest</td>
            <td>
                <% with CurrentAccount %>
                    <% if StatementOfInterest %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="statement_interest" name="statement_interest" data-member-id="{$ID}" value="$StatementOfInterest" checked>$StatementOfInterest
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if StatementOfInterest %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="statement_interest" name="statement_interest" data-member-id="{$ID}" value="$StatementOfInterest" <% if not Top.CurrentAccount.StatementOfInterest %>checked<% end_if%>>$StatementOfInterest
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.StatementOfInterest && DupeAccount.StatementOfInterest %>
                    <div class="checkbox statement_interest_div" id="statement_interest_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.StatementOfInterest</span>
                    </div>
                    <div class="checkbox hidden statement_interest_div" id="statement_interest_{$DupeAccount.ID}">
                        <span>$DupeAccount.StatementOfInterest</span>
                    </div>
                <% else_if  CurrentAccount.StatementOfInterest %>
                    $CurrentAccount.StatementOfInterest
                <% else_if  DupeAccount.StatementOfInterest %>
                    $DupeAccount.StatementOfInterest
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
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
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if Bio %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="bio" name="bio" data-member-id="{$ID}" value="$Bio" <% if not Top.CurrentAccount.Bio %>checked<% end_if%>>$Bio
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.Bio && DupeAccount.Bio %>
                    <div class="checkbox bio_div" id="bio_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Bio</span>
                    </div>
                    <div class="checkbox hidden bio_div" id="bio_{$DupeAccount.ID}">
                        <span>$DupeAccount.Bio</span>
                    </div>
                <% else_if  CurrentAccount.Bio %>
                    $CurrentAccount.Bio
                <% else_if  DupeAccount.Bio %>
                    $DupeAccount.Bio
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Food Preference</td>
            <td>
                <% with CurrentAccount %>
                    <% if FoodPreference %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="food_preference" name="food_preference" data-member-id="{$ID}" value="$FoodPreference" checked>$FoodPreference
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if FoodPreference %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="food_preference" name="food_preference" data-member-id="{$ID}" value="$FoodPreference" <% if not Top.CurrentAccount.FoodPreference %>checked<% end_if%>>$FoodPreference
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.FoodPreference && DupeAccount.FoodPreference %>
                    <div class="checkbox food_preference_div" id="food_preference_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.FoodPreference</span>
                    </div>
                    <div class="checkbox hidden food_preference_div" id="food_preference_{$DupeAccount.ID}">
                        <span>$DupeAccount.FoodPreference</span>
                    </div>
                <% else_if  CurrentAccount.FoodPreference %>
                    $CurrentAccount.FoodPreference
                <% else_if  DupeAccount.FoodPreference %>
                    $DupeAccount.FoodPreference
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Other Food</td>
            <td>
                <% with CurrentAccount %>
                    <% if OtherFood %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="other_food" name="other_food" data-member-id="{$ID}" value="$OtherFood" checked>$OtherFood
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if OtherFood %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="other_food" name="other_food" data-member-id="{$ID}" value="$OtherFood" <% if not Top.CurrentAccount.OtherFood %>checked<% end_if%>>$OtherFood
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.OtherFood && DupeAccount.OtherFood %>
                    <div class="checkbox other_food_div" id="other_food_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.OtherFood</span>
                    </div>
                    <div class="checkbox hidden other_food_div" id="other_food_{$DupeAccount.ID}">
                        <span>$DupeAccount.OtherFood</span>
                    </div>
                <% else_if  CurrentAccount.OtherFood %>
                    $CurrentAccount.OtherFood
                <% else_if  DupeAccount.OtherFood %>
                    $DupeAccount.OtherFood
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>IRC Handle</td>
            <td>
                <% with CurrentAccount %>
                    <% if IRCHandle %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="irc_handle" name="irc_handle" data-member-id="{$ID}" value="$IRCHandle" checked>$IRCHandle
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if IRCHandle %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="irc_handle" name="irc_handle" data-member-id="{$ID}" value="$IRCHandle" <% if not Top.CurrentAccount.IRCHandle %>checked<% end_if%>>$IRCHandle
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.IRCHandle && DupeAccount.IRCHandle %>
                    <div class="checkbox irc_handle_div" id="irc_handle_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.IRCHandle</span>
                    </div>
                    <div class="checkbox hidden irc_handle_div" id="irc_handle_{$DupeAccount.ID}">
                        <span>$DupeAccount.IRCHandle</span>
                    </div>
                <% else_if  CurrentAccount.IRCHandle %>
                    $CurrentAccount.IRCHandle
                <% else_if  DupeAccount.IRCHandle %>
                    $DupeAccount.IRCHandle
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Twitter Name</td>
            <td>
                <% with CurrentAccount %>
                    <% if TwitterName %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="twitter_name" name="twitter_name" data-member-id="{$ID}" value="$TwitterName" checked>$TwitterName
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if TwitterName %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="twitter_name" name="twitter_name" data-member-id="{$ID}" value="$TwitterName" <% if not Top.CurrentAccount.TwitterName %>checked<% end_if%>>$TwitterName
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.TwitterName && DupeAccount.TwitterName %>
                    <div class="checkbox twitter_name_div" id="twitter_name_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.TwitterName</span>
                    </div>
                    <div class="checkbox hidden twitter_name_div" id="twitter_name_{$DupeAccount.ID}">
                        <span>$DupeAccount.TwitterName</span>
                    </div>
                <% else_if  CurrentAccount.TwitterName %>
                    $CurrentAccount.TwitterName
                <% else_if  DupeAccount.TwitterName %>
                    $DupeAccount.TwitterName
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>LinkedIn Profile</td>
            <td>
                <% with CurrentAccount %>
                    <% if LinkedInProfile %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="linkedin_profile" name="linkedin_profile" data-member-id="{$ID}" value="$LinkedInProfile" checked>$LinkedInProfile
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if LinkedInProfile %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="linkedin_profile" name="linkedin_profile" data-member-id="{$ID}" value="$LinkedInProfile" <% if not Top.CurrentAccount.LinkedInProfile %>checked<% end_if%>>$LinkedInProfile
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.LinkedInProfile && DupeAccount.LinkedInProfile %>
                    <div class="checkbox linkedin_profile_div" id="linkedin_profile_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.LinkedInProfile</span>
                    </div>
                    <div class="checkbox hidden linkedin_profile_div" id="linkedin_profile_{$DupeAccount.ID}">
                        <span>$DupeAccount.LinkedInProfile</span>
                    </div>
                <% else_if  CurrentAccount.LinkedInProfile %>
                    <div class="checkbox linkedin_profile_div" id="linkedin_profile_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.LinkedInProfile</span>
                    </div>
                <% else_if  DupeAccount.LinkedInProfile %>
                    <div class="checkbox linkedin_profile_div" id="linkedin_profile_{$DupeAccount.ID}">
                        <span>$DupeAccount.LinkedInProfile</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Projects</td>
            <td>
                <% with CurrentAccount %>
                    <% if Projects %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="projects" name="projects" data-member-id="{$ID}" value="$Projects" checked>$Projects
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if Projects %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="projects" name="projects" data-member-id="{$ID}" value="$Projects" <% if not Top.CurrentAccount.Projects %>checked<% end_if%>>$Projects
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.Projects && DupeAccount.Projects %>
                    <div class="checkbox projects_div" id="projects_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Projects</span>
                    </div>
                    <div class="checkbox hidden projects_div" id="projects_{$DupeAccount.ID}">
                        <span>$DupeAccount.Projects</span>
                    </div>
                <% else_if  CurrentAccount.Projects %>
                    <div class="checkbox projects_div" id="projects_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.ShirtSize</span>
                    </div>
                <% else_if  DupeAccount.Projects %>
                    <div class="checkbox projects_div" id="projects_{$DupeAccount.ID}">
                        <span>$DupeAccount.Projects</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Other Project</td>
            <td>
                <% with CurrentAccount %>
                    <% if OtherProject %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="other_project" name="other_project" data-member-id="{$ID}" value="$OtherProject" checked>$OtherProject
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if OtherProject %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="other_project" name="other_project" data-member-id="{$ID}" value="$OtherProject" <% if not Top.CurrentAccount.OtherProject %>checked<% end_if%>>$OtherProject
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.OtherProject && DupeAccount.OtherProject %>
                    <div class="checkbox other_project_div" id="other_project_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.OtherProject</span>
                    </div>
                    <div class="checkbox hidden other_project_div" id="other_project_{$DupeAccount.ID}">
                        <span>$DupeAccount.OtherProject</span>
                    </div>
                <% else_if  CurrentAccount.OtherProject %>
                    <div class="checkbox other_project_div" id="other_project_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.OtherProject</span>
                    </div>
                <% else_if  DupeAccount.OtherProject %>
                    <div class="checkbox other_project_div" id="other_project_{$DupeAccount.ID}">
                        <span>$DupeAccount.OtherProject</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Address</td>
            <td>
                <% with CurrentAccount %>
                    <% if Address %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="address" name="address" data-member-id="{$ID}" value="$Address" checked>$Address
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if Address %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="address" name="address" data-member-id="{$ID}" value="$ShirtSize" <% if not Top.CurrentAccount.Address %>checked<% end_if%>>$Address
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.Address && DupeAccount.Address %>
                    <div class="checkbox address_div" id="address_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Address</span>
                    </div>
                    <div class="checkbox hidden address_div" id="address_{$DupeAccount.ID}">
                        <span>$DupeAccount.Address</span>
                    </div>
                <% else_if  CurrentAccount.Address %>
                    <div class="checkbox address_div" id="address_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Address</span>
                    </div>
                <% else_if  DupeAccount.Address %>
                    <div class="checkbox address_div" id="address_{$DupeAccount.ID}">
                        <span>$DupeAccount.Address</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Suburb</td>
            <td>
                <% with CurrentAccount %>
                    <% if Suburb %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="suburb" name="suburb" data-member-id="{$ID}" value="$Suburb" checked>$Suburb
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if Suburb %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="suburb" name="suburb" data-member-id="{$ID}" value="$Suburb" <% if not Top.CurrentAccount.Suburb %>checked<% end_if%>>$Suburb
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.Suburb && DupeAccount.Suburb %>
                    <div class="checkbox suburb_div" id="suburb_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Suburb</span>
                    </div>
                    <div class="checkbox hidden suburb_div" id="suburb_{$DupeAccount.ID}">
                        <span>$DupeAccount.Suburb</span>
                    </div>
                <% else_if  CurrentAccount.Suburb %>
                    <div class="checkbox suburb_div" id="suburb_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Suburb</span>
                    </div>
                <% else_if  DupeAccount.Suburb %>
                    <div class="checkbox suburb_div" id="suburb_{$DupeAccount.ID}">
                        <span>$DupeAccount.Suburb</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>State</td>
            <td>
                <% with CurrentAccount %>
                    <% if State %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="state" name="state" data-member-id="{$ID}" value="$State" checked>$State
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if State %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="state" name="state" data-member-id="{$ID}" value="$State" <% if not Top.CurrentAccount.State %>checked<% end_if%>>$State
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.State && DupeAccount.State %>
                    <div class="checkbox state_div" id="state_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.State</span>
                    </div>
                    <div class="checkbox hidden state_div" id="state_{$DupeAccount.ID}">
                        <span>$DupeAccount.State</span>
                    </div>
                <% else_if  CurrentAccount.State %>
                    <div class="checkbox state_div" id="state_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.State</span>
                    </div>
                <% else_if  DupeAccount.State %>
                    <div class="checkbox state_div" id="state_{$DupeAccount.ID}">
                        <span>$DupeAccount.State</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Postcode</td>
            <td>
                <% with CurrentAccount %>
                    <% if Postcode %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="postcode" name="postcode" data-member-id="{$ID}" value="$Postcode" checked>$Postcode
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if Postcode %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="postcode" name="postcode" data-member-id="{$ID}" value="$Postcode" <% if not Top.CurrentAccount.Postcode %>checked<% end_if%>>$Postcode
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.Postcode && DupeAccount.Postcode %>
                    <div class="checkbox postcode_div" id="postcode_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Postcode</span>
                    </div>
                    <div class="checkbox hidden postcode_div" id="postcode_{$DupeAccount.ID}">
                        <span>$DupeAccount.Postcode</span>
                    </div>
                <% else_if  CurrentAccount.Postcode %>
                    <div class="checkbox postcode_div" id="postcode_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Postcode</span>
                    </div>
                <% else_if  DupeAccount.Postcode %>
                    <div class="checkbox postcode_div" id="postcode_{$DupeAccount.ID}">
                        <span>$DupeAccount.Postcode</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Country</td>
            <td>
                <% with CurrentAccount %>
                    <% if Country %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="country" name="country" data-member-id="{$ID}" value="$Country" checked>$Country
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if Country %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="country" name="country" data-member-id="{$ID}" value="$Country" <% if not Top.CurrentAccount.Country %>checked<% end_if%>>$Country
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.Country && DupeAccount.Country %>
                    <div class="checkbox country_div" id="country_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Country</span>
                    </div>
                    <div class="checkbox hidden country_div" id="country_{$DupeAccount.ID}">
                        <span>$DupeAccount.Country</span>
                    </div>
                <% else_if  CurrentAccount.Country %>
                    <div class="checkbox country_div" id="country_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Country</span>
                    </div>
                <% else_if  DupeAccount.Country %>
                    <div class="checkbox country_div" id="country_{$DupeAccount.ID}">
                        <span>$DupeAccount.Country</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
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
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if City %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="city" name="city" data-member-id="{$ID}" value="$City" <% if not Top.CurrentAccount.City %>checked<% end_if%>>$City
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.City && DupeAccount.City %>
                    <div class="checkbox city_div" id="city_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.City</span>
                    </div>
                    <div class="checkbox hidden city_div" id="city_{$DupeAccount.ID}">
                        <span>$DupeAccount.City</span>
                    </div>
                <% else_if  CurrentAccount.City %>
                    <div class="checkbox city_div" id="city_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.City</span>
                    </div>
                <% else_if  DupeAccount.City %>
                    <div class="checkbox city_div" id="city_{$DupeAccount.ID}">
                        <span>$DupeAccount.City</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Gender</td>
            <td>
                <% with CurrentAccount %>
                    <% if Gender %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="gender" name="gender" data-member-id="{$ID}" value="$Gender" checked>$Gender
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if Gender %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="gender" name="gender" data-member-id="{$ID}" value="$Gender" <% if not Top.CurrentAccount.Gender %>checked<% end_if%>>$Gender
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.Gender && DupeAccount.Gender %>
                    <div class="checkbox gender_div" id="gender_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Gender</span>
                    </div>
                    <div class="checkbox hidden gender_div" id="gender_{$DupeAccount.ID}">
                        <span>$DupeAccount.Gender</span>
                    </div>
                <% else_if  CurrentAccount.Gender %>
                    <div class="checkbox gender_div" id="gender_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.Gender</span>
                    </div>
                <% else_if  DupeAccount.Gender %>
                    <div class="checkbox gender_div" id="gender_{$DupeAccount.ID}">
                        <span>$DupeAccount.Gender</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Photo</td>
            <td>
                <% with CurrentAccount %>
                    <% if Photo.exists %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="photo" name="photo" data-member-id="{$ID}" value="$Photo.ID" checked>$ProfilePhoto
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if Photo.exists %>
                        <div class="checkbox">
                            <label>
                                <input type="radio" class="photo" name="photo" data-member-id="{$ID}" value="$Photo.ID" <% if not Top.CurrentAccount.Photo.exists %>checked<% end_if%>>$ProfilePhoto
                            </label>
                        </div>
                    <% else %>
                        NOT SET
                    <% end_if %> <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.Photo.exists && DupeAccount.Photo.exists %>
                    <div class="checkbox photo_div" id="photo_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.ProfilePhoto</span>
                    </div>
                    <div class="checkbox hidden photo_div" id="photo_{$DupeAccount.ID}">
                        <span>$DupeAccount.ProfilePhoto</span>
                    </div>
                <% else_if  CurrentAccount.Photo.exists %>
                    <div class="checkbox photo_div" id="photo_{$CurrentAccount.ID}">
                        <span>$CurrentAccount.ProfilePhoto</span>
                    </div>
                <% else_if  DupeAccount.Photo.exists %>
                    <div class="checkbox photo_div" id="photo_{$DupeAccount.ID}">
                        <span>$DupeAccount.ProfilePhoto</span>
                    </div>
                <% else %>
                    NOT SET
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Is Candidate&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></td>
            <td>
                <% with CurrentAccount %>
                    <% if isCanditate %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if isCanditate %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.isCanditate && DupeAccount.isCanditate %>
                    Yes
                <% else_if  CurrentAccount.isCanditate %>
                   Yes
                <% else_if  DupeAccount.isCanditate %>
                    Yes
                <% else %>
                    No
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Is Speaker&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></td>
            <td>
                <% with CurrentAccount %>
                    <% if isSpeaker %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if isSpeaker %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.isSpeaker && DupeAccount.isSpeaker %>
                    Yes
                <% else_if  CurrentAccount.isSpeaker %>
                    Yes
                <% else_if  DupeAccount.isSpeaker %>
                    Yes
                <% else %>
                    No
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Is MarketPlace Admin&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></td>
            <td>
                <% with CurrentAccount %>
                    <% if isMarketPlaceAdmin %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if isMarketPlaceAdmin %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.isMarketPlaceAdmin && DupeAccount.isMarketPlaceAdmin %>
                    Yes
                <% else_if  CurrentAccount.isMarketPlaceAdmin %>
                    Yes
                <% else_if  DupeAccount.isMarketPlaceAdmin %>
                    Yes
                <% else %>
                    No
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Is Company Admin&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></td>
            <td>
                <% with CurrentAccount %>
                    <% if isCompanyAdmin %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if isCompanyAdmin %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.isCompanyAdmin && DupeAccount.isCompanyAdmin %>
                    Yes
                <% else_if  CurrentAccount.isCompanyAdmin %>
                    Yes
                <% else_if  DupeAccount.isCompanyAdmin %>
                    Yes
                <% else %>
                    No
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Is Training Admin&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></td>
            <td>
                <% with CurrentAccount %>
                    <% if isTrainingAdmin %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if isTrainingAdmin %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.isTrainingAdmin && DupeAccount.isTrainingAdmin %>
                    Yes
                <% else_if  CurrentAccount.isTrainingAdmin %>
                    Yes
                <% else_if  DupeAccount.isTrainingAdmin %>
                    Yes
                <% else %>
                    No
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Has Deployment Surveys&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></td>
            <td>
                <% with CurrentAccount %>
                    <% if hasDeploymentSurveys %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if hasDeploymentSurveys %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.hasDeploymentSurveys && DupeAccount.hasDeploymentSurveys %>
                    Yes
                <% else_if  CurrentAccount.hasDeploymentSurveys %>
                    Yes
                <% else_if  DupeAccount.hasDeploymentSurveys %>
                    Yes
                <% else %>
                    No
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td>Has App Dev Surveys&nbsp;<span style="cursor: pointer" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></td>
            <td>
                <% with CurrentAccount %>
                    <% if hasAppDevSurveys %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% with DupeAccount %>
                    <% if hasAppDevSurveys %>
                        Yes
                    <% else %>
                        No
                    <% end_if %>
                <% end_with %>
            </td>
            <td>
                <% if CurrentAccount.hasAppDevSurveys && DupeAccount.hasAppDevSurveys %>
                    Yes
                <% else_if  CurrentAccount.hasAppDevSurveys %>
                    Yes
                <% else_if  DupeAccount.hasAppDevSurveys %>
                    Yes
                <% else %>
                    No
                <% end_if %>
            </td>
        </tr>
        </tbody>
    </table>
    <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center" class="roundedButton merge" href="#" id="merge2">Merge Account</a>
</div>