var request = require('superagent');

var API_URL = 'presentations/api/v1';
var TIMEOUT = 10000;

var _pendingRequests = {};

var _errorHandlers = {};

function abortPendingRequests(key) {
    if (_pendingRequests[key]) {
        _pendingRequests[key]._callback = function(){};
        _pendingRequests[key].abort();
        _pendingRequests[key] = null;
    }
}


function makeUrl(part) {
    return API_URL + part;
}


// a get request with an authtoken param
function get(url, data) {     
    return request
        .get(url)
        .query(data || {})
        .timeout(TIMEOUT);       
}

function post(url, data) {
    return request        
        .post(url)
        .type('form')
        .send(data || {})
        .timeout(TIMEOUT);
}

function put(url, data) {
    return request        
        .put(url)
        .type('form')
        .send(data || {})
        .timeout(TIMEOUT);
}


function invokeError(error, res) {
    var func = _errorHandlers['error_'+error] || _errorHandlers['error_ERROR'];
    if(typeof func === 'function') {
       return func(res);
    }
}

function finalise(request, callback) {
    return request.end(function (err, res) {   
        if (err && err.timeout === TIMEOUT) {
            return invokeError('TIMEOUT', res);
        } 
        if(!res.ok) {
            return invokeError(res.status, res);
        }

        if(typeof callback === 'function') {
            return callback(res);
        }
    });
}


var Api = {

    registerErrorHandler: function (error, handler) {
        _errorHandlers['error_'+error] = handler;
    },

    
    getSummitData: function (id, callback) {
        var url = makeUrl('/summit/'+id);
        var key = 'SUMMIT_DATA';
        abortPendingRequests(key);

        return _pendingRequests[key] = finalise(
            get(
                url
            ), 
            callback
        );
    },


    getPresentations: function (data, callback) {
        var url = API_URL;
        var key = 'PRESENTATIONS';
        abortPendingRequests(key);

        return _pendingRequests[key] = finalise(
            get(
                url, 
                data
            )
            , callback
        );  
    },


    getPresentation: function (id, callback) {
        var url = makeUrl('/presentation/'+id);
        var key = 'PRESENTATION';
        abortPendingRequests(key);

        return _pendingRequests[key] = finalise(
            get(
                url
            ), 
            callback
        );  
    },


    setPresentationVote: function (presID, v) {
        var url = makeUrl('/presentation/'+presID+'/vote');
        var key = 'VOTE';
        abortPendingRequests(key);

        return _pendingRequests[key] = finalise(
            post(
                url,
                { vote: v}
            )
        );          
    }

};

module.exports = Api;