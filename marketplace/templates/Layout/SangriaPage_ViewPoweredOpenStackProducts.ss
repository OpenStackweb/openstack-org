<div id="openstack-powered-products-app"></div>
<script>
var pageSize         = 25;
var program_versions = [
    <% loop InteropProgramVersions %>
        {
            id: $ID,
            name: '{$Name}'
        },
    <% end_loop %>
];
</script>

$ModuleJS("sangria-marketplace-openstack-powered-products", false , "marketplace")
$ModuleCSS("sangria-marketplace-openstack-powered-products", false , "marketplace")