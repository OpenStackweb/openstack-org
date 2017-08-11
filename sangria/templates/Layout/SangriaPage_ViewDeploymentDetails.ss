<style>
    .container{
        width:90%;
    }
    .addDeploymentForm{
        background-color:#f3f3f3;
        padding:10px;
        clear:both;
        margin:10px;
        border:1px solid #ccc;
        display:none;
    }
</style>

<div stlye="display:block;clear:both">
    <h1 style="width:50%;float:left;">Public Deployment Details List</h1>
    <a href="#" class="roundedButton addDeploymentBtn" style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:50px;">Add New Deployment</a>
</div>

<div class="addDeploymentForm">
    <form method="POST" action="$Top.Link(AddNewDeployment)">
        <table style="border: 1px solid #ccc; border-collapse:collapse;clear:both;">
            <tr>
                <th style="border: 1px solid #ccc;">Deployment Name</th>
                <th style="border: 1px solid #ccc;">Country</th>
                <th style="border: 1px solid #ccc;">Type</th>
                <th style="border: 1px solid #ccc;">Deployment Survey</th>
                <th style="border: 1px solid #ccc;">Add New Deployment</th>
            </tr>
            <tr>
                <td style="border: 1px solid #ccc; width: 15%">
                    <input type="text" name="label">
                </td>
                <td style="border: 1px solid #ccc; width: 15%">
                    $CountriesDDL
                </td>
                <td style="border: 1px solid #ccc; width: 15%">
                    <select name="type">
                      $Top.DeploymentTypeOptions
                    </select>
                </td>

                <td style="border: 1px solid #ccc; width: 40%">
                    <select name="survey" style="width:100%">
                        <option value="0">[No Survey - No Org]</option>
                        <% loop DeploymentsSurvey %>
                            <option value=$ID>$Org.Name - $Title</option>
                        <% end_loop %>
                    </select>
                </td>
                <td style="border: 1px solid #ccc; width: 15%">
                    <input type="submit" class="roundedButton" value="Add New Deployment"  style="white-space: nowrap;">
                </td>
            </tr>
        </table>
    </form>
</div>

<% include SangriaPage_SurveyRangeSelector Label='Select version of deployment', FormAction=$Top.Link(ViewDeploymentDetails), FromPage=ViewDeploymentDetails %>

<form id="seach_deployments" name="seach_deployments" method="GET" action="$Top.Link(ViewDeploymentDetails)">
    <table id="filter" style="border: 1px solid #ccc; border-collapse:collapse;clear:both;">
        <thead>
        <tr>
            <th style="background-color: white;border: 1px solid #ccc;">Filter Deployments</th>
            <th style="background-color: white;border: 1px solid #ccc;">Date From</th>
            <th style="background-color: white;border: 1px solid #ccc;">Date To</th>
            <th style="background-color: white;border: 1px solid #ccc;">Search</th>
        </tr>
        </thead>
        <tbody>

        <tr>
            <td>
                <input type="text" value="" name="free-text" id="free-text" style="width: 160px;">
            </td>
            <td>
                <input type="date" value="" name="date-from" id="date-from" style="width: 160px;">
            </td>
            <td>
                <input type="date" value="" name="date-to" id="date-to" style="width: 160px;">
            </td>
            <td>
                <input type="submit" style="white-space: nowrap;" value="Search" class="roundedButton">
            </td>
        </tr>

        </tbody>
    </table>
</form>

<% if Deployments %>
    <table style="border: 1px solid #ccc; border-collapse:collapse;clear:both;">
        <tr>
            <th style="border: 1px solid #ccc;">Organization</th>
            <th style="border: 1px solid #ccc;">Deployment Name</th>
            <th style="border: 1px solid #ccc;">Industry</th>
            <th style="border: 1px solid #ccc;">Country</th>
            <th style="border: 1px solid #ccc;">Type</th>
            <th style="border: 1px solid #ccc;"><a href="$Top.Link(ViewDeploymentDetails)?sort=date" title="sort by date">
                Date $getSortIcon(deployments) </a>
            </th>
        </tr>
        <% loop Deployments %>
            <tr>
                <input type="hidden" value="$ID" name="ID">
                <td style="border: 1px solid #ccc;">
                    $Org.Name
                </td>
                <td style="border: 1px solid #ccc;">
                    <a href="$Top.Link(DeploymentDetails)/{$ID}" title="click to see deployment details">$Label</a>
                </td>
                <td style="border: 1px solid #ccc;">
                    <a id="dep{$ID}"></a>
                    $DeploymentSurvey.Industry
                </td>
                <td style="border: 1px solid #ccc;">$getCountry</td>
                <td style="border: 1px solid #ccc;">$DeploymentType</td>
                <td style="border: 1px solid #ccc; width: 15%">$UpdateDate</td>
            </tr>
        <% end_loop %>
    </table>
<% else %>
    <p>There are not any Deployments for your current filter criteria!.º</p>
<% end_if %>