import './core-services.tag';
import './optional-services.tag';
import './openstack-releases-ddl.tag';
import './openstack-components-free-search.tag';
import './openstack-components-filters.tag';

// observable object
import api from './api';

riot.mount('core-services', { api });
riot.mount('openstack-releases-ddl', { api });
riot.mount('openstack-components-free-search', { api });
riot.mount('optional-services', { api });
riot.mount('openstack-components-filters', { api });
