// observable object
require('./openstack-config-samples-types-nav.tag');
require('./openstack-config-samples.tag');
var config_samples_types_nav = riot.mount('openstack-config-samples-types-nav');
riot.mount('openstack-config-samples', { config_samples_types_nav: config_samples_types_nav[0].ctrl });