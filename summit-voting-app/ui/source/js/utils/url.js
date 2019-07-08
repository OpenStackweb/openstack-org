import Config from './Config';

const serialise = (obj) => {
  var str = [];
  for(var p in obj)
    if (obj.hasOwnProperty(p) && obj[p] !== null && obj[p] !== '') {
      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
  return str.join("&");
};

const getQueryStringParams = query => {
    return query
        ? (/^[?#]/.test(query) ? query.slice(1) : query)
            .split('&')
            .reduce((params, param) => {
                    let [key, value] = param.split('=');
                    params[key] = value ? decodeURIComponent(value.replace(/\+/g, ' ')) : '';
                    return params;
                }, {}
            )
        : {}
};

export default (pathParts, queryParams, windowObj) => {
	windowObj = windowObj || window;
	let baseURL = Config.get('baseURL');
	let path;

	// remove query string from baseURL
	let qsPos = baseURL.indexOf("?");
	if (qsPos !== -1) {
        let qsString = baseURL.slice(qsPos);
        let qsObj = getQueryStringParams(qsString);
        baseURL = baseURL.slice(0, qsPos);
        queryParams = {...queryParams, ...qsObj};
	}

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

	if(queryParams) {
		const serialised = serialise(queryParams);
		if(serialised.length) {
			path += `/?${serialise(queryParams)}`;	
		}
		
	}

	baseURL = baseURL.replace(/\/$/,'').replace(/^\//, '');
	path = path.replace(/\/$/,'').replace(/^\//, '');

	return [baseURL, path].join('/');
};