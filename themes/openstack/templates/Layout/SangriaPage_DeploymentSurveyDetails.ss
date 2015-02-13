<a href="$BackUrl">Back to list</a>&nbsp;|&nbsp;<a href="javascript:window.print()">Print This Page</a>
<% with Deployment %>
<h1>Deployment Survey # {$ID}</h1>
<hr>
<b>Last Updated:</b>&nbsp;$UpdateDate<br>
<b>Organization:</b>&nbsp;$Org.Name ($OrgSize)<br>
<b>Country:</b>&nbsp;$PrimaryCountry<br>
<b>State:</b>&nbsp;$PrimaryState<br>
<b>City:</b>&nbsp;$PrimaryCity<br>
<b>Industry:</b>&nbsp;$Industry<br>
<% if OtherIndustry %>
<b>Other Industry:</b>&nbsp;$OtherIndustry<br>
<% end_if %>
<b>Business Drivers:</b>&nbsp;$BusinessDrivers<br>
<% if OtherBusinessDrivers %>
<b>Other Business Drivers:</b>&nbsp;$OtherBusinessDrivers<br>
<% end_if %>
<b>Title:</b>&nbsp;$Title<br>
<b>Member:</b>&nbsp;$Member.FirstName $Member.Surname<br>
<b>User Group:</b>&nbsp;$UserGroupName<br>
<b>Involvement:</b>&nbsp;$OpenstackInvolvement<br>
<b>Sources:</b>&nbsp;$InformationSources<br>
<% if OtherInformationSources %>
<b>Other Sources:</b>&nbsp;$OtherInformationSources<br>
<% end_if %>
<b>Further Enhancement:</b>&nbsp;$FurtherEnhancement<br>
<b>Committee Priorities:</b>&nbsp;$FoundationUserCommitteePriorities<br>
<b>Current Step / Highest Step:</b>&nbsp;$CurrentStep / $HighestStepAllowed<br>
<b>Emailed?</b>&nbsp;<% if BeenEmailed %>Yes<% else %>No<% end_if %><br>
<b>Contact Allowed?</b>&nbsp;<% if OkToContact %>Yes<% else %>No<% end_if %><br>
<b>Preferences:</b>&nbsp;$WhatDoYouLikeMost<br>
<% end_with %>

