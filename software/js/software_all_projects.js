require('./core-services.tag');
require('./optional-services.tag');
require('./openstack-releases-ddl.tag');
require('./openstack-components-free-search.tag');

// observable object
var api = require('./api.js')

riot.mount('core-services', { api: api });
riot.mount('openstack-releases-ddl', { api: api });
riot.mount('openstack-components-free-search', { api: api });
riot.mount('optional-services', { api: api });