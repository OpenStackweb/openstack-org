<script type="application/javascript">
    $LoadJsonCountriesCoordinates(ViewUsersPerRegion)
    var countries_with_users = [];
</script>
<link rel="stylesheet" href="/themes/openstack/css/bootstrap.min.css" type="text/css" media="screen, projection">
<a href="{$Link(ViewUsersPerRegion)}">Back</a>
<h2> Users in $continent_name </h2>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <% loop CountriesWithUsers($continent) %>
                <h3><a href="#" class="country_link" data-country="{$country}">$country_name ($count)</a></h3>
                <ul style="list-style: none">
                    <% loop $Top.UserPerCountry($country) %>
                        <li>
                            <script type="application/javascript">
                                if(!countries_with_users.hasOwnProperty("{$Country}") )
                                    countries_with_users["{$Country}"] = new Array();
                                var users = countries_with_users["{$Country}"];
                                users.push({code:"{$Country}" , name : "{$FullName} - {$Email}", url: "/community/members/profile/{$ID}?BackUrl={$Top.Link(ViewUsersPerRegion)}%3Fcontinent%3D{$Top.continent}" });
                            </script>
                            <a href="/community/members/profile/{$ID}?BackUrl={$Top.Link(ViewUsersPerRegion)}%3Fcontinent%3D{$Top.continent}">{$FullName} - {$Email}</a>
                        </li>
                    <% end_loop %>
                </ul>

            <% end_loop %>
        </div>
        <div class="col-lg-8">
            <div style="width:100%; height: 650px; position: relative;" id="map" tabindex="0">
            </div>
        </div>
    </div>
</div>