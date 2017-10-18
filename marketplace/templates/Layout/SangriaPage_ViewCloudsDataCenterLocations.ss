<div id="openstack-cloud-datacenter-locations-app"></div>
<script>
    var pageSize   = 25;
    var reportConfig = {
        title : 'OpenStack Clouds DataCenter Locations',
        defaultSortField: 'name',
        initialPageSize: 25,
        filterTypes : [
            { value:'PUBLICCLOUD', label: 'Public Cloud'},
            { value:'PRIVATECLOUD', label: 'Private Cloud'}
        ],
        freeTextSearchPlaceHolder: 'Cloud Name/Company Name',
        columns: [
            { shouldSort: true, title: 'Sort by Cloud Name', label: 'Name', name: 'name'},
            { shouldSort: true, title: 'Sort by Cloud Type', label: 'Type', name: 'type'},
            { shouldSort: false, label: 'Company', name: 'company'},
            { shouldSort: false, label: '# DataCenters', name: 'dc_qty'},
            { shouldSort: false, label: '# Countries', name: 'dc_country_qty'},
            { shouldSort: false, label: 'Locations List', name: 'dc_location_list'},
        ],
        hasCustomSecondaryFilter: false
    }

</script>

$ModuleJS("sangria-marketplace-cloud-datacenter-locations", false , "marketplace")
$ModuleCSS("sangria-marketplace-cloud-datacenter-locations", false , "marketplace")