<script type="application/javascript">
   $LoadJsonCountriesCoordinates('ViewDeploymentSurveysPerRegion')
   var countries_with_deployment = [];
</script>
<link rel="stylesheet" href="/themes/openstack/css/bootstrap.min.css" type="text/css" media="screen, projection">
<h2>Deployment Surveys Submitted &mdash; $DeploymentSurveyCount total</h2>
<% if DeploymentSurveysPerContinent %>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <ul style="list-style: none">
                    <% loop DeploymentSurveysPerContinent %>
                        <li>
                            <a href="{$Top.Link(ViewDeploymentSurveysPerRegion)}?continent={$continent_id}">$continent ($count
                                )</a>
                            <ul style="list-style: none">
                                <% loop $Top.DeploymentSurveysPerContinentCountry($continent_id) %>
                                    <li>
                                        <script type="application/javascript">
                                            countries_with_deployment["{$country}"] = {code:"{$country}" , name : "{$country_name}", deployments: {$count} };
                                        </script>
                                        <a href="{$Top.Link(ViewDeploymentSurveysPerRegion)}?country={$country}">$country_name
                                            ($count)</a>
                                    </li>
                                <% end_loop %>
                            </ul>
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
<% end_if %>