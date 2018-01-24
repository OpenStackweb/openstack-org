<script>
    var pageSize     = 25;
    var ApiBaseUrl   = '$OpenStackResourceApiBaseUrl';
    var reportConfig = {
        title : 'AUC Metrics - User MissMatches Errors',
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
        freeTextSearchPlaceHolder: 'User Identifier',
        columns: [
            { shouldSort: true, title: 'Sort by ID', label: 'Id', name: 'id'},
            { shouldSort: true, title: 'Sort by Service Identifier', label: 'Service Identifier', name: 'service_identifier'},
            { shouldSort: true, title: 'Sort by User Identifier',label: 'User Identifier', name: 'user_identifier'},
            { shouldSort: false, label: ' ', name: 'action_buttons'},
        ],
        hasCustomSecondaryFilter: false,
        useExport: false,
    };
</script>
<div id="auc-metrics-fix-conflicts-app-container">
</div>
$ModuleJS('missmatch-users',  false , "auc-metrics")
$ModuleCSS('missmatch-users', false , "auc-metrics")

