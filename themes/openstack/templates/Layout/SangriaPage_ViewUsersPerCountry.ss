<script type="application/javascript">
    $LoadJsonCountriesCoordinates
    var countries_with_users = [];
</script>
<link rel="stylesheet" href="/themes/openstack/css/bootstrap.min.css" type="text/css" media="screen, projection">
<a href="{$Link(ViewUsersPerRegion)}?continent={$continent}">Back</a>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <h2><a href="#" class="country_link" data-country="{$country}">Users in $country_name ($count)</a></h2>
            <ul style="list-style: none">
                <% loop UserPerCountry($country) %>
                    <li>
                        <script type="application/javascript">
                            if(!countries_with_users.hasOwnProperty("{$Country}") )
                                countries_with_users["{$Country}"] = new Array();
                            var users = countries_with_users["{$Country}"];
                            users.push({code:"{$Country}" , name : "{$FullName} - {$Email}", url: "/community/members/profile/{$ID}?BackUrl={$Top.Link(ViewUsersPerRegion)}%3Fcontinent%3D{$Top.continent}" });
                        </script>
                        <a href="/community/members/profile/{$ID}?BackUrl={$Top.Link(ViewUsersPerRegion)}%3Fcountry%3D{$Top.country}">$FullName - $Email</a>
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