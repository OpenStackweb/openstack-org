// Requirements and globals
var api            = riot.observable();
var api_base_url   = 'api/v1/software';
var query_template = 'releases/@RELEASE_ID/components';

// events

api.load_components_by_release = function(release_id, term)
{

    var url = api_base_url+'/'+ query_template.replace('@RELEASE_ID', release_id);

    if(term !== null && term !== '' && term !== undefined )
        url += '?term='+term;

    return $.get(url,function (resp) {
        api.trigger('loaded-components-by-release', resp)
    });

}

module.exports = api;