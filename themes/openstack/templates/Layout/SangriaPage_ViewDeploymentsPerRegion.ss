<script type="application/javascript">
   $LoadJsonCountriesCoordinates
   var countries_with_deployment = [];
</script>
<link rel="stylesheet" href="/themes/openstack/css/bootstrap.min.css" type="text/css" media="screen, projection">
<form id="range_form" action="$Top.Link(ViewDeploymentsPerRegion)" method="POST">
<label for="range">Deployment Date Range</label>
<select id="range">
    <option selected value="OLD">OLD</option>
    <option value="MARCH_2015">MARCH 2015</option>
</select>
    <input type="hidden" id="survey_range" name="survey_range" value="{$Top.getSurveyRange(DeploymentsPerRegion)}" />
</form>
<h2>Deployment Submitted &mdash; $DeploymentCount total</h2>
<% if DeploymentsPerContinent %>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <ul style="list-style: none">
                    <% loop DeploymentsPerContinent %>
                        <li>
                            <a href="{$Top.Link(ViewDeploymentsPerRegion)}?continent={$continent_id}">$continent ($count
                                )</a>
                            <ul style="list-style: none">
                                <% loop $Top.DeploymentsPerContinentCountry($continent_id) %>
                                    <li>
                                        <script type="application/javascript">
                                            countries_with_deployment["{$country}"] = {code:"{$country}" , name : "{$country_name}", deployments: {$count} };
                                        </script>
                                        <a href="{$Top.Link(ViewDeploymentsPerRegion)}?country={$country}">$country_name
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
