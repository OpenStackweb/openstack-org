module.exports = (obj, callback) => {
	Object.keys(obj).forEach(k => {		
		callback(k, obj[k]);
	})
};