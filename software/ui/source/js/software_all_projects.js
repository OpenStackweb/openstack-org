import './project-services.tag';
import './project-services-with-nav.tag';
import './openstack-releases-ddl.tag';
import './openstack-components-free-search.tag';
import './openstack-components-filters.tag';
import './openstack-category-nav.tag';

// observable object
import api from './api';

riot.mount('project-services', { api });
riot.mount('project-services-with-nav', { api });
riot.mount('openstack-releases-ddl', { api });
riot.mount('openstack-components-free-search', { api });
riot.mount('openstack-components-filters', { api });
riot.mount('openstack-category-nav', { api });
