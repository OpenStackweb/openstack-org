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
	    if (obj.hasOwnProperty(p) && obj[p] !== null && obj[p] !== '' && obj[p] !== undefined) {
	      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
	    }
	  return str.join("&");
	}

	addQueryParams(path, queryParams) {
		if(queryParams) {
			let serialised;
			if(typeof queryParams === 'object') {
				serialised = this.serialise(queryParams);
			}
			else if(queryParams === true) {
				serialised = window.location.search.substring(1);
			}

			if(serialised) {
				path += `?${serialised}`;
			}

		}

		return path;
	}

    updateQueryParams(path, queryParams) {
        if(queryParams) {
            if(typeof queryParams === 'object') {
                for (var key in queryParams) {
                    var value = queryParams[key];
                    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                    var separator = path.indexOf('?') !== -1 ? "&" : "?";
                    if (path.match(re)) {
                        return path.replace(re, '$1' + key + "=" + value + '$2');
                    }
                    else {
                        return path + separator + key + "=" + value;
                    }
                }
            }
        }

        return path;
    }

	create (pathParts, queryParams, baseURL, windowObj) {
		try {
			if(!windowObj) windowObj = window;
		} catch (e) {
			if(!windowObj) windowObj = {};
		}
		
		baseURL = baseURL || this.baseURL;
		let path;

		// array
		if(Array.isArray(pathParts)) {
			path = pathParts.join('/');
		}
		// null
		else if(pathParts === undefined) {
			path = windowObj.location.pathname.replace(new RegExp(`^${baseURL}`), '');		
		}
		// string
		else {
			path = pathParts;
		}

		path = this.addQueryParams(path, queryParams);
		baseURL = baseURL.replace(/\/$/,'');//.replace(/^\//, '');
		path = path.replace(/\/$/,'').replace(/^\//, '');

		return [baseURL, path].join('/');
	}

    update (pathParts, queryParams, baseURL, windowObj) {
        try {
            if(!windowObj) windowObj = window;
        } catch (e) {
            if(!windowObj) windowObj = {};
        }

        baseURL = baseURL || this.baseURL;
        let path;

        // array
        if(Array.isArray(pathParts)) {
            path = pathParts.join('/');
        }
        // null
        else if(pathParts === undefined) {
            path = windowObj.location.pathname.replace(new RegExp(`^${baseURL}`), '');
        }
        // string
        else {
            path = pathParts;
        }

        path = path + windowObj.location.search;
        path = this.updateQueryParams(path , queryParams);
        baseURL = baseURL.replace(/\/$/,'');//.replace(/^\//, '');
        path = path.replace(/\/$/,'').replace(/^\//, '');

        return [baseURL, path].join('/');
    }

	makeRelative (url) {
		const replace = this.baseURL.replace(/^\//,'');
		return url.replace(/^\//,'')
				  .replace(new RegExp(`^${replace}`), '')
				  .replace(/^\//,'');
	}

	trim(url) {
		return url.replace(/^\//,'').replace(/\/$/,'');
	}
}

const SNG = new URL();

export default SNG;
