let _data = {};

const Config = {

	load: function (data) {
		_data = data;
	},

	get: function (k) {
		return _data[k];
	},

	set: function (k, v) {
		_data[k] = v;
	}
};

export default Config;