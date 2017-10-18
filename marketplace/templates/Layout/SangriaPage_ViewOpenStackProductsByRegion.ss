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

var reportConfig = {
    title : 'OpenStack Products By Region',
    defaultSortField: 'name',
    initialPageSize: 25,
    filterTypes : [
        { value: 'DISTRIBUTION',label: 'Distributions'},
        { value: 'APPLIANCE',label: 'Appliance'},
        { value: 'REMOTECLOUD',label: 'Remote Cloud'},
        { value:'PUBLICCLOUD', label: 'Public Cloud'},
        { value:'PRIVATECLOUD', label: 'Private Cloud'},
    ],
    freeTextSearchPlaceHolder: 'Product Name/Company Name',
    columns: [
        { shouldSort: true, title: 'Sort by Name', label: 'Name', name: 'name'},
        { shouldSort: true, title: 'Sort by Type', label: 'Type', name: 'type'},
        { shouldSort: false, label: 'Company', name: 'company'},
        { shouldSort: false, label: 'City', name: 'city'},
        { shouldSort: false, label: 'Country', name: 'country'},
        { shouldSort: false, label: 'Region', name: 'region'},
        { shouldSort: false, label: 'Contacts', name: 'admins'},
        { shouldSort: false, label: 'Notes', name: 'notes'},
    ],
    hasCustomSecondaryFilter: true
};
</script>

$ModuleJS("sangria-marketplace-openstack-products-by-region", false , "marketplace")
$ModuleCSS("sangria-marketplace-openstack-products-by-region", false , "marketplace")