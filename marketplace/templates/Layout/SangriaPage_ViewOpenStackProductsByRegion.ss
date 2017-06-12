<div id="openstack-products-by-region-app"></div>
<script>
var pageSize         = 25;
var regions = [
    <% loop Regions %>
        {
            name: '{$Name}'
        },
    <% end_loop %>
];
</script>

$ModuleJS("sangria-marketplace-openstack-products-by-region", false , "marketplace")