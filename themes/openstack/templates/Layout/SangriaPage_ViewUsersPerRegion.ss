<script type="application/javascript">
    $LoadJsonCountriesCoordinates(ViewUsersPerRegion)
    var countries_with_users = [];
</script>
<style>
.export_cb {margin: 0 3px 0 0 !important; vertical-align: top;}
</style>
<link rel="stylesheet" href="/themes/openstack/css/bootstrap.min.css" type="text/css" media="screen, projection">
<h2>Users &mdash; $UsersCount total</h2>
<% if UsersPerContinent %>
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-4">
                <hr>
                <form method="get" name="form-export-members" id="form-export-members" action="$Link(exportUsersPerRegion)">
                <input type="submit" class="btn" value="Export selected" id="submit_export"/>
                <span for="countries[]" class="error" style="margin-left:20px;display:none"></span>
                <br><br>
                <input type="checkbox" name="members[]" value="foundation-members" class="export_cb export_cb_fm" checked />Foundation Members
                &nbsp;&nbsp;
                <input type="checkbox" name="members[]" value="community-members" class="export_cb export_cb_cm" checked />Community Members
                <hr>
                <input type="checkbox" class="export_cb export_cb_all" />Select All
                <ul style="list-style: none">
                    <% loop UsersPerContinent %>
                        <li>
                            <input type="checkbox" class="export_cb export_cb_cont" />
                            <a href="{$Top.Link(ViewUsersPerRegion)}?continent={$continent_id}">$continent ($count)</a>
                            <ul style="list-style: none">
                                <% loop $Top.UsersPerContinentCountry($continent_id) %>
                                    <li>
                                        <script type="application/javascript">
                                            countries_with_users["{$country}"] = {code:"{$country}" , name : "{$country_name}", users: {$count} };
                                        </script>
                                        <input type="checkbox" class="export_cb" value="{$country}" name="countries[]"/>
                                        <a href="{$Top.Link(ViewUsersPerRegion)}?country={$country}">$country_name
                                            ($count)</a>
                                    </li>
                                <% end_loop %>
                            </ul>
                        </li>
                    <% end_loop %>
                </ul>
                </form>
            </div>
            <div class="col-lg-8">
                <div style="width:100%; height: 650px; position: relative;" id="map" tabindex="0">
                </div>
            </div>
        </div>
    </div>
<% end_if %>