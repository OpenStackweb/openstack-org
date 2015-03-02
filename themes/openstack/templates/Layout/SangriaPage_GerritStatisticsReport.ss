<a href="$Top.Link">Back</a>&nbsp;|&nbsp;<a href="javascript:window.print()">Print This Page</a>
<h1>Gerrit Statistics</h1>

<h2>Total # of commits per User</h2>
    <ul>
    <% loop CommitsPerUser %>
        <li>$Commits - $Email </li>
    <% end_loop %>
    </ul>

<h2>Total # of commits per country</h2>

<ul>
    <% loop CommitsPerCountry %>
        <li>$Commits - $CountryName </li>
    <% end_loop %>
</ul>
<BR>
<h2>Total # of commits</h2>
$TotalCommits
<BR>
<BR>
<h2>Total # of users With commits</h2>
$UsersWithCommits
<BR>
<BR>