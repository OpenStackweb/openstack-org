var Cortex = require('cortexjs');

var Backend = new Cortex({
	summit: {
		categories: null
	},
	presentations: null,
	presentationPagination: {
		hasMore: false,
		total: 0,
		remaining: 0
	},
	activePresentation: null
});

module.exports = Backend;