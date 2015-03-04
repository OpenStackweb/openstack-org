<a href="$Top.Link">Back</a>&nbsp;|&nbsp;<a href="javascript:window.print()">Print This Page</a>
<script type="application/javascript">
    var countries_with_commits = [];
    var countries_names = [];
    var data_countries_with_commits = [];
    var data_users_with_commits = [];
        $LoadJsonCountriesCoordinates
</script>
<h1>Gerrit Statistics</h1>
$DateFilters
<h2>Total # of commits</h2>
$TotalCommits
<h2>Total # of users With commits</h2>
$UsersWithCommits
<h2>Total commits per country</h2>
<button type="button" id="collapse_coutries" class="btn btn-info" data-toggle="collapse"
        data-target="#commits_per_country"><span class="glyphicon glyphicon-chevron-up"></span></button>
<div id="commits_per_country" class="container-fluid collapse in">
    <div class="row">
        <div class="col-lg-4">
            <% loop CommitsPerCountry %>
                <script type="application/javascript">
                    var country = {
                        code: "{$Country}",
                        name: "{$CountryName}",
                        commits: {$Commits},
                        color: Math.floor(Math.random() * 16777215).toString(16)
                    };
                    countries_with_commits["{$Country}"] = country;
                    countries_names['{$CountryName}'] = '{$Country}';
                    data_countries_with_commits.push({
                        value: country.commits,
                        color: '#' + country.color,
                        highlight: '#' + country.color,
                        label: country.name
                    });
                </script>

            <% end_loop %>
            <canvas id="myChart" width="350" height="400"></canvas>
            <div id="legend"></div>
        </div>
        <div class="col-lg-8">
            <div style="width:100%; height: 650px; position: relative;" id="map" tabindex="0">
            </div>
        </div>
    </div>
</div>
<h2>Total commits per User</h2>
<button type="button" id="collapse_users" class="btn btn-info" data-toggle="collapse"
        data-target="#commits_per_user"><span class="glyphicon glyphicon-chevron-up"></span></button>
<div id="commits_per_user" class="container-fluid collapse in">
    <div class="row">
        <div class="col-lg-8">
            <% loop CommitsPerUser %>
                <script type="application/javascript">
                    var user_color = Math.floor(Math.random() * 16777215).toString(16);
                    data_users_with_commits.push({
                        value: {$Commits},
                        color: '#' + user_color,
                        highlight: '#' + user_color,
                        label: '{$Email}'
                    });
                </script>
            <% end_loop %>
            <canvas id="myChart2" width="850" height="550"></canvas>
        </div>
        <div class="col-lg-4">
            <div id="legend2"></div>
        </div>
    </div>
</div>