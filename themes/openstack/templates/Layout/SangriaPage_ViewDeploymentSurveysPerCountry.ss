<script type="application/javascript">
        $LoadJsonCountriesCoordinates('ViewDeploymentSurveysPerRegion')
    var countries_with_deployment = [];
</script>
<link rel="stylesheet" href="/themes/openstack/css/bootstrap.min.css" type="text/css" media="screen, projection">
<a href="{$Link(ViewDeploymentSurveysPerRegion)}?continent={$continent}">Back</a>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <h2><a href="#" class="country_link" data-country="{$country}">Deployment Surveys in $country_name ($count)</a></h2>
            <ul style="list-style: none">
                <% loop DeploymentSurveysPerCountry($country) %>
                    <li>
                        <script type="application/javascript">
                            if(!countries_with_deployment.hasOwnProperty("{$country}") )
                                countries_with_deployment["{$country}"] = new Array();
                            var deployments = countries_with_deployment["{$country}"];
                            deployments.push({code:"{$country}" , name : "{$Label} - {$DeploymentType}", url: "{$Top.Link(DeploymentSurveyDetails)}/{$ID}?BackUrl={$Top.Link(ViewDeploymentSurveysPerRegion)}%3Fcountry%3D{$Top.country}" });
                        </script>
                        <a href="{$Top.Link(DeploymentSurveyDetails)}/{$ID}?BackUrl={$Top.Link(ViewDeploymentSurveysPerRegion)}%3Fcountry%3D{$Top.country}">$Org.Name</a>
                    </li>
                <% end_loop %>
            </ul>
        </div>
        <div class="col-lg-8">
            <div style="width:100%; height: 650px; position: relative;" id="map" tabindex="0">
            </div>
        </div>
    </div>
</div>