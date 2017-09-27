<div class="grey-bar">
    <div class="container">
        &nbsp;
    </div>
</div>
<div class="container">
    $Content
    <br>
    <div id="marketplace-driver-app"></div>
</div>

<script>
    var projects = [
        <% loop getProjects() %>
            {
                name: '{$Name.JS}'
            },
        <% end_loop %>
    ];

    var releases = [
        <% loop getReleases() %>
            {
                name: '{$Name.JS}'
            },
        <% end_loop %>
    ];

    var vendors = [
        <% loop getVendors() %>
            {
                name: '{$Name.JS}'
            },
        <% end_loop %>
    ];

    var filters = {projects, releases, vendors};
</script>

$ModuleJS("marketplace-driver-page", false , "marketplace")
