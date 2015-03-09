<a href="$BackUrl">Back</a>&nbsp;|&nbsp;<a href="javascript:window.print()">Print This Page</a>
<% with Survey %>
<h1>Survey # {$ID}</h1>
<hr>
<b>Created:</b>&nbsp;$Created<br>
<b>Last Updated:</b>&nbsp;$UpdateDate<br>
<br>
<h2>About You</h2>
<hr>
<b>Member:</b>&nbsp;$Member.FirstName, $Member.Surname<br>
<b>Email:</b>&nbsp;<a href="mailto:$Member.Email">$Member.Email</a><br>
<b>Which of the following do you yourself personally do?</b>&nbsp;$OpenStackActivity<br>
<b>Your relationship with OpenStack</b>&nbsp;$OpenStackRelationship<br>
<b>Is Ok To Contact?:</b>&nbsp;<% if OkToContact %>Yes<% else %> No <% end_if %><br><br>
<h2>Your Organization</h2>
<hr>
<b>Organization</b>&nbsp;$Org.Name<br>
<b>Your Organization’s Primary Industry</b>&nbsp;$Industry $OtherIndustry<br>
<% if Industry == 'Information Technology' %>
<b>Your Organization’s Primary IT Activity</b>&nbsp;$ITActivity<br>
<% end_if %>
<b>Your Organization’s Primary Location or Headquarters</b>&nbsp;$PrimaryCountry<br>
<b>State / Province / Region</b>&nbsp;$PrimaryState<br>
<b>City</b>&nbsp;$PrimaryCity<br>
<b>Your Organization Size (All Branches, Locations, Sites)</b>&nbsp;$OrgSize<br>
<b>What best describes your Organization’s involvement with OpenStack?</b>&nbsp;$OpenStackInvolvement<br><br>
<h2>Your Thoughts</h2>
<hr>
<b>What are your top business drivers for using OpenStack?<BR>Please rank up to 5.<BR>1 = top business driver, 2 = next, 3 = third, and so on</b>&nbsp;$BusinessDrivers $OtherBusinessDrivers<br>
<b>Where do end up finding information about using OpenStack, after using search engines and talking to your colleagues?</b>&nbsp;$InformationSources $OtherInformationSources<br>
<b>How likely are you to recommend OpenStack to a friend or colleague? (0=Least Likely, 10=Most Likely)</b>&nbsp;$OpenStackRecommendRate<br>
<b>What do you like most about OpenStack, besides “free” and “open”?</b>&nbsp;$WhatDoYouLikeMost<br>
<b>What areas of OpenStack require further enhancement?</b>&nbsp;$FurtherEnhancement<br>
<b>What should be the priorities for the Foundation and User Committee during the coming year?</b>&nbsp;$FoundationUserCommitteePriorities<br><br>
<b>Are you interested in using container technology with OpenStack?</b>&nbsp;<% if InterestedUsingContainerTechnology %>Yes<% else %>No<% end_if %><br>
<b>Which of the following container related technologies are you interested in using?</b>&nbsp;$ContainerRelatedTechnologies<br><br>
<% if hasAppDevSurveys %>
<h2>Application Development</h2>
<hr>
<% loop AppDevSurveys %>
<b>What toolkits do you use or plan to use to interact with the OpenStack API?</b>&nbsp;$Toolkits $OtherToolkits<br>
<b>If you wrote your own code for interacting with the OpenStack API, what programming language did you write it in?</b>&nbsp;$ProgrammingLanguages $OtherProgrammingLanguages<br>
<b>If you wrote your own code for interacting with the OpenStack API, what wire format are you using?</b>&nbsp;$APIFormats $OtherAPIFormats<br>
<b>What operating systems are you using or plan on using to develop your applications?</b>&nbsp;$OperatingSystems $OtherOperatingSystems<br>
<b>What guest operating systems are you using or plan on using to deploy your applications to customers?</b>&nbsp;$GuestOperatingSystems $OtherGuestOperatingSystems<br>
<b>What do you struggle with when developing or deploying applications on OpenStack?</b>&nbsp;$StruggleDevelopmentDeploying<br>
<b>What is your top priority in evaluating API and SDK docs?</b>&nbsp;$DocsPriority<br>
<% end_loop %>
<% end_if %>
<br>
<br>
<b>Is User Group Member?:</b>&nbsp;<% if UserGroupMember %>True<% else %> False <% end_if %> <br>
<% if UserGroupMember %>
<b>User Group Name:</b>&nbsp;$UserGroupName<br>
<% end_if %>

<% if Deployments %>
<h3>Deployments</h3>
<hr>
<ul>
<% loop Deployments %>
    <li>
        <a href="$Top.Link(DeploymentDetails)/{$ID}?BackUrl={$Top.CurrentUrl}" title="click to see deployment details">$Label</a>
    </li>
<% end_loop %>
</ul>
<% end_if %>

<% end_with %>