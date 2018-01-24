<script>
    var pageSize     = 25;
    var reportConfig = {
        title : 'AUC Metrics - List',
        defaultSortField: 'id',
        initialPageSize: 25,
        filterTypes : [
            { value: 'OFFICIAL_USER_GROUP_ORGANIZER',label: 'OFFICIAL_USER_GROUP_ORGANIZER'},
            { value: 'ACTIVE_MEMBER_UC_WORKING_GROUP',label: 'ACTIVE_MEMBER_UC_WORKING_GROUP'},
            { value: 'ACTIVE_MODERATOR_ASK_OPENSTACK',label: 'ACTIVE_MODERATOR_ASK_OPENSTACK'},
            { value:'SUPERUSER_CONTRIBUTOR', label: 'SUPERUSER_CONTRIBUTOR'},
            { value:'OFFICIAL_USER_GROUP_ORGANIZER', label: 'OFFICIAL_USER_GROUP_ORGANIZER'},
            { value:'ACTIVE_MEMBER_UC_WORKING_GROUP', label: 'ACTIVE_MEMBER_UC_WORKING_GROUP'},
        ],
        freeTextSearchPlaceHolder: 'Identifier/Value/Member',
        columns: [
            { shouldSort: true, title: 'Sort by ID', label: 'Id', name: 'id'},
            { shouldSort: true, title: 'Sort by Identifier', label: 'Service Identifier', name: 'identifier'},
            { shouldSort: true, title: 'Sort by Created', label: 'Created', name: 'created'},
            { shouldSort: true, title: 'Sort by Expires', label: 'Expires', name: 'expires'},
            { shouldSort: true, title: 'Sort by Member Id', label: 'Member Id', name: 'member_id'},
            { shouldSort: false, label: 'Member', name: 'member_full_name'},
            { shouldSort: false, label: 'Email', name: 'member_email'},
            { shouldSort: false, label: 'Value', name: 'value'},
            { shouldSort: false, label: 'Value Description', name: 'value_description'},
        ],
        hasCustomSecondaryFilter: false,
        useExport: true,
    };
</script>
<div id="auc-metrics-list-app-container">
</div>
$ModuleJS('metrics-list',  false , "auc-metrics")
$ModuleCSS('metrics-list', false , "auc-metrics")

