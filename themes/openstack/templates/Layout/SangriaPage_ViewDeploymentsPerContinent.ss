<% include SangriaPage_SurveyRangeSelector Label='Select version of deployment', FormAction=$Top.GetLinkForDeploymentsPerContinent($continent), FromPage=ViewDeploymentsPerRegion %>
<script type="application/javascript">
        $LoadJsonCountriesCoordinates
    var countries_with_deployment = [];
</script>
<link rel="stylesheet" href="/themes/openstack/css/bootstrap.min.css" type="text/css" media="screen, projection">
<a href="{$Link(ViewDeploymentsPerRegion)}">Back</a>
<h2> Deployments in $continent_name </h2>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <% if CountriesWithDeployments($continent) %>
            <% loop CountriesWithDeployments($continent) %>

                <h3><a href="#" class="country_link" data-country="{$country}">$country_name ($count)</a></h3>
                <ul style="list-style: none">
                    <% loop $Top.DeploymentsPerCountry($country) %>
                        <li>

                            <script type="application/javascript">
                                if(!countries_with_deployment.hasOwnProperty("{$country}") )
                                    countries_with_deployment["{$country}"] = new Array();
                                var deployments = countries_with_deployment["{$country}"];
                                deployments.push({code:"{$country}" , name : "{$Label} - {$DeploymentType}", url: "{$Top.Link(DeploymentDetails)}/{$ID}?BackUrl={$Top.Link(ViewDeploymentsPerRegion)}%3Fcontinent%3D{$Top.continent}" });
                            </script>
                            <a href="{$Top.Link(DeploymentDetails)}/{$ID}?BackUrl={$Top.Link(ViewDeploymentsPerRegion)}%3Fcontinent%3D{$Top.continent}">$Label - $DeploymentType</a>

                        </li>
                    <% end_loop %>
                </ul>

            <% end_loop %>
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