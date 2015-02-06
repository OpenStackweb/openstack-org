<script type="application/javascript">
    $LoadJsonCountriesCoordinates(ViewUsersPerRegion)
    var countries_with_users = [];
</script>
<link rel="stylesheet" href="/themes/openstack/css/bootstrap.min.css" type="text/css" media="screen, projection">
<h2>Users &mdash; $UsersCount total</h2>
<% if UsersPerContinent %>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <ul style="list-style: none">
                    <% loop UsersPerContinent %>
                        <li>
                            <a href="{$Top.Link(ViewUsersPerRegion)}?continent={$continent_id}">$continent ($count
                                )</a>
                            <ul style="list-style: none">
                                <% loop $Top.UsersPerContinentCountry($continent_id) %>
                                    <li>
                                        <script type="application/javascript">
                                            countries_with_users["{$country}"] = {code:"{$country}" , name : "{$country_name}", users: {$count} };
                                        </script>
                                        <a href="{$Top.Link(ViewUsersPerRegion)}?country={$country}">$country_name
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