<div id="openstack-powered-products-app"></div>
<script>
    var pageSize   = 25;
    var reportConfig = {
        title : 'OpenStack Powered Products',
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
            { shouldSort: false, label: 'Required for Compute', name: 'required_for_compute'},
            { shouldSort: false, label: 'Required for Storage', name: 'required_for_storage'},
            { shouldSort: false, label: 'Federated Identity', name: 'federated_identity'},
            { shouldSort: false, label: 'Program Version Compatibility', name: 'program_version_id'},
            { shouldSort: true, title: 'Sort by Expiry Date',label: 'Expiry Date (CDT)', name: 'expiry_date'},
            { shouldSort: false, label: 'Last Edited By', name: 'edited_by'},
            { shouldSort: false, label: ' ', name: 'action_buttons'},
        ],
        hasCustomSecondaryFilter: false
    };

    var programVersions = [
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