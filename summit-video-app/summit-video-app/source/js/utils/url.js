class URL {

	constructor () {
		this.baseURL = '/'
	}

	setBaseURL(url) {
		this.baseURL = url;
	}

	getBaseURL () {
		return this.baseURL;
	}

	serialise (obj) {
	  var str = [];
	  for(var p in obj)
	    if (obj.hasOwnProperty(p) && obj[p] !== null && obj[p] !== '') {
	      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
	    }
	  return str.join("&");
	}

	create (pathParts, queryParams, windowObj) {
		try {
			if(!windowObj) windowObj = window;
		} catch (e) {
			if(!windowObj) windowObj = {};
		}
		
		let baseURL = this.baseURL;
		let path;

		// array
		if(Array.isArray(pathParts)) {
			path = pathParts.join('/');
		}
		// null
		else if(!pathParts) {
			path = windowObj.location.pathname.replace(new RegExp(`^${baseURL}`), '');		
		}
		// string
		else {
			path = pathParts;
		}

		if(queryParams && typeof queryParams === 'object') {
			const serialised = this.serialise(queryParams);
			if(serialised.length) {
				path += `?${this.serialise(queryParams)}`;	
			}
			
		}

		baseURL = baseURL.replace(/\/$/,'').replace(/^\//, '');
		path = path.replace(/\/$/,'').replace(/^\//, '');
		
		return [baseURL, path].join('/');
	}

	makeRelative (url) {
		const replace = this.baseURL.replace(/^\//,'');
		return url.replace(new RegExp(`^${replace}`), '')
				  .replace(/^\//,'');
	}
}

const SNG = new URL();

export default SNG;
