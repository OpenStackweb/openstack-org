<% include SangriaPage_SurveyRangeSelector Label='Deployments Subset', FormAction=$Top.GetLinkForDeploymentsPerCountry($country), FromPage=ViewDeploymentsPerRegion %>
<script type="application/javascript">
        $LoadJsonCountriesCoordinates
    var countries_with_deployment = [];
</script>
<link rel="stylesheet" href="/themes/openstack/css/bootstrap.min.css" type="text/css" media="screen, projection">
<a href="{$Link(ViewDeploymentsPerRegion)}?continent={$continent}">Back</a>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <h2><a href="#" class="country_link" data-country="{$country}">Deployments in $country_name ($count)</a></h2>
            <% if DeploymentsPerCountry($country) %>
            <ul style="list-style: none">
                <% loop DeploymentsPerCountry($country) %>
                    <li>
                        <script type="application/javascript">
                            if(!countries_with_deployment.hasOwnProperty("{$country}") )
                                countries_with_deployment["{$country}"] = new Array();
                            var deployments = countries_with_deployment["{$country}"];
                            deployments.push({code:"{$country}" , name : "{$Label} - {$DeploymentType}", url: "{$Top.Link(DeploymentDetails)}/{$ID}?BackUrl={$Top.Link(ViewDeploymentsPerRegion)}%3Fcountry%3D{$Top.country}" });
                        </script>
                        <a href="{$Top.Link(DeploymentDetails)}/{$ID}?BackUrl={$Top.Link(ViewDeploymentsPerRegion)}%3Fcountry%3D{$Top.country}">$Label - $DeploymentType</a>
                    </li>
                <% end_loop %>
            </ul>
            <% else %>
                * There are not Deployments.
            <% end_if %>
        </div>
        <div class="col-lg-8">
            <div style="width:100%; height: 650px; position: relative;" id="map" tabindex="0">
            </div>
        </div>
    </div>
</div>